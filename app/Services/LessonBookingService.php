<?php

namespace App\Services;

use App\Models\ClassroomMeeting;
use App\Models\InstructorProfile;
use App\Models\LessonBooking;
use App\Models\StudentLearningProfile;
use App\Models\TutorAvailability;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LessonBookingService
{
    public function createBooking(array $data, User $requestedBy): LessonBooking
    {
        $studentId = (int) $data['student_id'];
        $instructorId = (int) $data['instructor_id'];
        $duration = (int) ($data['duration_minutes'] ?? 60);
        $scheduledAt = Carbon::parse($data['scheduled_at']);

        $studentUser = User::find($studentId);
        $profile = $studentUser
            ? TutorLessonQuotaService::syncProfileForUser($studentUser)
            : StudentLearningProfile::firstOrCreate(
                ['user_id' => $studentId],
                ['matching_mode' => StudentLearningProfile::MODE_PICK_TEACHER]
            );

        $isTrial = ! empty($data['is_trial']);

        if (! $isTrial && ! $profile->hasMinutesFor($duration)) {
            throw ValidationException::withMessages([
                'scheduled_at' => __('tutor.insufficient_hours'),
            ]);
        }

        $instructorProfile = InstructorProfile::where('user_id', $instructorId)->first();
        if (! $instructorProfile?->isTutorActivated() && empty($data['is_trial'])) {
            throw ValidationException::withMessages([
                'instructor_id' => __('tutor.instructor_not_available'),
            ]);
        }

        $matchingMode = $data['matching_mode'] ?? $profile->matching_mode;
        if (! empty($data['is_trial']) || $matchingMode === StudentLearningProfile::MODE_PICK_TEACHER) {
            if ($instructorProfile && ! $instructorProfile->supportsMatchingMode($matchingMode) && ! $data['is_trial']) {
                throw ValidationException::withMessages([
                    'instructor_id' => __('tutor.instructor_mode_mismatch'),
                ]);
            }
        }

        $sessionType = $data['session_type'] ?? $profile->preferred_session_type;
        if ($instructorProfile && ! $instructorProfile->supportsSessionType($sessionType) && ! $data['is_trial']) {
            throw ValidationException::withMessages([
                'session_type' => __('tutor.session_not_supported'),
            ]);
        }

        if (! $isTrial && ! $this->isSlotAvailable($instructorId, $scheduledAt, $duration)) {
            throw ValidationException::withMessages([
                'scheduled_at' => __('tutor.slot_not_available'),
            ]);
        }

        return DB::transaction(function () use ($data, $requestedBy, $studentId, $instructorId, $duration, $scheduledAt, $profile, $matchingMode, $sessionType) {
            $booking = LessonBooking::create([
                'student_id' => $studentId,
                'instructor_id' => $instructorId,
                'parent_id' => $data['parent_id'] ?? null,
                'requested_by_user_id' => $requestedBy->id,
                'academic_subject_id' => $data['academic_subject_id'] ?? null,
                'tutor_assisted_request_id' => $data['tutor_assisted_request_id'] ?? null,
                'matching_mode' => $matchingMode,
                'session_type' => $sessionType,
                'status' => LessonBooking::STATUS_PENDING,
                'is_trial' => (bool) ($data['is_trial'] ?? false),
                'scheduled_at' => $scheduledAt,
                'duration_minutes' => $duration,
                'student_notes' => $data['student_notes'] ?? null,
            ]);

            TutorNotificationService::bookingRequested($booking);

            return $booking;
        });
    }

    public function confirm(LessonBooking $booking, ?string $instructorNotes = null): LessonBooking
    {
        if ($booking->status !== LessonBooking::STATUS_PENDING) {
            throw ValidationException::withMessages(['status' => __('tutor.invalid_booking_state')]);
        }

        return DB::transaction(function () use ($booking, $instructorNotes) {
            $meeting = $this->createClassroomMeeting($booking);

            $booking->update([
                'status' => LessonBooking::STATUS_CONFIRMED,
                'confirmed_at' => now(),
                'instructor_notes' => $instructorNotes,
                'classroom_meeting_id' => $meeting->id,
            ]);

            TutorNotificationService::bookingConfirmed($booking->fresh());

            return $booking->fresh();
        });
    }

    public function cancel(LessonBooking $booking, string $byRole): LessonBooking
    {
        if (in_array($booking->status, [LessonBooking::STATUS_COMPLETED, LessonBooking::STATUS_CANCELLED], true)) {
            throw ValidationException::withMessages(['status' => __('tutor.invalid_booking_state')]);
        }

        $booking->update([
            'status' => LessonBooking::STATUS_CANCELLED,
            'cancelled_at' => now(),
            'cancelled_by' => $byRole,
        ]);

        if ($booking->classroomMeeting && ! $booking->classroomMeeting->ended_at) {
            $booking->classroomMeeting->update(['ended_at' => now()]);
        }

        TutorNotificationService::bookingCancelled($booking, $byRole);

        return $booking;
    }

    public function complete(LessonBooking $booking): LessonBooking
    {
        return DB::transaction(function () use ($booking) {
            $booking->refresh();
            $minutes = (int) $booking->billable_minutes;

            if ($booking->classroomMeeting && ! $booking->classroomMeeting->ended_at) {
                $booking->classroomMeeting->update(['ended_at' => now()]);
            }

            $booking->update([
                'status' => LessonBooking::STATUS_COMPLETED,
                'completed_at' => now(),
                'co_presence_ended_at' => $booking->co_presence_ended_at ?? now(),
            ]);

            $profile = StudentLearningProfile::firstOrCreate(['user_id' => $booking->student_id]);
            $profile->deductMinutes($minutes);

            TutorWorkLogService::recordFromBooking($booking);

            if ($booking->is_trial) {
                $instructorProfile = InstructorProfile::firstOrCreate(['user_id' => $booking->instructor_id]);
                $instructorProfile->update([
                    'tutor_trial_completed_at' => now(),
                    'tutor_activated_at' => now(),
                    'offers_tutor_booking' => true,
                    'status' => InstructorProfile::STATUS_APPROVED,
                ]);
                User::where('id', $booking->instructor_id)->update(['is_active' => true]);
                TutorNotificationService::trialCompleted($booking->instructor_id);
            }

            TutorNotificationService::bookingCompleted($booking);

            return $booking->fresh();
        });
    }

    public function createClassroomMeeting(LessonBooking $booking): ClassroomMeeting
    {
        $code = ClassroomMeeting::generateCode();
        $title = __('tutor.lesson_with', [
            'student' => $booking->student?->name ?? 'طالب',
        ]);

        return ClassroomMeeting::create([
            'user_id' => $booking->instructor_id,
            'lesson_booking_id' => $booking->id,
            'code' => $code,
            'room_name' => \App\Support\PlatformBranding::classroomRoomName($code),
            'title' => $title,
            'scheduled_for' => $booking->scheduled_at,
            'planned_duration_minutes' => $booking->duration_minutes,
            'max_participants' => $booking->session_type === StudentLearningProfile::SESSION_SMALL_GROUP ? 6 : 2,
            'settings' => ['lesson_booking_id' => $booking->id],
        ]);
    }

    public function isSlotAvailable(int $instructorId, Carbon $scheduledAt, int $durationMinutes): bool
    {
        $day = (int) $scheduledAt->dayOfWeek;
        $start = $scheduledAt->format('H:i:s');
        $end = $scheduledAt->copy()->addMinutes($durationMinutes)->format('H:i:s');

        $hasWindow = TutorAvailability::query()
            ->where('instructor_id', $instructorId)
            ->where('day_of_week', $day)
            ->where('is_active', true)
            ->where('start_time', '<=', $start)
            ->where('end_time', '>=', $end)
            ->exists();

        if (! $hasWindow) {
            return false;
        }

        $conflict = LessonBooking::query()
            ->where('instructor_id', $instructorId)
            ->whereIn('status', [LessonBooking::STATUS_PENDING, LessonBooking::STATUS_CONFIRMED, LessonBooking::STATUS_IN_PROGRESS])
            ->where(function ($q) use ($scheduledAt, $durationMinutes) {
                $endAt = $scheduledAt->copy()->addMinutes($durationMinutes);
                $q->whereBetween('scheduled_at', [$scheduledAt, $endAt])
                    ->orWhereRaw('DATE_ADD(scheduled_at, INTERVAL duration_minutes MINUTE) > ? AND scheduled_at < ?', [
                        $scheduledAt, $endAt,
                    ]);
            })
            ->exists();

        return ! $conflict;
    }

    public static function bookableInstructorsQuery(?string $matchingMode = null, ?int $subjectId = null)
    {
        $q = InstructorProfile::query()
            ->offersTutorBooking()
            ->with('user');

        if ($matchingMode) {
            $q->whereJsonContains('tutor_matching_modes', $matchingMode);
        }

        if ($subjectId) {
            $q->where(function ($sub) use ($subjectId) {
                $sub->whereJsonContains('tutor_subject_ids', (int) $subjectId)
                    ->orWhereJsonContains('tutor_subject_ids', (string) $subjectId);
            });
        }

        return $q;
    }

    /**
     * مواعيد متاحة للحجز الذاتي (تجميع نوافذ كل المعلمين المؤهلين).
     *
     * @return list<array{scheduled_at: string, label: string}>
     */
    public function availableSelfScheduleSlots(StudentLearningProfile $profile, ?int $subjectId = null): array
    {
        $settings = TutorLessonQuotaService::settings();
        $days = max(1, min(60, (int) ($settings['booking_advance_days'] ?? 14)));
        $duration = max(30, (int) ($settings['default_duration_minutes'] ?? 60));
        $step = max(15, (int) ($settings['slot_step_minutes'] ?? 30));
        $subjectId = $subjectId ?: ($profile->subject_ids[0] ?? null);

        $instructors = self::bookableInstructorsQuery(
            StudentLearningProfile::MODE_SELF_SCHEDULE,
            $subjectId
        )->get();

        $slots = [];
        $now = now();

        for ($d = 0; $d < $days; $d++) {
            $date = $now->copy()->startOfDay()->addDays($d);
            $dayOfWeek = (int) $date->dayOfWeek;

            foreach ($instructors as $instructorProfile) {
                if (! $instructorProfile->supportsSessionType($profile->preferred_session_type)) {
                    continue;
                }

                $windows = TutorAvailability::query()
                    ->where('instructor_id', $instructorProfile->user_id)
                    ->where('day_of_week', $dayOfWeek)
                    ->where('is_active', true)
                    ->get();

                foreach ($windows as $window) {
                    $cursor = $date->copy()->setTimeFromTimeString(substr((string) $window->start_time, 0, 8));
                    $windowEnd = $date->copy()->setTimeFromTimeString(substr((string) $window->end_time, 0, 8));

                    while ($cursor->copy()->addMinutes($duration)->lte($windowEnd)) {
                        if ($cursor->gt($now) && $this->isSlotAvailable($instructorProfile->user_id, $cursor, $duration)) {
                            $key = $cursor->format('Y-m-d H:i');
                            if (! isset($slots[$key])) {
                                $slots[$key] = [
                                    'scheduled_at' => $cursor->toIso8601String(),
                                    'label' => $cursor->locale(app()->getLocale())->translatedFormat('l j F — H:i'),
                                ];
                            }
                        }
                        $cursor->addMinutes($step);
                    }
                }
            }
        }

        ksort($slots);

        return array_values($slots);
    }

    public function assignInstructorForSlot(array $data, User $requestedBy): LessonBooking
    {
        $studentId = (int) $data['student_id'];
        $scheduledAt = Carbon::parse($data['scheduled_at']);
        $duration = (int) ($data['duration_minutes'] ?? 60);
        $subjectId = $data['academic_subject_id'] ?? null;

        $profile = StudentLearningProfile::firstOrCreate(
            ['user_id' => $studentId],
            ['matching_mode' => StudentLearningProfile::MODE_SELF_SCHEDULE]
        );

        $sessionType = $data['session_type'] ?? $profile->preferred_session_type;
        $candidates = self::bookableInstructorsQuery(
            StudentLearningProfile::MODE_SELF_SCHEDULE,
            $subjectId
        )->get();

        foreach ($candidates as $instructorProfile) {
            if (! $instructorProfile->supportsSessionType($sessionType)) {
                continue;
            }
            if (! $this->isSlotAvailable($instructorProfile->user_id, $scheduledAt, $duration)) {
                continue;
            }

            return $this->createBooking([
                'student_id' => $studentId,
                'instructor_id' => $instructorProfile->user_id,
                'parent_id' => $data['parent_id'] ?? null,
                'matching_mode' => StudentLearningProfile::MODE_SELF_SCHEDULE,
                'session_type' => $sessionType,
                'scheduled_at' => $scheduledAt,
                'duration_minutes' => $duration,
                'academic_subject_id' => $subjectId,
                'student_notes' => $data['student_notes'] ?? null,
            ], $requestedBy);
        }

        throw ValidationException::withMessages([
            'scheduled_at' => __('tutor.slot_not_available'),
        ]);
    }
}
