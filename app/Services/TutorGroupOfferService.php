<?php

namespace App\Services;

use App\Models\InstructorProfile;
use App\Models\StudentLearningProfile;
use App\Models\TutorGroupOffer;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class TutorGroupOfferService
{
    public static function studentPlanKey(User $student): ?string
    {
        $sub = $student->activeSubscription();
        if (! $sub) {
            return null;
        }

        $key = (string) ($sub->teacher_plan_key ?? '');
        if (StudentSubscriptionPlansService::isStudentPlanKey($key)) {
            return $key;
        }

        return null;
    }

    public static function groupLimitsForUser(User $student): array
    {
        $sub = $student->activeSubscription();
        $limits = is_array($sub?->feature_limits) ? $sub->feature_limits : [];
        $planKey = self::studentPlanKey($student);
        $planLimits = $planKey
            ? (StudentSubscriptionPlansService::getPlans()[$planKey]['limits'] ?? [])
            : [];

        $enabled = filter_var(
            $limits['tutor_group_enabled'] ?? $planLimits['tutor_group_enabled'] ?? false,
            FILTER_VALIDATE_BOOLEAN
        );

        $maxSize = (int) ($limits['tutor_group_max_size'] ?? $planLimits['tutor_group_max_size'] ?? 0);

        return [
            'enabled' => $enabled,
            'max_size' => max(0, $maxSize),
        ];
    }

    public static function studentCanBookGroup(User $student): bool
    {
        return self::groupLimitsForUser($student)['enabled'];
    }

    /**
     * @return Collection<int, TutorGroupOffer>
     */
    public static function offersForStudentInstructor(User $student, User $instructor, ?int $subjectId = null): Collection
    {
        if (! self::studentCanBookGroup($student)) {
            return collect();
        }

        $profile = InstructorProfile::where('user_id', $instructor->id)->first();
        if (! $profile?->isTutorActivated() || ! $profile->supportsSessionType(StudentLearningProfile::SESSION_SMALL_GROUP)) {
            return collect();
        }

        $planKey = self::studentPlanKey($student);
        $groupLimits = self::groupLimitsForUser($student);

        return TutorGroupOffer::query()
            ->active()
            ->where('instructor_id', $instructor->id)
            ->when($subjectId, fn ($q) => $q->where(function ($q) use ($subjectId) {
                $q->whereNull('academic_subject_id')->orWhere('academic_subject_id', $subjectId);
            }))
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get()
            ->filter(function (TutorGroupOffer $offer) use ($planKey, $groupLimits) {
                $planKeys = $offer->subscription_plan_keys ?? [];
                if ($planKeys !== [] && ($planKey === null || ! in_array($planKey, $planKeys, true))) {
                    return false;
                }
                if ($groupLimits['max_size'] > 0 && (int) $offer->max_group_size > $groupLimits['max_size']) {
                    return false;
                }

                return true;
            })
            ->values();
    }

    public static function resolveOfferForBooking(
        User $student,
        User $instructor,
        ?int $offerId,
        string $sessionType,
        ?int $subjectId = null
    ): ?TutorGroupOffer {
        if ($sessionType !== StudentLearningProfile::SESSION_SMALL_GROUP) {
            return null;
        }

        if (! $offerId) {
            throw ValidationException::withMessages([
                'tutor_group_offer_id' => __('tutor.group_offer_required'),
            ]);
        }

        $offer = TutorGroupOffer::query()
            ->active()
            ->where('id', $offerId)
            ->where('instructor_id', $instructor->id)
            ->first();

        if (! $offer) {
            throw ValidationException::withMessages([
                'tutor_group_offer_id' => __('tutor.group_offer_invalid'),
            ]);
        }

        $available = self::offersForStudentInstructor($student, $instructor, $subjectId);
        if (! $available->contains('id', $offer->id)) {
            throw ValidationException::withMessages([
                'tutor_group_offer_id' => __('tutor.group_offer_not_allowed'),
            ]);
        }

        return $offer;
    }
}
