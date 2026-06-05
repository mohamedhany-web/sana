<?php

namespace App\Services;

use App\Models\Subscription;
use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class StudentControlOverviewService
{
    public static function activeStudentSubscriptionQuery(): Builder
    {
        return Subscription::query()
            ->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', now());
            })
            ->whereHas('user', fn ($uq) => $uq->where('role', 'student'));
    }

    /** @return array<string, mixed> */
    public static function dashboardStats(): array
    {
        $activeQuery = self::activeStudentSubscriptionQuery();

        $activeSubs = (clone $activeQuery)->count();
        $activeStudentIds = (clone $activeQuery)->distinct('user_id')->count('user_id');
        $totalStudents = User::where('role', 'student')->count();
        $withoutSub = User::where('role', 'student')
            ->whereDoesntHave('subscriptions', function ($q) {
                $q->where('status', 'active')
                    ->where(function ($d) {
                        $d->whereNull('end_date')->orWhere('end_date', '>=', now());
                    });
            })
            ->count();

        $withSupport = (clone $activeQuery)->where(function ($q) {
            $q->whereJsonContains('features', 'support')
                ->orWhereJsonContains('features', 'direct_support');
        })->distinct('user_id')->count('user_id');

        $openTickets = SupportTicket::query()
            ->whereIn('status', ['open', 'in_progress'])
            ->whereHas('user', fn ($uq) => $uq->where('role', 'student'))
            ->count();

        return [
            'total_students' => $totalStudents,
            'active_subscriptions' => $activeSubs,
            'active_students_with_subscription' => $activeStudentIds,
            'students_without_subscription' => $withoutSub,
            'students_with_support_feature' => $withSupport,
            'open_support_tickets' => $openTickets,
        ];
    }

    /** @return Collection<int, array<string, mixed>> */
    public static function planCards(): Collection
    {
        $plans = StudentSubscriptionPlansService::getPlans();
        $activeQuery = self::activeStudentSubscriptionQuery();

        return collect(StudentSubscriptionPlansService::planKeys())->map(function (string $planKey) use ($plans, $activeQuery) {
            $plan = $plans[$planKey] ?? [];
            $hours = (int) ($plan['limits']['tutor_lesson_hours'] ?? 0);

            return [
                'key' => $planKey,
                'label' => (string) ($plan['label'] ?? $planKey),
                'price' => (float) ($plan['price'] ?? 0),
                'hours' => $hours,
                'billing_cycle' => (string) ($plan['billing_cycle'] ?? 'monthly'),
                'card_subtitle' => (string) ($plan['card_subtitle'] ?? ''),
                'subscriptions_count' => (clone $activeQuery)->where('teacher_plan_key', $planKey)->count(),
                'students_count' => (clone $activeQuery)->where('teacher_plan_key', $planKey)->distinct('user_id')->count('user_id'),
            ];
        })->sortByDesc('students_count')->values();
    }

    public static function customPlanStats(): array
    {
        $planKeys = StudentSubscriptionPlansService::planKeys();
        $activeQuery = self::activeStudentSubscriptionQuery();

        $customQuery = (clone $activeQuery)->where(function ($q) use ($planKeys) {
            $q->whereNull('teacher_plan_key')
                ->orWhereNotIn('teacher_plan_key', $planKeys);
        });

        return [
            'subscriptions_count' => (clone $customQuery)->count(),
            'students_count' => (clone $customQuery)->distinct('user_id')->count('user_id'),
        ];
    }

    /** @return Collection<int, array<string, mixed>> */
    public static function optionalCapabilityCards(): Collection
    {
        $capabilities = config('student_subscription_capabilities', []);
        $activeQuery = self::activeStudentSubscriptionQuery();

        return collect($capabilities)->map(function ($cfg, $key) use ($activeQuery) {
            if ($key === 'tutor_lessons') {
                $studentsCount = (clone $activeQuery)
                    ->get(['user_id', 'feature_limits'])
                    ->filter(fn ($s) => (int) (is_array($s->feature_limits) ? ($s->feature_limits['tutor_lesson_hours'] ?? 0) : 0) > 0)
                    ->pluck('user_id')
                    ->unique()
                    ->count();

                return [
                    'key' => $key,
                    'label' => $cfg['label'],
                    'icon' => $cfg['icon'],
                    'icon_bg' => $cfg['icon_bg'],
                    'icon_text' => $cfg['icon_text'],
                    'description' => $cfg['description'],
                    'students_count' => $studentsCount,
                    'filter_type' => 'capability',
                ];
            }

            if ($key === 'support') {
                $featureKeys = $cfg['feature_keys'] ?? ['support', 'direct_support'];
                $q = clone $activeQuery;
                $q->where(function ($inner) use ($featureKeys) {
                    foreach ($featureKeys as $fk) {
                        $inner->orWhereJsonContains('features', $fk);
                    }
                });

                return [
                    'key' => 'support',
                    'label' => $cfg['label'],
                    'icon' => $cfg['icon'],
                    'icon_bg' => $cfg['icon_bg'],
                    'icon_text' => $cfg['icon_text'],
                    'description' => $cfg['description'],
                    'students_count' => $q->distinct('user_id')->count('user_id'),
                    'filter_type' => 'feature',
                ];
            }

            return null;
        })->filter()->values();
    }

    public static function subscriptionHasFeature(Subscription $subscription, string $featureKey): bool
    {
        $features = $subscription->features ?? [];
        if (! is_array($features)) {
            return false;
        }
        if (! array_is_list($features)) {
            $features = array_keys(array_filter($features, fn ($v) => (bool) $v));
        }

        return in_array($featureKey, $features, true)
            || in_array($featureKey, Subscription::normalizeFeatureKeys($features), true);
    }
}
