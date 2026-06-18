<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Support\UserAppPreferences;

class StudentNotificationService
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function createForStudent(User $student, array $attributes): ?Notification
    {
        if (! $student->isStudent() || ! UserAppPreferences::wantsNotification($student, $attributes)) {
            return null;
        }

        return Notification::create($attributes);
    }
}
