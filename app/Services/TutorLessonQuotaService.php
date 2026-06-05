<?php

namespace App\Services;

use App\Models\StudentLearningProfile;
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
     * ساعات الحصص من الاشتراك النشط (feature_limits) أو الافتراضي العام.
     */
    public static function quotaHoursForUser(User $user): int
    {
        $limitKey = config('tutor_lessons.subscription_limit_key', 'tutor_lesson_hours');
        $sub = $user->activeSubscription();

        if ($sub && is_array($sub->feature_limits) && array_key_exists($limitKey, $sub->feature_limits)) {
            return max(0, (int) $sub->feature_limits[$limitKey]);
        }

        return max(0, (int) (self::settings()['default_student_lesson_hours'] ?? 0));
    }

    public static function syncProfileForUser(User $user): StudentLearningProfile
    {
        $profile = StudentLearningProfile::firstOrCreate(
            ['user_id' => $user->id],
            ['matching_mode' => StudentLearningProfile::MODE_PICK_TEACHER]
        );

        $quota = self::quotaHoursForUser($user);
        if ($profile->lesson_hours_quota !== $quota) {
            $profile->update(['lesson_hours_quota' => $quota]);
        }

        return $profile->fresh();
    }
}
