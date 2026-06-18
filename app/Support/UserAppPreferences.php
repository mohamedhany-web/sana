<?php

namespace App\Support;

use App\Models\User;

class UserAppPreferences
{
    public const DEFAULTS = [
        'notify_new_courses' => true,
        'notify_orders' => true,
        'notify_exams' => true,
        'show_progress_to_teachers' => true,
        'show_activity' => false,
        'theme' => 'light',
        'locale' => 'ar',
    ];

    /** @return array<string, mixed> */
    public static function forUser(User $user): array
    {
        $stored = is_array($user->app_preferences) ? $user->app_preferences : [];

        return array_merge(self::DEFAULTS, $stored);
    }

    /** @param array<string, mixed> $input */
    public static function normalizeFromRequest(array $input): array
    {
        $locale = ($input['locale'] ?? 'ar') === 'en' ? 'en' : 'ar';
        $theme = in_array($input['theme'] ?? 'light', ['light', 'dark', 'auto'], true)
            ? $input['theme']
            : 'light';

        return [
            'notify_new_courses' => filter_var($input['notify_new_courses'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'notify_orders' => filter_var($input['notify_orders'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'notify_exams' => filter_var($input['notify_exams'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'show_progress_to_teachers' => filter_var($input['show_progress_to_teachers'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'show_activity' => filter_var($input['show_activity'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'theme' => $theme,
            'locale' => $locale,
        ];
    }

    public static function wantsNotification(User $user, array $payload): bool
    {
        $prefs = self::forUser($user);
        $type = (string) ($payload['type'] ?? 'general');
        $data = is_array($payload['data'] ?? null) ? $payload['data'] : [];

        if ($type === 'exam' || ! empty($data['exam_id'])) {
            return (bool) $prefs['notify_exams'];
        }

        if (! empty($data['order_id'])) {
            return (bool) $prefs['notify_orders'];
        }

        if (in_array($type, ['course', 'announcement', 'assignment', 'grade'], true)) {
            return (bool) $prefs['notify_new_courses'];
        }

        if ($type === 'reminder') {
            if (! empty($data['exam_id'])) {
                return (bool) $prefs['notify_exams'];
            }

            return (bool) $prefs['notify_new_courses'];
        }

        return true;
    }

    public static function localeForUser(User $user): string
    {
        $locale = self::forUser($user)['locale'] ?? 'ar';

        return $locale === 'en' ? 'en' : 'ar_SA';
    }
}
