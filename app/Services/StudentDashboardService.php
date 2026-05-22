<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\ExamAttempt;
use App\Models\Lecture;
use App\Models\LessonProgress;
use App\Models\User;
use App\Models\UserAchievement;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class StudentDashboardService
{
    /**
     * @param  \Illuminate\Support\Collection<int, \App\Models\Assignment>  $upcomingAssignments
     * @param  \Illuminate\Support\Collection<int, \App\Models\Exam>  $upcomingExams
     */
    public function upcomingCalendarItems(User $user, Collection $upcomingAssignments, Collection $upcomingExams, int $limit = 5): Collection
    {
        $items = collect();

        foreach ($upcomingAssignments as $assignment) {
            $due = $assignment->due_date;
            if (! $due) {
                continue;
            }
            $course = $assignment->course ?? optional($assignment->lecture)->course;
            $items->push([
                'at' => Carbon::parse($due),
                'title' => $assignment->title,
                'subtitle' => $course?->title ?? '',
                'type' => 'assignment',
                'type_label' => __('student.assignments'),
                'icon' => 'fa-tasks',
                'color' => '#F59E0B',
                'url' => route('student.assignments.show', $assignment),
            ]);
        }

        foreach ($upcomingExams as $exam) {
            $start = $exam->start_time ?? ($exam->start_date ? Carbon::parse($exam->start_date)->startOfDay() : null);
            if (! $start) {
                continue;
            }
            $items->push([
                'at' => Carbon::parse($start),
                'title' => $exam->title,
                'subtitle' => $exam->course?->title ?? '',
                'type' => 'exam',
                'type_label' => __('student.exams'),
                'icon' => 'fa-clipboard-check',
                'color' => '#EF4444',
                'url' => route('student.exams.show', $exam),
            ]);
        }

        $lectures = Lecture::query()
            ->whereHas('course', function ($q) use ($user) {
                $q->whereHas('enrollments', function ($q2) use ($user) {
                    $q2->where('user_id', $user->id)->where('status', 'active');
                });
            })
            ->where('status', 'scheduled')
            ->where('scheduled_at', '>=', now())
            ->orderBy('scheduled_at')
            ->with('course')
            ->limit(5)
            ->get();

        foreach ($lectures as $lecture) {
            $items->push([
                'at' => $lecture->scheduled_at,
                'title' => $lecture->title,
                'subtitle' => $lecture->course?->title ?? '',
                'type' => 'lecture',
                'type_label' => __('student.dashboard_event_lecture'),
                'icon' => 'fa-video',
                'color' => '#3B82F6',
                'url' => $lecture->course_id ? route('my-courses.learn', $lecture->course_id) : null,
            ]);
        }

        return $items
            ->filter(fn (array $row) => $row['at'] instanceof Carbon && $row['at']->gte(now()->startOfDay()->subDay()))
            ->sortBy('at')
            ->take($limit)
            ->values();
    }

    /**
     * @param  \Illuminate\Support\Collection<int, ExamAttempt>  $recentExamAttempts
     * @param  \Illuminate\Support\Collection<int, Certificate>  $recentCertificates
     */
    public function recentActivity(User $user, Collection $recentExamAttempts, Collection $recentCertificates, int $limit = 6): Collection
    {
        $items = collect();

        foreach ($recentExamAttempts as $attempt) {
            $exam = $attempt->exam;
            $at = $attempt->submitted_at ?? $attempt->created_at;
            if (! $at) {
                continue;
            }
            $items->push([
                'at' => Carbon::parse($at),
                'icon' => 'fa-award',
                'tone' => 'emerald',
                'text' => __('student.dashboard_activity_exam', ['title' => $exam?->title ?? __('student.exam_deleted')]),
                'meta' => ! is_null($attempt->percentage)
                    ? number_format((float) $attempt->percentage, 0).'%'
                    : null,
                'url' => $exam ? route('student.exams.result', [$exam, $attempt]) : null,
            ]);
        }

        foreach ($recentCertificates as $certificate) {
            $at = $certificate->issued_at ?? $certificate->created_at;
            if (! $at) {
                continue;
            }
            $items->push([
                'at' => Carbon::parse($at),
                'icon' => 'fa-certificate',
                'tone' => 'amber',
                'text' => __('student.dashboard_activity_certificate', [
                    'title' => $certificate->title ?? $certificate->course_name ?? __('student.certificate_untitled'),
                ]),
                'meta' => null,
                'url' => route('student.certificates.show', $certificate),
            ]);
        }

        $recentLessons = LessonProgress::query()
            ->where('user_id', $user->id)
            ->where('is_completed', true)
            ->whereNotNull('completed_at')
            ->with(['lesson.course'])
            ->orderByDesc('completed_at')
            ->limit(5)
            ->get();

        foreach ($recentLessons as $progress) {
            $lesson = $progress->lesson;
            if (! $lesson) {
                continue;
            }
            $items->push([
                'at' => Carbon::parse($progress->completed_at),
                'icon' => 'fa-circle-check',
                'tone' => 'violet',
                'text' => __('student.dashboard_activity_lesson', [
                    'lesson' => $lesson->title,
                    'course' => $lesson->course?->title ?? '',
                ]),
                'meta' => null,
                'url' => $lesson->advanced_course_id
                    ? route('my-courses.show', $lesson->advanced_course_id)
                    : null,
            ]);
        }

        return $items
            ->sortByDesc('at')
            ->take($limit)
            ->values();
    }

    public function achievementsCount(User $user): int
    {
        return (int) UserAchievement::query()->where('user_id', $user->id)->count();
    }

    public function humanEventTime(Carbon $at): string
    {
        if ($at->isToday()) {
            return __('student.dashboard_today_at', ['time' => $at->translatedFormat('H:i')]);
        }
        if ($at->isTomorrow()) {
            return __('student.dashboard_tomorrow_at', ['time' => $at->translatedFormat('H:i')]);
        }
        if ($at->lte(now()->addDays(7))) {
            return $at->diffForHumans();
        }

        return $at->translatedFormat('d M · H:i');
    }

    public function humanActivityTime(Carbon $at): string
    {
        return $at->diffForHumans();
    }
}
