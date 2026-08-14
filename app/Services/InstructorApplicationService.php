<?php

namespace App\Services;

use App\Mail\InstructorAccountActivatedMail;
use App\Models\InstructorProfile;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class InstructorApplicationService
{
    public static function approve(
        InstructorProfile $profile,
        User $reviewer,
        ?string $adminNote = null,
        string $portalMode = InstructorProfile::PORTAL_BOTH
    ): void {
        if (! in_array($portalMode, InstructorProfile::PORTAL_MODES, true)) {
            $portalMode = InstructorProfile::PORTAL_BOTH;
        }

        DB::transaction(function () use ($profile, $reviewer, $portalMode) {
            $profile->refresh();

            if ($profile->user_id) {
                self::setApplicantActiveState((int) $profile->user_id, true, $reviewer, false);
            }

            $profile->update([
                'status' => InstructorProfile::STATUS_APPROVED,
                'instructor_portal_mode' => $portalMode,
                'reviewed_at' => now(),
                'reviewed_by' => $reviewer->id,
                'rejection_reason' => null,
                'submitted_at' => $profile->submitted_at ?? now(),
                // يظهر على الرئيسية افتراضياً بعد القبول؛ التسويق يمكنه الإخفاء لاحقاً دون إلغاء القبول
                'show_on_homepage' => true,
                // بعد موافقة الإدارة يظهر للطالب (حجز/كتالوج) إن وُجدت مواد من ملف التقديم
                'offers_tutor_booking' => is_array($profile->tutor_subject_ids) && count($profile->tutor_subject_ids) > 0
                    ? true
                    : (bool) $profile->offers_tutor_booking,
                'tutor_activated_at' => is_array($profile->tutor_subject_ids) && count($profile->tutor_subject_ids) > 0
                    ? ($profile->tutor_activated_at ?? now())
                    : $profile->tutor_activated_at,
            ]);
        });

        self::notifyApproved($profile, $adminNote);
    }

    public static function reject(InstructorProfile $profile, User $reviewer, string $reason): void
    {
        DB::transaction(function () use ($profile, $reviewer, $reason) {
            $profile->refresh();

            if ($profile->user_id) {
                self::setApplicantActiveState((int) $profile->user_id, false, $reviewer, false);
            }

            $profile->update([
                'status' => InstructorProfile::STATUS_REJECTED,
                'reviewed_at' => now(),
                'reviewed_by' => $reviewer->id,
                'rejection_reason' => $reason,
                'offers_tutor_booking' => false,
                'tutor_activated_at' => null,
                'show_on_homepage' => false,
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

            $updates = [
                'headline' => $data['headline'],
                'bio' => $data['bio'],
                'tutor_years_experience' => (int) $data['years_experience'],
                'tutor_subject_ids' => array_map('intval', $data['subject_ids']),
                'tutor_academic_year_ids' => array_map('intval', $data['academic_year_ids']),
                'tutor_matching_modes' => array_values($data['matching_modes']),
                'tutor_session_types' => array_values($data['session_types']),
                'reviewed_at' => now(),
                'reviewed_by' => $reviewer->id,
            ];

            if (
                $profile->status === InstructorProfile::STATUS_APPROVED
                && isset($data['instructor_portal_mode'])
                && in_array($data['instructor_portal_mode'], InstructorProfile::PORTAL_MODES, true)
            ) {
                $updates['instructor_portal_mode'] = $data['instructor_portal_mode'];
            }

            $profile->update($updates);
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

        self::setApplicantActiveState((int) $user->id, $newState, $reviewer, true);

        return $newState;
    }

    public static function setAccountActive(InstructorProfile $profile, User $reviewer, bool $active): void
    {
        if (! $profile->user_id) {
            return;
        }

        self::setApplicantActiveState((int) $profile->user_id, $active, $reviewer, true);
    }

    public static function reopenForReview(InstructorProfile $profile, User $reviewer): void
    {
        DB::transaction(function () use ($profile, $reviewer) {
            $profile->refresh();

            if ($profile->user_id) {
                self::setApplicantActiveState((int) $profile->user_id, false, $reviewer, false);
            }

            $profile->update([
                'status' => InstructorProfile::STATUS_PENDING_REVIEW,
                'reviewed_at' => now(),
                'reviewed_by' => $reviewer->id,
                'rejection_reason' => null,
                'offers_tutor_booking' => false,
                'tutor_activated_at' => null,
                'show_on_homepage' => false,
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
                    self::setApplicantActiveState($userId, false, $reviewer, false);
                }
            }
        });
    }

    /**
     * @param  bool  $notifyEmail  إرسال إيميل عند التفعيل. عند القبول يُرسل عبر notifyApproved لتجنّب التكرار.
     */
    private static function setApplicantActiveState(int $applicantUserId, bool $isActive, User $reviewer, bool $notifyEmail = true): void
    {
        $applicant = User::query()->find($applicantUserId);
        if (! $applicant) {
            return;
        }

        if (! $isActive && self::mustKeepAccountActive($applicant)) {
            return;
        }

        $wasActive = (bool) $applicant->is_active;

        User::query()->whereKey($applicantUserId)->update(['is_active' => $isActive]);

        if (Auth::id() === $applicantUserId) {
            Auth::setUser($reviewer->fresh());
        }

        if ($notifyEmail && $isActive && ! $wasActive) {
            self::sendActivatedEmail($applicant->fresh());
        }
    }

    public static function mustKeepAccountActive(User $user): bool
    {
        return in_array($user->role, ['super_admin', 'admin'], true) || $user->is_employee;
    }

    public static function sendActivatedEmail(User $user, ?string $adminNote = null): bool
    {
        $email = trim((string) $user->email);
        if ($email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Log::warning('instructor activation email skipped — invalid address', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);

            return false;
        }

        try {
            Mail::to($email, $user->name)->send(new InstructorAccountActivatedMail($user, $adminNote));

            Log::info('instructor activation email sent', [
                'user_id' => $user->id,
                'email' => $email,
            ]);

            return true;
        } catch (\Throwable $e) {
            Log::error('instructor activation email failed', [
                'user_id' => $user->id,
                'email' => $email,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    private static function notifyApproved(InstructorProfile $profile, ?string $adminNote): void
    {
        try {
            $user = $profile->user()->first();
            if (! $user) {
                return;
            }

            TutorNotificationService::instructorApplicationApproved($user, $adminNote);
            self::sendActivatedEmail($user, $adminNote);
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
