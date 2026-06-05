<?php

namespace App\Services;

use App\Models\InstructorProfile;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class InstructorApplicationService
{
    public static function approve(InstructorProfile $profile, User $reviewer, ?string $adminNote = null): void
    {
        DB::transaction(function () use ($profile, $reviewer, $adminNote) {
            $profile->refresh();
            $user = $profile->user;
            if (! $user) {
                return;
            }

            $user->update(['is_active' => true]);

            $profile->update([
                'status' => InstructorProfile::STATUS_APPROVED,
                'reviewed_at' => now(),
                'reviewed_by' => $reviewer->id,
                'rejection_reason' => null,
            ]);

            TutorNotificationService::instructorApplicationApproved($user, $adminNote);
        });
    }

    public static function reject(InstructorProfile $profile, User $reviewer, string $reason): void
    {
        DB::transaction(function () use ($profile, $reviewer, $reason) {
            $profile->refresh();
            $user = $profile->user;
            if (! $user) {
                return;
            }

            $user->update(['is_active' => false]);

            $profile->update([
                'status' => InstructorProfile::STATUS_REJECTED,
                'reviewed_at' => now(),
                'reviewed_by' => $reviewer->id,
                'rejection_reason' => $reason,
            ]);

            TutorNotificationService::instructorApplicationRejected($user, $reason);
        });
    }
}
