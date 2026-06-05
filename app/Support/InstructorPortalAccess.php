<?php

namespace App\Support;

use App\Models\InstructorProfile;
use App\Models\User;

class InstructorPortalAccess
{
    public static function resolve(?User $user): string
    {
        if (! $user) {
            return InstructorProfile::PORTAL_BOTH;
        }

        $profile = $user->instructorProfile;
        if (! $profile || $profile->status !== InstructorProfile::STATUS_APPROVED) {
            return InstructorProfile::PORTAL_BOTH;
        }

        return $profile->instructor_portal_mode ?? InstructorProfile::PORTAL_BOTH;
    }

    public static function hasTutorLessonsPortal(?User $user = null): bool
    {
        $mode = self::resolve($user ?? auth()->user());

        return in_array($mode, [InstructorProfile::PORTAL_TUTOR_LESSONS, InstructorProfile::PORTAL_BOTH], true);
    }

    public static function hasCoursesPortal(?User $user = null): bool
    {
        $mode = self::resolve($user ?? auth()->user());

        return in_array($mode, [InstructorProfile::PORTAL_COURSES, InstructorProfile::PORTAL_BOTH], true);
    }

    public static function isRestricted(?User $user = null): bool
    {
        $user = $user ?? auth()->user();
        if (! $user?->instructorProfile) {
            return false;
        }

        if ($user->instructorProfile->status !== InstructorProfile::STATUS_APPROVED) {
            return false;
        }

        return self::resolve($user) !== InstructorProfile::PORTAL_BOTH;
    }

    public static function homeRoute(?User $user = null): string
    {
        $user = $user ?? auth()->user();
        $mode = self::resolve($user);

        if ($mode === InstructorProfile::PORTAL_TUTOR_LESSONS) {
            return 'instructor.tutor-lessons.hub';
        }

        return 'dashboard';
    }

    public static function modeLabel(?string $mode): string
    {
        return match ($mode) {
            InstructorProfile::PORTAL_TUTOR_LESSONS => 'حصص خاصة فقط',
            InstructorProfile::PORTAL_COURSES => 'كورسات فقط',
            InstructorProfile::PORTAL_BOTH => 'حصص وكورسات',
            default => 'غير محدد',
        };
    }
}
