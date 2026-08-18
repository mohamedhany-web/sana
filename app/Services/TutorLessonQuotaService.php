<?php

namespace App\Services;

use App\Models\StudentLearningProfile;
use App\Models\TutorHourPurchase;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class TutorLessonQuotaService
{
    public static function settings(): array
    {
        $defaults = config('tutor_lessons.defaults', []);

        return Cache::remember('tutor_lessons_settings', 300, function () use ($defaults) {
            if (! DB::getSchemaBuilder()->hasTable('settings')) {
                return $defaults;
            }

            $key = config('tutor_lessons.settings_key', 'tutor_lessons');
            $row = DB::table('settings')->where('key', $key)->first();
            if (! $row) {
                return $defaults;
            }

            $decoded = json_decode($row->value, true);

            return is_array($decoded) ? array_merge($defaults, $decoded) : $defaults;
        });
    }

    public static function clearSettingsCache(): void
    {
        Cache::forget('tutor_lessons_settings');
    }

    /**
     * ساعات الاشتراك الأساسية (بدون المشتريات الإضافية).
     */
    public static function baseQuotaHoursForUser(User $user): int
    {
        $limitKey = config('tutor_lessons.subscription_limit_key', 'tutor_lesson_hours');
        $sub = $user->activeSubscription();

        if ($sub && is_array($sub->feature_limits) && array_key_exists($limitKey, $sub->feature_limits)) {
            return (int) $sub->feature_limits[$limitKey];
        }

        return max(0, (int) (self::settings()['default_student_lesson_hours'] ?? 0));
    }

    /**
     * @deprecated استخدم baseQuotaHoursForUser — يُبقى للتوافق.
     */
    public static function quotaHoursForUser(User $user): int
    {
        return max(0, self::baseQuotaHoursForUser($user));
    }

    public static function syncProfileForUser(User $user): StudentLearningProfile
    {
        $profile = StudentLearningProfile::firstOrCreate(
            ['user_id' => $user->id],
            ['matching_mode' => StudentLearningProfile::MODE_PICK_TEACHER]
        );

        if ($profile->matching_mode === null || $profile->matching_mode === '') {
            $profile->update(['matching_mode' => StudentLearningProfile::MODE_PICK_TEACHER]);
        }

        $base = self::baseQuotaHoursForUser($user);
        $bonus = max(0, (int) ($profile->lesson_hours_bonus ?? 0));
        $effective = $base < 0 ? -1 : ($base + $bonus);

        if ((int) $profile->lesson_hours_quota !== $effective) {
            $profile->update(['lesson_hours_quota' => $effective]);
        }

        return $profile->fresh();
    }

    public static function addBonusHours(User $user, int $hours): StudentLearningProfile
    {
        $hours = max(0, $hours);
        $profile = StudentLearningProfile::firstOrCreate(
            ['user_id' => $user->id],
            ['matching_mode' => StudentLearningProfile::MODE_PICK_TEACHER]
        );

        if ($hours > 0) {
            $profile->increment('lesson_hours_bonus', $hours);
        }

        return self::syncProfileForUser($user->fresh());
    }

    public static function approvePurchase(TutorHourPurchase $purchase, User $admin, ?string $adminNotes = null): TutorHourPurchase
    {
        if (! $purchase->isPending()) {
            return $purchase;
        }

        return DB::transaction(function () use ($purchase, $admin, $adminNotes) {
            $purchase->update([
                'status' => TutorHourPurchase::STATUS_APPROVED,
                'approved_at' => now(),
                'approved_by' => $admin->id,
                'admin_notes' => $adminNotes,
            ]);

            self::addBonusHours($purchase->user, (int) $purchase->hours);

            return $purchase->fresh();
        });
    }

    public static function rejectPurchase(TutorHourPurchase $purchase, User $admin, ?string $adminNotes = null): TutorHourPurchase
    {
        if (! $purchase->isPending()) {
            return $purchase;
        }

        $purchase->update([
            'status' => TutorHourPurchase::STATUS_REJECTED,
            'approved_at' => now(),
            'approved_by' => $admin->id,
            'admin_notes' => $adminNotes,
        ]);

        return $purchase->fresh();
    }

    /**
     * باقات الأدمن المتاحة للشراء من الطالب (لها سعر ومفعّلة للشراء).
     *
     * @return array<string, array<string, mixed>>
     */
    public static function purchasablePlans(): array
    {
        $plans = StudentSubscriptionPlansService::getPlans();
        $out = [];

        foreach (StudentSubscriptionPlansService::planKeys() as $key) {
            $plan = $plans[$key] ?? null;
            if (! is_array($plan)) {
                continue;
            }
            $hours = (int) ($plan['limits']['tutor_lesson_hours'] ?? 0);
            $price = (float) ($plan['price'] ?? 0);
            $buyable = filter_var($plan['student_buyable'] ?? true, FILTER_VALIDATE_BOOLEAN);

            // يظهر للطالب عند وجود سعر فعلي + تفعيل الشراء (السعر يلغي وضع «تواصل فقط»)
            if (! $buyable || $price <= 0 || $hours <= 0) {
                continue;
            }

            $out[$key] = $plan;
        }

        return $out;
    }
}
