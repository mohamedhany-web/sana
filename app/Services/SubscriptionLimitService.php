<?php

namespace App\Services;

use App\Models\ClassroomMeeting;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SubscriptionLimitService
{
    public static function defaultPlans(): array
    {
        return [
            'teacher_starter' => [
                'limits' => [
                    'classroom_meetings_per_month' => 0,
                    'classroom_max_participants' => 1,
                    'classroom_default_duration_minutes' => 60,
                    'classroom_max_duration_minutes' => 60,
                    'personal_marketing_profile_sections' => 5,
                    'personal_marketing_priority_score' => 40,
                    'personal_marketing_monthly_featured_days' => 0,
                ],
            ],
            'teacher_pro' => [
                'limits' => [
                    'classroom_meetings_per_month' => 9999,
                    'classroom_max_participants' => 150,
                    'classroom_default_duration_minutes' => 90,
                    'classroom_max_duration_minutes' => 480,
                    'personal_marketing_profile_sections' => 12,
                    'personal_marketing_priority_score' => 90,
                    'personal_marketing_monthly_featured_days' => 12,
                ],
            ],
        ];
    }

    public static function teacherPlansFromSettings(): array
    {
        return Cache::remember('teacher_features_settings_limits', 300, function () {
            $defaults = self::defaultPlans();
            $fromSettings = InstructorSubscriptionPlansService::getPlans();

            $plans = array_merge($defaults, $fromSettings);
            foreach ($defaults as $planKey => $planDefaults) {
                $current = $plans[$planKey]['limits'] ?? [];
                $plans[$planKey]['limits'] = array_merge($planDefaults['limits'], is_array($current) ? $current : []);
            }

            return $plans;
        });
    }

    /**
     * دمج الحدود الافتراضية في الكود مع قيم إعدادات باقة محددة من لوحة المعلمين.
     *
     * @param  array<string, mixed>  $teacherPlansFull  ناتج InstructorSubscriptionPlansService::getPlans()
     */
    public static function limitsArrayForPlanKey(array $teacherPlansFull, string $planKey): array
    {
        if (! in_array($planKey, ['teacher_starter', 'teacher_pro'], true)) {
            $planKey = 'teacher_starter';
        }

        $defaults = self::defaultPlans()[$planKey]['limits'] ?? self::defaultPlans()['teacher_starter']['limits'];
        $fromPlan = is_array(($teacherPlansFull[$planKey]['limits'] ?? null)) ? $teacherPlansFull[$planKey]['limits'] : [];

        return array_merge($defaults, $fromPlan);
    }

    /**
     * مفاتيح الحدود القابلة للتخزين على الاشتراك (تطابق إعدادات باقات المعلمين).
     *
     * @return list<string>
     */
    public static function limitSchemaKeys(): array
    {
        return [
            'classroom_meetings_per_month',
            'classroom_max_participants',
            'classroom_default_duration_minutes',
            'classroom_max_duration_minutes',
            'personal_marketing_profile_sections',
            'personal_marketing_priority_score',
            'personal_marketing_monthly_featured_days',
            'tutor_lesson_hours',
        ];
    }

    public static function studentTutorLimitKeys(): array
    {
        return ['tutor_lesson_hours'];
    }

    /**
     * @param  array<string, mixed>|null  $input
     * @return array<string, int>|null
     */
    public static function sanitizeFeatureLimits(?array $input): ?array
    {
        if ($input === null || $input === []) {
            return null;
        }

        $out = [];
        foreach (self::limitSchemaKeys() as $k) {
            if (! array_key_exists($k, $input)) {
                continue;
            }
            $out[$k] = (int) $input[$k];
        }

        if (isset($out['classroom_default_duration_minutes'], $out['classroom_max_duration_minutes'])
            && $out['classroom_default_duration_minutes'] > $out['classroom_max_duration_minutes']) {
            $out['classroom_default_duration_minutes'] = $out['classroom_max_duration_minutes'];
        }

        return $out === [] ? null : $out;
    }

    /**
     * @param  array<string, int>  $raw
     * @return array{plan_key: string, classroom_meetings_per_month: int, classroom_max_participants: int, classroom_default_duration_minutes: int, classroom_max_duration_minutes: int, personal_marketing_profile_sections: int, personal_marketing_priority_score: int, personal_marketing_monthly_featured_days: int}
     */
    public static function normalizeLimitsRow(array $raw, string $planKeyForDisplay): array
    {
        $defM = max(15, (int) ($raw['classroom_default_duration_minutes'] ?? 60));
        $maxM = max(30, (int) ($raw['classroom_max_duration_minutes'] ?? 120));
        if ($defM > $maxM) {
            $defM = $maxM;
        }

        return [
            'plan_key' => $planKeyForDisplay,
            'classroom_meetings_per_month' => max(0, (int) ($raw['classroom_meetings_per_month'] ?? 0)),
            'classroom_max_participants' => max(1, (int) ($raw['classroom_max_participants'] ?? 25)),
            'classroom_default_duration_minutes' => $defM,
            'classroom_max_duration_minutes' => $maxM,
            'personal_marketing_profile_sections' => max(1, (int) ($raw['personal_marketing_profile_sections'] ?? 5)),
            'personal_marketing_priority_score' => max(0, min(100, (int) ($raw['personal_marketing_priority_score'] ?? 0))),
            'personal_marketing_monthly_featured_days' => max(0, min(31, (int) ($raw['personal_marketing_monthly_featured_days'] ?? 0))),
        ];
    }

    public static function limitsForUser(User $user): array
    {
        $plans = self::teacherPlansFromSettings();
        $sub = $user->activeSubscription();

        $planKey = $sub?->teacher_plan_key;
        if (! $planKey || ! isset($plans[$planKey])) {
            $planKey = 'teacher_starter';
        }

        $limits = self::limitsArrayForPlanKey($plans, $planKey);

        if ($sub && is_array($sub->feature_limits)) {
            foreach (self::limitSchemaKeys() as $k) {
                if (array_key_exists($k, $sub->feature_limits)) {
                    $limits[$k] = (int) $sub->feature_limits[$k];
                }
            }
        }

        $displayKey = ($sub && $sub->teacher_plan_key !== null && $sub->teacher_plan_key !== '')
            ? (string) $sub->teacher_plan_key
            : 'manual';

        return self::normalizeLimitsRow($limits, $displayKey);
    }

    public static function monthlyClassroomUsage(User $user): int
    {
        return ClassroomMeeting::query()
            ->where('user_id', $user->id)
            ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->count();
    }
}

