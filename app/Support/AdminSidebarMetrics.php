<?php

namespace App\Support;

use App\Models\Certificate;
use App\Models\ContactMessage;
use App\Models\EmployeeTask;
use App\Models\InstructorProfile;
use App\Models\InstructorRequest;
use App\Models\LeaveRequest;
use App\Models\Notification;
use App\Models\Order;
use App\Models\Subscription;
use App\Models\Task;
use App\Models\User;
use App\Models\WithdrawalRequest;
use Illuminate\Support\Facades\Cache;

/**
 * أرقام شارات السايدبار — تُحسب مرة كل دقيقة لتسريع تنقل لوحة الإدارة.
 */
final class AdminSidebarMetrics
{
    public static function cached(int $userId): array
    {
        return Cache::remember('admin_sidebar_metrics:v1:'.$userId, 60, fn () => self::compute($userId));
    }

    public static function forget(int $userId): void
    {
        Cache::forget('admin_sidebar_metrics:v1:'.$userId);
    }

    private static function compute(int $userId): array
    {
        $safeCount = static function (callable $callback): int {
            try {
                return (int) $callback();
            } catch (\Throwable) {
                return 0;
            }
        };

        return [
            'pending_instructor_applications' => $safeCount(fn () => InstructorProfile::query()
                ->where('status', InstructorProfile::STATUS_PENDING_REVIEW)
                ->whereNotNull('submitted_at')
                ->count()),
            'sidebar_inbox_unread' => $safeCount(fn () => Notification::query()
                ->where('user_id', $userId)
                ->unread()
                ->valid()
                ->count()),
            'sidebar_contact_unread' => $safeCount(fn () => ContactMessage::query()
                ->whereNull('read_at')
                ->count()),
            'students_count' => $safeCount(fn () => User::query()->where('role', 'student')->count()),
            'active_subs_count' => $safeCount(fn () => Subscription::query()->where('status', 'active')->count()),
            'pending_orders' => $safeCount(fn () => Order::query()->where('status', 'pending')->count()),
            'pending_tasks' => $safeCount(fn () => EmployeeTask::query()->where('status', 'pending')->count()),
            'pending_leaves' => $safeCount(fn () => LeaveRequest::query()->where('status', 'pending')->count()),
            'pending_withdrawals' => $safeCount(fn () => WithdrawalRequest::query()->where('status', 'pending')->count()),
            'pending_instructor_tasks' => $safeCount(fn () => Task::query()
                ->where('status', 'pending')
                ->whereHas('user', fn ($q) => $q->whereIn('role', ['instructor', 'teacher']))
                ->count()),
            'pending_instructor_requests' => $safeCount(fn () => InstructorRequest::query()->where('status', 'pending')->count()),
            'total_certificates' => $safeCount(fn () => Certificate::query()->count()),
            'pending_certificates' => $safeCount(fn () => Certificate::query()
                ->where(function ($q) {
                    $q->where('status', 'pending')->orWhere('is_verified', false);
                })
                ->count()),
        ];
    }
}
