<?php

namespace App\Services\Parent;

use App\Models\Assignment;
use App\Models\Order;
use App\Models\StudentReport;
use App\Models\User;
use Illuminate\Support\Collection;

class ParentDashboardService
{
    public function linkedStudents(User $parent): Collection
    {
        return $parent->linkedStudents()
            ->with(['academicYear'])
            ->orderBy('name')
            ->get();
    }

    public function overviewStats(User $parent): array
    {
        $students = $this->linkedStudents($parent);
        $studentIds = $students->pluck('id');

        $avgProgress = 0;
        if ($studentIds->isNotEmpty()) {
            $avgProgress = (int) round(
                \App\Models\StudentCourseEnrollment::query()
                    ->whereIn('user_id', $studentIds)
                    ->whereIn('status', ['active', 'completed'])
                    ->avg('progress') ?? 0
            );
        }

        return [
            'children_count' => $students->count(),
            'avg_progress' => $avgProgress,
            'active_courses' => $studentIds->isEmpty() ? 0 : \App\Models\StudentCourseEnrollment::query()
                ->whereIn('user_id', $studentIds)
                ->where('status', 'active')
                ->count(),
            'pending_orders' => $studentIds->isEmpty() ? 0 : Order::query()
                ->whereIn('user_id', $studentIds)
                ->where('status', 'pending')
                ->count(),
            'reports_count' => $studentIds->isEmpty() ? 0 : StudentReport::query()
                ->whereIn('student_id', $studentIds)
                ->count(),
        ];
    }

    public function studentSnapshot(User $student): array
    {
        $enrollments = $student->courseEnrollments()
            ->whereIn('status', ['active', 'completed'])
            ->with('course')
            ->get();

        $progress = $enrollments->isEmpty()
            ? 0
            : (int) round($enrollments->avg('progress') ?? 0);

        $upcomingAssignments = Assignment::query()
            ->where('status', 'published')
            ->where(function ($q) use ($enrollments) {
                $ids = $enrollments->pluck('advanced_course_id')->filter();
                $q->whereIn('advanced_course_id', $ids)->orWhereIn('course_id', $ids);
            })
            ->where(function ($q) {
                $q->whereNull('due_date')->orWhere('due_date', '>=', now()->startOfDay());
            })
            ->orderBy('due_date')
            ->limit(5)
            ->get();

        $recentOrders = Order::query()
            ->where('user_id', $student->id)
            ->latest()
            ->limit(5)
            ->with('course')
            ->get();

        $reports = StudentReport::query()
            ->where('student_id', $student->id)
            ->latest()
            ->limit(5)
            ->get();

        return compact('enrollments', 'progress', 'upcomingAssignments', 'recentOrders', 'reports');
    }
}
