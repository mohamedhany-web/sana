<?php

namespace App\Services;

use App\Models\InstructorProfile;
use App\Models\StudentLearningProfile;
use App\Models\User;

class TutorInstructorActivationService
{
    /**
     * تفعيل المعلم ليظهر للطلاب (اختيار معلم / حجز ذاتي) بعد إكمال الملف + جدول أسبوعي.
     */
    public static function attemptAutoActivate(InstructorProfile $profile, User $instructor): bool
    {
        if ($profile->isTutorActivated()) {
            return true;
        }

        if (! config('tutor_lessons.auto_activate_on_setup', true)) {
            return false;
        }

        if (! $profile->tutor_onboarding_completed_at) {
            return false;
        }

        $modes = $profile->tutor_matching_modes ?? [];
        $bookableModes = [
            StudentLearningProfile::MODE_PICK_TEACHER,
            StudentLearningProfile::MODE_SELF_SCHEDULE,
        ];
        if (count(array_intersect($modes, $bookableModes)) === 0) {
            return false;
        }

        if (! $instructor->tutorAvailabilities()->where('is_active', true)->exists()) {
            return false;
        }

        $profile->update([
            'offers_tutor_booking' => true,
            'tutor_activated_at' => $profile->tutor_activated_at ?? now(),
            'status' => InstructorProfile::STATUS_APPROVED,
        ]);

        return true;
    }

    public static function activationBlockers(InstructorProfile $profile, User $instructor): array
    {
        $blockers = [];

        if (! $profile->tutor_onboarding_completed_at) {
            $blockers[] = 'أكمل واحفظ الملف التعريفي.';
        }

        $modes = $profile->tutor_matching_modes ?? [];
        if (! in_array(StudentLearningProfile::MODE_PICK_TEACHER, $modes, true)
            && ! in_array(StudentLearningProfile::MODE_SELF_SCHEDULE, $modes, true)) {
            $blockers[] = 'فعّل نمط «اختيار معلم» أو «حجز ذاتي» في أنماط التوافق.';
        }

        if (! $instructor->tutorAvailabilities()->where('is_active', true)->exists()) {
            $blockers[] = 'أضف فترة واحدة على الأقل في الجدول الأسبوعي.';
        }

        if (! $profile->isTutorActivated()) {
            if (! config('tutor_lessons.auto_activate_on_setup', true)) {
                $blockers[] = 'بانتظار تفعيل الإدارة أو إتمام الجلسة التجريبية.';
            }
        }

        return $blockers;
    }
}
