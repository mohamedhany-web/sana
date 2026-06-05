<?php

namespace App\Services;

use App\Models\InstructorProfile;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InstructorApplicationService
{
    public static function approve(InstructorProfile $profile, User $reviewer, ?string $adminNote = null): void
    {
        DB::transaction(function () use ($profile, $reviewer) {
            $profile->refresh();

            if ($profile->user_id) {
                self::setApplicantActiveState((int) $profile->user_id, true, $reviewer);
            }

            $profile->update([
                'status' => InstructorProfile::STATUS_APPROVED,
                'reviewed_at' => now(),
                'reviewed_by' => $reviewer->id,
                'rejection_reason' => null,
            ]);
        });

        self::notifyApproved($profile, $adminNote);
    }

    public static function reject(InstructorProfile $profile, User $reviewer, string $reason): void
    {
        DB::transaction(function () use ($profile, $reviewer, $reason) {
            $profile->refresh();

            if ($profile->user_id) {
                self::setApplicantActiveState((int) $profile->user_id, false, $reviewer);
            }

            $profile->update([
                'status' => InstructorProfile::STATUS_REJECTED,
                'reviewed_at' => now(),
                'reviewed_by' => $reviewer->id,
                'rejection_reason' => $reason,
            ]);
        });

        self::notifyRejected($profile, $reason);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function updateApplication(InstructorProfile $profile, array $data, User $reviewer): void
    {
        DB::transaction(function () use ($profile, $data, $reviewer) {
            $profile->refresh();
            $user = $profile->user;
            if (! $user) {
                return;
            }

            $user->update([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
            ]);

            $profile->update([
                'headline' => $data['headline'],
                'bio' => $data['bio'],
                'tutor_years_experience' => (int) $data['years_experience'],
                'tutor_subject_ids' => array_map('intval', $data['subject_ids']),
                'tutor_academic_year_ids' => array_map('intval', $data['academic_year_ids']),
                'tutor_matching_modes' => array_values($data['matching_modes']),
                'tutor_session_types' => array_values($data['session_types']),
                'reviewed_at' => now(),
                'reviewed_by' => $reviewer->id,
            ]);
        });
    }

    public static function toggleAccountActive(InstructorProfile $profile, User $reviewer): bool
    {
        $user = $profile->user;
        if (! $user) {
            return false;
        }

        $newState = ! $user->is_active;
        if (! $newState && self::mustKeepAccountActive($user)) {
            return (bool) $user->is_active;
        }

        self::setApplicantActiveState((int) $user->id, $newState, $reviewer);

        return $newState;
    }

    public static function setAccountActive(InstructorProfile $profile, User $reviewer, bool $active): void
    {
        if (! $profile->user_id) {
            return;
        }

        self::setApplicantActiveState((int) $profile->user_id, $active, $reviewer);
    }

    public static function reopenForReview(InstructorProfile $profile, User $reviewer): void
    {
        DB::transaction(function () use ($profile, $reviewer) {
            $profile->refresh();

            if ($profile->user_id) {
                self::setApplicantActiveState((int) $profile->user_id, false, $reviewer);
            }

            $profile->update([
                'status' => InstructorProfile::STATUS_PENDING_REVIEW,
                'reviewed_at' => now(),
                'reviewed_by' => $reviewer->id,
                'rejection_reason' => null,
            ]);
        });
    }

    public static function destroyApplication(InstructorProfile $profile, User $reviewer): void
    {
        DB::transaction(function () use ($profile, $reviewer) {
            $userId = $profile->user_id;

            $profile->delete();

            if ($userId) {
                $user = User::query()->find($userId);
                if ($user && ! self::mustKeepAccountActive($user)) {
                    self::setApplicantActiveState($userId, false, $reviewer);
                }
            }
        });
    }

    private static function setApplicantActiveState(int $applicantUserId, bool $isActive, User $reviewer): void
    {
        $applicant = User::query()->find($applicantUserId);
        if (! $applicant) {
            return;
        }

        if (! $isActive && self::mustKeepAccountActive($applicant)) {
            return;
        }

        User::query()->whereKey($applicantUserId)->update(['is_active' => $isActive]);

        if (Auth::id() === $applicantUserId) {
            Auth::setUser($reviewer->fresh());
        }
    }

    public static function mustKeepAccountActive(User $user): bool
    {
        return in_array($user->role, ['super_admin', 'admin'], true) || $user->is_employee;
    }

    private static function notifyApproved(InstructorProfile $profile, ?string $adminNote): void
    {
        try {
            $user = $profile->user()->first();
            if ($user) {
                TutorNotificationService::instructorApplicationApproved($user, $adminNote);
            }
        } catch (\Throwable $e) {
            Log::error('instructor application approved notification failed', [
                'profile_id' => $profile->id,
                'user_id' => $profile->user_id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private static function notifyRejected(InstructorProfile $profile, string $reason): void
    {
        try {
            $user = $profile->user()->first();
            if ($user) {
                TutorNotificationService::instructorApplicationRejected($user, $reason);
            }
        } catch (\Throwable $e) {
            Log::error('instructor application rejected notification failed', [
                'profile_id' => $profile->id,
                'user_id' => $profile->user_id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
