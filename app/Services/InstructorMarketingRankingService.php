<?php

namespace App\Services;

use App\Models\InstructorProfile;
use App\Models\Subscription;

class InstructorMarketingRankingService
{
    public static function rankApprovedProfiles()
    {
        $profiles = InstructorProfile::approved()->with('user')->get();
        return self::rankProfilesCollection($profiles);
    }

    public static function rankProfilesCollection($profiles)
    {
        if ($profiles->isEmpty()) {
            return $profiles;
        }

        $profiles->each(function ($profile) {
            $profile->courses_count = \App\Models\AdvancedCourse::where('instructor_id', $profile->user_id)
                ->where('is_active', true)->count();
        });

        $userIds = $profiles->pluck('user_id')->unique()->values();
        $subs = Subscription::query()
            ->whereIn('user_id', $userIds)
            ->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', now());
            })
            ->orderByDesc('end_date')
            ->get(['id', 'user_id', 'teacher_plan_key', 'features', 'end_date']);

        $subByUser = $subs->groupBy('user_id')->map(fn ($rows) => $rows->first());
        $planLimits = SubscriptionLimitService::teacherPlansFromSettings();
        $defaultLimits = SubscriptionLimitService::defaultPlans()['teacher_starter']['limits'] ?? [];
        $today = now()->day;
        $daysInMonth = now()->daysInMonth;

        $featureWeights = [
            'visible_to_academies' => 25,
            'can_apply_opportunities' => 10,
            'recommended_to_academies' => 20,
            'priority_opportunities' => 20,
            'direct_support' => 8,
        ];

        $profiles->each(function ($profile) use ($subByUser, $planLimits, $defaultLimits, $today, $daysInMonth, $featureWeights) {
            $sub = $subByUser->get($profile->user_id);
            $features = Subscription::normalizeFeatureKeys(is_array($sub?->features) ? $sub->features : []);
            $planKey = $sub?->teacher_plan_key;
            $limits = $planLimits[$planKey]['limits'] ?? $defaultLimits;

            $priorityScore = (int) ($limits['personal_marketing_priority_score'] ?? 0);
            $featuredDays = max(0, (int) ($limits['personal_marketing_monthly_featured_days'] ?? 0));
            $seed = ((int) $profile->user_id * 7 + (int) now()->month + ((int) now()->year * 3));
            $todaySlot = (($today + $seed) % max(1, $daysInMonth));
            $isFeaturedToday = $featuredDays > 0 && $todaySlot < min($featuredDays, $daysInMonth);

            $featuresBonus = 0;
            foreach ($featureWeights as $featureKey => $weight) {
                if (in_array($featureKey, $features, true)) {
                    $featuresBonus += $weight;
                }
            }

            $visibilityPenalty = in_array('visible_to_academies', $features, true) ? 0 : -40;
            $featuredBonus = $isFeaturedToday ? 35 : 0;
            $coursesBonus = min(20, (int) ($profile->courses_count ?? 0) * 2);

            $profile->ranking_score = $priorityScore + $featuresBonus + $featuredBonus + $coursesBonus + $visibilityPenalty;
            $profile->marketing_featured_today = $isFeaturedToday;
            $profile->marketing_priority_score = $priorityScore;
            $profile->marketing_featured_days = $featuredDays;
            $profile->marketing_features_enabled = $features;
        });

        return $profiles->sort(function ($a, $b) {
            if ((int) $b->ranking_score !== (int) $a->ranking_score) {
                return (int) $b->ranking_score <=> (int) $a->ranking_score;
            }
            if ((bool) $b->marketing_featured_today !== (bool) $a->marketing_featured_today) {
                return (int) $b->marketing_featured_today <=> (int) $a->marketing_featured_today;
            }
            if ((int) $b->courses_count !== (int) $a->courses_count) {
                return (int) $b->courses_count <=> (int) $a->courses_count;
            }
            return $a->created_at <=> $b->created_at;
        })->values();
    }
}

