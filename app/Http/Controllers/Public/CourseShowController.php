<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\AdvancedCourse;
use App\Models\Assignment;
use App\Models\AdvancedExam;
use App\Models\CourseLesson;
use App\Models\CourseReview;
use App\Models\CurriculumItem;
use App\Models\InstructorProfile;
use App\Models\Lecture;
use App\Models\StudentCourseEnrollment;
use App\Support\PublicCourseCatalog;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CourseShowController extends Controller
{
    public function show(int $id): View
    {
        $isEnrolled = false;
        $enrollmentProgress = null;

        if (auth()->check()) {
            $enrollment = StudentCourseEnrollment::query()
                ->where('user_id', auth()->id())
                ->where('advanced_course_id', $id)
                ->where('status', 'active')
                ->first();

            $isEnrolled = $enrollment !== null;
            $enrollmentProgress = $enrollment?->progress;
        }

        $course = PublicCourseCatalog::publiclyVisibleQuery()
            ->where('id', $id)
            ->with([
                'academicSubject',
                'academicYear',
                'instructor:id,name,profile_image',
                'courseCategory',
            ])
            ->withCount(['lessons', 'exams', 'assignments', 'lectures'])
            ->first();

        if (! $course) {
            if ($isEnrolled) {
                $course = AdvancedCourse::query()
                    ->where('id', $id)
                    ->where('is_active', true)
                    ->with([
                        'academicSubject',
                        'academicYear',
                        'instructor:id,name,profile_image',
                        'courseCategory',
                    ])
                    ->withCount(['lessons', 'exams', 'assignments', 'lectures'])
                    ->firstOrFail();
            } else {
                throw new NotFoundHttpException;
            }
        }

        $canEnrollPublicly = PublicCourseCatalog::isPubliclyVisible($course);

        $sections = $course->sections()
            ->where('is_active', true)
            ->whereNull('parent_id')
            ->orderBy('order')
            ->with([
                'activeItems.item',
                'children' => fn ($q) => $q->where('is_active', true)->orderBy('order')->with(['activeItems.item']),
            ])
            ->get();

        $curriculum = $this->buildCurriculum($course, $sections);
        $curriculumStats = $this->curriculumStats($curriculum);

        $instructorProfile = null;
        $instructorStats = ['courses' => 0, 'students' => 0, 'rating' => null];

        if ($course->instructor_id) {
            $instructorProfile = InstructorProfile::query()
                ->where('user_id', $course->instructor_id)
                ->where('status', InstructorProfile::STATUS_APPROVED)
                ->first();

            $instructorStats['courses'] = PublicCourseCatalog::publiclyVisibleQuery()
                ->where('instructor_id', $course->instructor_id)
                ->count();

            $instructorStats['students'] = (int) PublicCourseCatalog::publiclyVisibleQuery()
                ->where('instructor_id', $course->instructor_id)
                ->sum('students_count');

            $avgRating = PublicCourseCatalog::publiclyVisibleQuery()
                ->where('instructor_id', $course->instructor_id)
                ->where('rating', '>', 0)
                ->where('reviews_count', '>', 0)
                ->avg('rating');

            if ($avgRating) {
                $instructorStats['rating'] = round((float) $avgRating, 1);
            }
        }

        $reviews = CourseReview::query()
            ->where('course_id', $course->id)
            ->where('is_approved', true)
            ->with('user:id,name,profile_image')
            ->latest()
            ->limit(12)
            ->get();

        $ratingBreakdown = CourseReview::query()
            ->where('course_id', $course->id)
            ->where('is_approved', true)
            ->selectRaw('rating, COUNT(*) as total')
            ->groupBy('rating')
            ->pluck('total', 'rating')
            ->all();

        $relatedCourses = PublicCourseCatalog::publiclyVisibleQuery()
            ->where('id', '!=', $course->id)
            ->where(function ($query) use ($course) {
                if ($course->course_category_id) {
                    $query->where('course_category_id', $course->course_category_id);
                }
                $query->orWhere('academic_subject_id', $course->academic_subject_id)
                    ->orWhere('is_featured', true);
            })
            ->with(['instructor:id,name', 'courseCategory'])
            ->withCount('lessons')
            ->limit(3)
            ->get();

        $savedCourseIds = PublicCourseCatalog::savedCourseIdsFor(auth()->user());
        $levelLabel = PublicCourseCatalog::levelLabel($course->level);

        return view('course-show', compact(
            'course',
            'relatedCourses',
            'isEnrolled',
            'enrollmentProgress',
            'curriculum',
            'curriculumStats',
            'instructorProfile',
            'instructorStats',
            'reviews',
            'ratingBreakdown',
            'savedCourseIds',
            'levelLabel',
            'canEnrollPublicly',
        ));
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function buildCurriculum(AdvancedCourse $course, $sections): array
    {
        if ($sections->isNotEmpty()) {
            return $sections->map(fn ($section) => $this->mapSection($section))->values()->all();
        }

        $lessons = $course->lessons()
            ->where('is_active', true)
            ->orderBy('order')
            ->get();

        if ($lessons->isEmpty()) {
            return [];
        }

        return [[
            'id' => 'lessons',
            'title' => 'محتوى الدورة',
            'description' => null,
            'items' => $lessons->map(fn (CourseLesson $lesson) => [
                'type' => 'lesson',
                'type_label' => 'درس',
                'icon' => 'fa-play-circle',
                'title' => $lesson->title,
                'duration' => (int) ($lesson->duration_minutes ?? 0),
            ])->values()->all(),
            'children' => [],
        ]];
    }

    /**
     * @return array<string, mixed>
     */
    private function mapSection($section): array
    {
        $items = $section->activeItems->map(function (CurriculumItem $ci) {
            $item = $ci->item;
            if (! $item) {
                return null;
            }

            return $this->mapCurriculumItem($item);
        })->filter()->values()->all();

        return [
            'id' => $section->id,
            'title' => $section->title,
            'description' => $section->description,
            'items' => $items,
            'children' => $section->children
                ? $section->children->map(fn ($child) => $this->mapSection($child))->values()->all()
                : [],
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    private function mapCurriculumItem(object $item): ?array
    {
        if ($item instanceof Lecture) {
            return [
                'type' => 'lecture',
                'type_label' => 'محاضرة',
                'icon' => 'fa-video',
                'title' => $item->title ?? 'محاضرة',
                'duration' => (int) ($item->duration_minutes ?? 0),
            ];
        }

        if ($item instanceof CourseLesson) {
            return [
                'type' => 'lesson',
                'type_label' => 'درس',
                'icon' => 'fa-play-circle',
                'title' => $item->title ?? 'درس',
                'duration' => (int) ($item->duration_minutes ?? 0),
            ];
        }

        if ($item instanceof Assignment) {
            return [
                'type' => 'assignment',
                'type_label' => 'واجب',
                'icon' => 'fa-file-pen',
                'title' => $item->title ?? 'واجب',
                'duration' => 0,
            ];
        }

        if ($item instanceof AdvancedExam) {
            return [
                'type' => 'exam',
                'type_label' => 'اختبار',
                'icon' => 'fa-clipboard-check',
                'title' => $item->title ?? 'اختبار',
                'duration' => (int) ($item->duration_minutes ?? 0),
            ];
        }

        return null;
    }

    /**
     * @param  list<array<string, mixed>>  $curriculum
     * @return array{modules: int, lessons: int, quizzes: int, assignments: int}
     */
    private function curriculumStats(array $curriculum): array
    {
        $stats = ['modules' => count($curriculum), 'lessons' => 0, 'quizzes' => 0, 'assignments' => 0];

        $walk = function (array $sections) use (&$walk, &$stats) {
            foreach ($sections as $section) {
                foreach ($section['items'] ?? [] as $item) {
                    match ($item['type'] ?? '') {
                        'lecture', 'lesson' => $stats['lessons']++,
                        'exam' => $stats['quizzes']++,
                        'assignment' => $stats['assignments']++,
                        default => null,
                    };
                }
                if (! empty($section['children'])) {
                    $stats['modules'] += count($section['children']);
                    $walk($section['children']);
                }
            }
        };

        $walk($curriculum);

        return $stats;
    }
}
