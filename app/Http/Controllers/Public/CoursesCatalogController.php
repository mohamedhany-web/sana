<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\AcademicSubject;
use App\Models\AcademicYear;
use App\Models\AdvancedCourse;
use App\Models\CourseCategory;
use App\Models\StudentCourseEnrollment;
use App\Models\StudentSavedCourse;
use App\Support\PublicCourseCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class CoursesCatalogController extends Controller
{
    public function index(Request $request): View
    {
        $data = $this->buildCatalog(null);
        $subjectId = (int) $request->query('subject', 0);
        $categoryId = (int) $request->query('category', 0);

        if ($subjectId > 0) {
            $data['initialSubjectId'] = (string) $subjectId;
        }

        if ($categoryId > 0) {
            $data['initialCategoryId'] = (string) $categoryId;
        }

        return view('courses', array_merge($data, ['savedOnly' => false]));
    }

    public function saved(): View|RedirectResponse
    {
        if (! auth()->check()) {
            return redirect()->route('login', ['redirect' => route('public.courses.saved')]);
        }

        $ids = StudentSavedCourse::query()
            ->where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->pluck('advanced_course_id');

        return view('courses', array_merge($this->buildCatalog($ids), ['savedOnly' => true]));
    }

    /**
     * @return array<string, mixed>
     */
    private function buildCatalog(?Collection $onlyIds): array
    {
        $coursesQuery = PublicCourseCatalog::publiclyListableQuery();

        if ($onlyIds !== null) {
            if ($onlyIds->isEmpty()) {
                $coursesCollection = collect();
            } else {
                $coursesCollection = $coursesQuery
                    ->whereIn('id', $onlyIds)
                    ->with(['academicSubject', 'academicYear', 'instructor:id,name,profile_image', 'courseCategory'])
                    ->withCount('lectures')
                    ->orderByRaw('FIELD(id, '.implode(',', $onlyIds->map(fn ($id) => (int) $id)->all()).')')
                    ->get();
            }
        } else {
            $coursesCollection = $coursesQuery
                ->with(['academicSubject', 'academicYear', 'instructor:id,name,profile_image', 'courseCategory'])
                ->withCount('lectures')
                ->orderByDesc('is_featured')
                ->orderByDesc('created_at')
                ->get()
                ->filter(fn (AdvancedCourse $course) => ! PublicCourseCatalog::isPlaceholderTitle($course->title))
                ->values();
        }

        $enrollmentProgress = $this->enrollmentProgressForUser(auth()->user());
        $courses = PublicCourseCatalog::mapForCards($coursesCollection, $enrollmentProgress);
        $savedCourseIds = PublicCourseCatalog::savedCourseIdsFor(auth()->user());

        $courseFilterCategories = CourseCategory::active()->ordered()->get(['id', 'name']);
        $academicYears = AcademicYear::active()->ordered()->get(['id', 'name']);
        $academicSubjects = AcademicSubject::active()->ordered()->get(['id', 'name']);

        $catalogCategories = $this->buildCategoryCards($courseFilterCategories, $courses);
        $instructors = $this->buildInstructorFilters($courses);

        return compact(
            'courses',
            'savedCourseIds',
            'courseFilterCategories',
            'academicYears',
            'academicSubjects',
            'catalogCategories',
            'instructors',
        ) + [
            'initialCategoryId' => '',
            'initialSubjectId' => '',
            'initialYearId' => '',
            'catalogIsEmpty' => count($courses) === 0,
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $courses
     * @return list<array<string, mixed>>
     */
    private function buildCategoryCards(Collection $categories, array $courses): array
    {
        $visuals = [
            ['emoji' => '🔢', 'icon' => 'fa-calculator', 'bg' => 'linear-gradient(145deg,#EDE9FE,#C4B5FD)'],
            ['emoji' => '🔬', 'icon' => 'fa-flask', 'bg' => 'linear-gradient(145deg,#D1FAE5,#6EE7B7)'],
            ['emoji' => '💻', 'icon' => 'fa-code', 'bg' => 'linear-gradient(145deg,#DBEAFE,#93C5FD)'],
            ['emoji' => '🌍', 'icon' => 'fa-language', 'bg' => 'linear-gradient(145deg,#FFEDD5,#FDBA74)'],
            ['emoji' => '🤖', 'icon' => 'fa-robot', 'bg' => 'linear-gradient(145deg,#F3E8FF,#D8B4FE)'],
            ['emoji' => '📊', 'icon' => 'fa-chart-line', 'bg' => 'linear-gradient(145deg,#CCFBF1,#5EEAD4)'],
            ['emoji' => '📚', 'icon' => 'fa-book-open', 'bg' => 'linear-gradient(145deg,#FCE7F3,#F9A8D4)'],
        ];

        if ($categories->isEmpty()) {
            return [];
        }

        return $categories->values()->map(function ($cat, $i) use ($courses, $visuals) {
            $v = $visuals[$i % count($visuals)];
            $count = count(array_filter($courses, fn ($c) => (int) ($c['course_category_id'] ?? 0) === (int) $cat->id));

            return [
                'id' => $cat->id,
                'name' => $cat->name,
                'count' => $count,
                'emoji' => $v['emoji'],
                'icon' => $v['icon'],
                'bg' => $v['bg'],
            ];
        })->filter(fn (array $cat) => (int) ($cat['count'] ?? 0) > 0)->values()->all();
    }

    /**
     * @param  array<int, array<string, mixed>>  $courses
     * @return list<array<string, mixed>>
     */
    private function buildInstructorFilters(array $courses): array
    {
        $map = [];
        foreach ($courses as $course) {
            $inst = $course['instructor'] ?? null;
            if (! $inst || empty($inst['id'])) {
                continue;
            }
            $map[(int) $inst['id']] = [
                'id' => (int) $inst['id'],
                'name' => $inst['name'],
            ];
        }

        return array_values($map);
    }

    /** @return array<int, float> */
    private function enrollmentProgressForUser(?\App\Models\User $user): array
    {
        if (! $user) {
            return [];
        }

        return StudentCourseEnrollment::query()
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->pluck('progress', 'advanced_course_id')
            ->map(fn ($p) => (float) $p)
            ->all();
    }
}
