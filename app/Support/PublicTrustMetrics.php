<?php

namespace App\Support;

use App\Models\Certificate;
use App\Models\CourseReview;
use App\Models\StudentCourseEnrollment;
use App\Models\User;

/**
 * أرقام الثقة للصفحات العامة — تُعرض فقط عند وجود قيمة حقيقية (> 0).
 */
final class PublicTrustMetrics
{
    /** @return array{students: int, courses: int, instructors: int, certificates: int, completed: int, reviews_count: int, avg_rating: ?float} */
    public static function payload(): array
    {
        $reviewsQuery = CourseReview::query()->where('is_approved', true);
        $reviewsCount = (int) $reviewsQuery->count();

        return [
            'students' => (int) User::query()->where('role', 'student')->where('is_active', true)->count(),
            'courses' => (int) PublicCourseCatalog::publiclyListableQuery()->count(),
            'instructors' => (int) PublicInstructorCatalog::rankForPublic()->count(),
            'certificates' => (int) Certificate::query()
                ->where(function ($q) {
                    $q->where('status', 'issued')->orWhere('is_verified', true);
                })
                ->count(),
            'completed' => (int) StudentCourseEnrollment::query()->where('status', 'completed')->count(),
            'reviews_count' => $reviewsCount,
            'avg_rating' => $reviewsCount > 0
                ? round((float) ($reviewsQuery->avg('rating') ?: 0), 1)
                : null,
        ];
    }

    /** @param  array<string, mixed>  $stats */
    public static function hasTrustStats(array $stats): bool
    {
        return ((int) ($stats['reviews_count'] ?? 0)) > 0
            || ((int) ($stats['students'] ?? 0)) > 0
            || ((int) ($stats['certificates'] ?? 0)) > 0
            || ((int) ($stats['completed'] ?? 0)) > 0
            || ((int) ($stats['instructors'] ?? 0)) > 0;
    }
}
