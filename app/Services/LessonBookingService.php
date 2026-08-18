<?php

namespace App\Services;

use App\Models\ClassroomMeeting;
use App\Models\InstructorProfile;
use App\Models\LessonBooking;
use App\Models\StudentLearningProfile;
use App\Models\TutorAvailability;
use App\Models\TutorGroupOffer;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
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

        if (! $isTrial && empty($data['skip_quota']) && ! $profile->hasMinutesFor($duration)) {
            throw ValidationException::withMessages([
                'scheduled_at' => __('tutor.insufficient_hours'),
            ]);
        }

        $instructorProfile = InstructorProfile::where('user_id', $instructorId)->first();
        if (! $instructorProfile?->isTutorActivated() && ! $isTrial) {
            throw ValidationException::withMessages([
                'instructor_id' => __('tutor.instructor_not_available'),
            ]);
        }

        $matchingMode = $data['matching_mode'] ?? $profile->matching_mode ?? StudentLearningProfile::MODE_PICK_TEACHER;
        if ($matchingMode === '' || $matchingMode === null) {
            $matchingMode = StudentLearningProfile::MODE_PICK_TEACHER;
        }
        // الطالب يختار المعلم بنفسه من القائمة — لا نمنع الحجز بسبب نمط التوافق في ملف المعلم.
        if (
            empty($data['admin_booking'])
            && $matchingMode === StudentLearningProfile::MODE_SELF_SCHEDULE
            && $instructorProfile
            && ! $instructorProfile->supportsMatchingMode($matchingMode)
            && ! $isTrial
        ) {
            throw ValidationException::withMessages([
                'instructor_id' => __('tutor.instructor_mode_mismatch'),
            ]);
        }

        $sessionType = $data['session_type'] ?? $profile->preferred_session_type ?? StudentLearningProfile::SESSION_ONE_TO_ONE;
        if ($instructorProfile) {
            $sessionType = $instructorProfile->resolveSessionType(is_string($sessionType) ? $sessionType : null);
        }

        $groupOffer = null;
        $maxGroupSize = isset($data['max_group_size']) ? (int) $data['max_group_size'] : null;

        if ($sessionType === StudentLearningProfile::SESSION_SMALL_GROUP && ! $isTrial) {
            if (! empty($data['admin_booking'])) {
                if (! empty($data['tutor_group_offer_id'])) {
                    $groupOffer = TutorGroupOffer::query()
                        ->active()
                        ->where('id', (int) $data['tutor_group_offer_id'])
                        ->where('instructor_id', $instructorId)
                        ->first();
                    if (! $groupOffer) {
                        throw ValidationException::withMessages([
                            'tutor_group_offer_id' => __('tutor.group_offer_invalid'),
                        ]);
                    }
                    $duration = (int) ($groupOffer->duration_minutes ?: $duration);
                    $maxGroupSize = (int) ($groupOffer->max_group_size ?: ($maxGroupSize ?: 5));
                } else {
                    $maxGroupSize = max(2, $maxGroupSize ?: 5);
                }
            } else {
                if (! $studentUser) {
                    throw ValidationException::withMessages([
                        'session_type' => __('tutor.group_offer_not_allowed'),
                    ]);
                }
                $instructorUser = User::find($instructorId);
                $groupOffer = TutorGroupOfferService::resolveOfferForBooking(
                    $studentUser,
                    $instructorUser,
                    isset($data['tutor_group_offer_id']) ? (int) $data['tutor_group_offer_id'] : null,
                    $sessionType,
                    isset($data['academic_subject_id']) ? (int) $data['academic_subject_id'] : null
                );
                $duration = (int) ($groupOffer->duration_minutes ?: $duration);
                $maxGroupSize = (int) ($groupOffer->max_group_size ?: 5);
            }
        }

        $seatsNeeded = max(1, (int) ($data['seats_needed'] ?? 1));
        $requireAvailabilityWindow = empty($data['ignore_availability_window']);

        if (! $isTrial && ! $this->isSlotAvailable(
            $instructorId,
            $scheduledAt,
            $duration,
            $sessionType,
            $seatsNeeded,
            $maxGroupSize,
            $requireAvailabilityWindow
        )) {
            throw ValidationException::withMessages([
                'scheduled_at' => __('tutor.slot_not_available'),
            ]);
        }

        return DB::transaction(function () use ($data, $requestedBy, $studentId, $instructorId, $duration, $scheduledAt, $matchingMode, $sessionType, $groupOffer, $maxGroupSize, $isTrial) {
            $booking = LessonBooking::create([
                'student_id' => $studentId,
                'instructor_id' => $instructorId,
                'parent_id' => $data['parent_id'] ?? null,
                'requested_by_user_id' => $requestedBy->id,
                'academic_subject_id' => $data['academic_subject_id'] ?? null,
                'tutor_assisted_request_id' => $data['tutor_assisted_request_id'] ?? null,
                'matching_mode' => $matchingMode,
                'session_type' => $sessionType,
                'tutor_group_offer_id' => $groupOffer?->id ?? ($data['tutor_group_offer_id'] ?? null),
                'max_group_size' => $maxGroupSize ?? $groupOffer?->max_group_size,
                'group_session_key' => $data['group_session_key'] ?? null,
                'status' => LessonBooking::STATUS_PENDING,
                'is_trial' => $isTrial,
                'scheduled_at' => $scheduledAt,
                'duration_minutes' => $duration,
                'student_notes' => $data['student_notes'] ?? null,
                'instructor_notes' => $data['instructor_notes'] ?? null,
            ]);

            if (empty($data['skip_notification'])) {
                TutorNotificationService::bookingRequested($booking);
            }

            return $booking;
        });
    }

    /**
     * حجز إداري: طالب واحد أو عدة طلاب (مجموعة) مع خيار التأكيد الفوري.
     *
     * @param  array{
     *     student_ids: list<int>,
     *     instructor_id: int,
     *     scheduled_at: mixed,
     *     duration_minutes?: int,
     *     session_type: string,
     *     academic_subject_id?: int|null,
     *     tutor_group_offer_id?: int|null,
     *     max_group_size?: int|null,
     *     confirmation_mode?: string,
     *     enforce_quota?: bool,
     *     ignore_availability_window?: bool,
     *     student_notes?: string|null,
     *     instructor_notes?: string|null
     * }  $data
     * @return Collection<int, LessonBooking>
     */
    public function createAdminBookings(array $data, User $admin): Collection
    {
        $studentIds = array_values(array_unique(array_map('intval', $data['student_ids'] ?? [])));
        if ($studentIds === []) {
            throw ValidationException::withMessages([
                'student_ids' => 'اختر طالباً واحداً على الأقل.',
            ]);
        }

        $sessionType = (string) ($data['session_type'] ?? StudentLearningProfile::SESSION_ONE_TO_ONE);
        if ($sessionType === StudentLearningProfile::SESSION_ONE_TO_ONE && count($studentIds) > 1) {
            throw ValidationException::withMessages([
                'student_ids' => 'الحجز الفردي يقبل طالباً واحداً فقط. اختر «مجموعة» لحجز عدة طلاب.',
            ]);
        }

        if ($sessionType === StudentLearningProfile::SESSION_SMALL_GROUP && count($studentIds) < 1) {
            throw ValidationException::withMessages([
                'student_ids' => 'اختر طلاب المجموعة.',
            ]);
        }

        $instructorId = (int) $data['instructor_id'];
        $instructorProfile = InstructorProfile::where('user_id', $instructorId)->first();
        if (! $instructorProfile?->isTutorActivated()) {
            throw ValidationException::withMessages([
                'instructor_id' => __('tutor.instructor_not_available'),
            ]);
        }

        $duration = (int) ($data['duration_minutes'] ?? 60);
        $scheduledAt = ! empty($data['scheduled_at'])
            ? Carbon::parse($data['scheduled_at'])
            : now()->addHour();
        $maxGroupSize = isset($data['max_group_size']) ? max(2, (int) $data['max_group_size']) : 5;
        $offerId = isset($data['tutor_group_offer_id']) ? (int) $data['tutor_group_offer_id'] : null;

        if ($sessionType === StudentLearningProfile::SESSION_SMALL_GROUP && $offerId) {
            $offer = TutorGroupOffer::query()
                ->active()
                ->where('id', $offerId)
                ->where('instructor_id', $instructorId)
                ->first();
            if (! $offer) {
                throw ValidationException::withMessages([
                    'tutor_group_offer_id' => __('tutor.group_offer_invalid'),
                ]);
            }
            $duration = (int) ($offer->duration_minutes ?: $duration);
            $maxGroupSize = (int) ($offer->max_group_size ?: $maxGroupSize);
        }

        if ($sessionType === StudentLearningProfile::SESSION_SMALL_GROUP && count($studentIds) > $maxGroupSize) {
            throw ValidationException::withMessages([
                'student_ids' => 'عدد الطلاب ('.count($studentIds).') يتجاوز الحد الأقصى للمجموعة ('.$maxGroupSize.').',
            ]);
        }

        $students = User::query()
            ->whereIn('id', $studentIds)
            ->where('role', 'student')
            ->get()
            ->keyBy('id');

        foreach ($studentIds as $sid) {
            if (! $students->has($sid)) {
                throw ValidationException::withMessages([
                    'student_ids' => 'أحد الطلاب المحددين غير صالح.',
                ]);
            }
        }

        $enforceQuota = array_key_exists('enforce_quota', $data)
            ? filter_var($data['enforce_quota'], FILTER_VALIDATE_BOOLEAN)
            : true;
        $ignoreWindow = filter_var($data['ignore_availability_window'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $confirmationMode = (string) ($data['confirmation_mode'] ?? 'await_instructor');
        if (! in_array($confirmationMode, ['await_instructor', 'confirm_now'], true)) {
            $confirmationMode = 'await_instructor';
        }

        if ($enforceQuota) {
            $short = [];
            foreach ($studentIds as $sid) {
                $profile = TutorLessonQuotaService::syncProfileForUser($students->get($sid));
                if (! $profile->hasMinutesFor($duration)) {
                    $short[] = $students->get($sid)->name;
                }
            }
            if ($short !== []) {
                throw ValidationException::withMessages([
                    'student_ids' => 'رصيد الحصص غير كافٍ لـ: '.implode('، ', $short).'. ألغِ «التحقق من الرصيد» للمتابعة أو زِد الحصة.',
                ]);
            }
        }

        $joinGroupKey = isset($data['group_session_key']) ? trim((string) $data['group_session_key']) : '';
        $groupKey = null;

        if ($joinGroupKey !== '') {
            if ($sessionType !== StudentLearningProfile::SESSION_SMALL_GROUP) {
                throw ValidationException::withMessages([
                    'group_session_key' => 'الانضمام لمجموعة يتطلب نوع الحجز «مجموعة».',
                ]);
            }

            $existingGroup = LessonBooking::query()
                ->where('group_session_key', $joinGroupKey)
                ->whereIn('status', [
                    LessonBooking::STATUS_PENDING,
                    LessonBooking::STATUS_CONFIRMED,
                    LessonBooking::STATUS_IN_PROGRESS,
                ])
                ->orderBy('id')
                ->get();

            if ($existingGroup->isEmpty()) {
                throw ValidationException::withMessages([
                    'group_session_key' => 'المجموعة المحددة غير موجودة أو مغلقة.',
                ]);
            }

            $anchor = $existingGroup->first();
            if ((int) $anchor->instructor_id !== $instructorId) {
                throw ValidationException::withMessages([
                    'instructor_id' => 'المعلم لا يطابق مجموعة الحصة المحددة.',
                ]);
            }

            if ($existingGroup->whereIn('student_id', $studentIds)->isNotEmpty()) {
                throw ValidationException::withMessages([
                    'student_ids' => 'أحد الطلاب مسجّل مسبقاً في هذه المجموعة.',
                ]);
            }

            $scheduledAt = Carbon::parse($anchor->scheduled_at);
            $duration = (int) $anchor->duration_minutes;
            $maxGroupSize = max(2, (int) ($existingGroup->max('max_group_size') ?: $maxGroupSize));
            $offerId = $anchor->tutor_group_offer_id ? (int) $anchor->tutor_group_offer_id : $offerId;
            if (empty($data['academic_subject_id']) && $anchor->academic_subject_id) {
                $data['academic_subject_id'] = $anchor->academic_subject_id;
            }
            $groupKey = $joinGroupKey;

            if (($existingGroup->count() + count($studentIds)) > $maxGroupSize) {
                throw ValidationException::withMessages([
                    'student_ids' => 'لا تتسع المجموعة للمقاعد المطلوبة (المتبقي: '.max(0, $maxGroupSize - $existingGroup->count()).').',
                ]);
            }
        } else {
            $groupKey = $sessionType === StudentLearningProfile::SESSION_SMALL_GROUP
                ? (string) Str::uuid()
                : null;
        }

        if (! $this->isSlotAvailable(
            $instructorId,
            $scheduledAt,
            $duration,
            $sessionType,
            count($studentIds),
            $sessionType === StudentLearningProfile::SESSION_SMALL_GROUP ? $maxGroupSize : null,
            ! $ignoreWindow
        )) {
            throw ValidationException::withMessages([
                'scheduled_at' => __('tutor.slot_not_available'),
            ]);
        }

        return DB::transaction(function () use (
            $data,
            $admin,
            $studentIds,
            $instructorId,
            $duration,
            $scheduledAt,
            $sessionType,
            $maxGroupSize,
            $offerId,
            $groupKey,
            $joinGroupKey,
            $enforceQuota,
            $ignoreWindow,
            $confirmationMode
        ) {
            $bookings = collect();

            foreach ($studentIds as $index => $studentId) {
                $booking = $this->createBooking([
                    'student_id' => $studentId,
                    'instructor_id' => $instructorId,
                    'matching_mode' => StudentLearningProfile::MODE_ASSISTED,
                    'session_type' => $sessionType,
                    'scheduled_at' => $scheduledAt,
                    'duration_minutes' => $duration,
                    'academic_subject_id' => $data['academic_subject_id'] ?? null,
                    'tutor_group_offer_id' => $offerId,
                    'max_group_size' => $sessionType === StudentLearningProfile::SESSION_SMALL_GROUP ? $maxGroupSize : null,
                    'group_session_key' => $groupKey,
                    'student_notes' => $data['student_notes'] ?? null,
                    'instructor_notes' => $data['instructor_notes'] ?? null,
                    'admin_booking' => true,
                    'skip_quota' => ! $enforceQuota,
                    'ignore_availability_window' => $ignoreWindow,
                    // التحقق الكامل تم قبل الحلقة؛ المقاعد تُنشأ معاً في نفس المعاملة
                    'seats_needed' => 0,
                    'skip_notification' => false,
                ], $admin);

                $bookings->push($booking);
            }

            if ($confirmationMode === 'confirm_now') {
                $meeting = null;
                if ($joinGroupKey !== '') {
                    $sib = LessonBooking::query()
                        ->where('group_session_key', $groupKey)
                        ->whereNotNull('classroom_meeting_id')
                        ->with('classroomMeeting')
                        ->orderBy('id')
                        ->first();
                    $meeting = $sib?->classroomMeeting;
                }
                foreach ($bookings as $booking) {
                    $booking->refresh();
                    if ($booking->status !== LessonBooking::STATUS_PENDING) {
                        continue;
                    }
                    if (! $meeting) {
                        $meeting = $this->createClassroomMeeting($booking->fresh(['student']));
                    } else {
                        $meeting->update([
                            'max_participants' => max(
                                (int) $meeting->max_participants,
                                $this->resolveMaxParticipants($booking)
                            ),
                        ]);
                    }

                    $booking->update([
                        'status' => LessonBooking::STATUS_CONFIRMED,
                        'confirmed_at' => now(),
                        'classroom_meeting_id' => $meeting->id,
                        'instructor_notes' => $data['instructor_notes'] ?? $booking->instructor_notes,
                    ]);

                    TutorNotificationService::bookingConfirmed($booking->fresh());
                }
            }

            return $bookings->map(fn (LessonBooking $b) => $b->fresh());
        });
    }

    public function confirm(LessonBooking $booking, ?string $instructorNotes = null): LessonBooking
    {
        if ($booking->status !== LessonBooking::STATUS_PENDING) {
            throw ValidationException::withMessages(['status' => __('tutor.invalid_booking_state')]);
        }

        return DB::transaction(function () use ($booking, $instructorNotes) {
            $meeting = $this->resolveSharedClassroomMeeting($booking)
                ?? $this->createClassroomMeeting($booking);

            $booking->update([
                'status' => LessonBooking::STATUS_CONFIRMED,
                'confirmed_at' => now(),
                'instructor_notes' => $instructorNotes,
                'classroom_meeting_id' => $meeting->id,
            ]);

            // تأكيد باقي حجوزات نفس المجموعة بنفس الغرفة (اختياري عند تأكيد المعلم لأول واحد)
            if ($booking->group_session_key) {
                $siblings = LessonBooking::query()
                    ->where('group_session_key', $booking->group_session_key)
                    ->where('id', '!=', $booking->id)
                    ->where('status', LessonBooking::STATUS_PENDING)
                    ->get();

                foreach ($siblings as $sibling) {
                    $sibling->update([
                        'status' => LessonBooking::STATUS_CONFIRMED,
                        'confirmed_at' => now(),
                        'classroom_meeting_id' => $meeting->id,
                        'instructor_notes' => $instructorNotes ?? $sibling->instructor_notes,
                    ]);
                    TutorNotificationService::bookingConfirmed($sibling->fresh());
                }

                $meeting->update([
                    'max_participants' => max(
                        (int) $meeting->max_participants,
                        $this->resolveMaxParticipants($booking->fresh())
                    ),
                ]);
            }

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
            $stillActive = LessonBooking::query()
                ->where('classroom_meeting_id', $booking->classroom_meeting_id)
                ->where('id', '!=', $booking->id)
                ->whereIn('status', [
                    LessonBooking::STATUS_PENDING,
                    LessonBooking::STATUS_CONFIRMED,
                    LessonBooking::STATUS_IN_PROGRESS,
                ])
                ->exists();

            if (! $stillActive) {
                $booking->classroomMeeting->update(['ended_at' => now()]);
            }
        }

        TutorNotificationService::bookingCancelled($booking, $byRole);

        return $booking;
    }

    public function complete(LessonBooking $booking): LessonBooking
    {
        return DB::transaction(function () use ($booking) {
            $booking = LessonBooking::query()->lockForUpdate()->findOrFail($booking->id);

            if ($booking->status === LessonBooking::STATUS_COMPLETED) {
                return $booking;
            }

            if ($booking->status === LessonBooking::STATUS_CANCELLED) {
                throw ValidationException::withMessages([
                    'status' => __('tutor.invalid_booking_state'),
                ]);
            }

            $minutes = (int) $booking->billable_minutes;
            if (Schema::hasTable('classroom_meetings')) {
                $booking->loadMissing('classroomMeeting');
                $minutes = app(TutorAttendanceService::class)->resolveBillableMinutes($booking);
                $booking->refresh();

                if ($booking->classroomMeeting && ! $booking->classroomMeeting->ended_at) {
                    $booking->classroomMeeting->update(['ended_at' => now()]);
                }
            }

            $completedPayload = [
                'status' => LessonBooking::STATUS_COMPLETED,
                'completed_at' => now(),
                'billable_minutes' => $minutes,
            ];
            if (Schema::hasColumn('lesson_bookings', 'co_presence_ended_at')) {
                $completedPayload['co_presence_ended_at'] = $booking->co_presence_ended_at ?? now();
            }
            $booking->update($completedPayload);

            $hoursAlreadyDeducted = Schema::hasColumn('lesson_bookings', 'hours_deducted')
                ? (bool) $booking->hours_deducted
                : false;

            if (! $hoursAlreadyDeducted && ! $booking->is_trial) {
                $profile = StudentLearningProfile::firstOrCreate(['user_id' => $booking->student_id]);
                $profile->deductMinutes($minutes);
                if (Schema::hasColumn('lesson_bookings', 'hours_deducted')) {
                    $booking->update(['hours_deducted' => true]);
                }
            }

            TutorWorkLogService::recordFromBooking($booking->fresh());
            InstructorHourlyLessonPayService::processLessonCompletion($booking->fresh(['student']));

            if ($booking->is_trial) {
                $instructorProfile = InstructorProfile::firstOrCreate(['user_id' => $booking->instructor_id]);
                $instructorProfile->update([
                    'tutor_trial_completed_at' => now(),
                    'tutor_activated_at' => now(),
                    'offers_tutor_booking' => true,
                    'status' => InstructorProfile::STATUS_APPROVED,
                    'submitted_at' => $instructorProfile->submitted_at ?? now(),
                ]);
                User::where('id', $booking->instructor_id)->update(['is_active' => true]);
                TutorNotificationService::trialCompleted($booking->instructor_id);
            }

            TutorNotificationService::bookingCompleted($booking->fresh());

            return $booking->fresh();
        });
    }

    public function createClassroomMeeting(LessonBooking $booking): ClassroomMeeting
    {
        $code = ClassroomMeeting::generateCode();
        $studentName = $booking->student?->name ?? 'طالب';
        $title = $booking->group_session_key
            ? 'حصة مجموعة — '.$studentName.' وزملاؤه'
            : __('tutor.lesson_with', ['student' => $studentName]);

        return ClassroomMeeting::create([
            'user_id' => $booking->instructor_id,
            'lesson_booking_id' => $booking->id,
            'code' => $code,
            'room_name' => \App\Support\PlatformBranding::classroomRoomName($code),
            'title' => $title,
            'scheduled_for' => $booking->scheduled_at,
            'planned_duration_minutes' => $booking->duration_minutes,
            'max_participants' => $this->resolveMaxParticipants($booking),
            'settings' => [
                'lesson_booking_id' => $booking->id,
                'group_session_key' => $booking->group_session_key,
            ],
        ]);
    }

    protected function resolveSharedClassroomMeeting(LessonBooking $booking): ?ClassroomMeeting
    {
        if (! $booking->group_session_key) {
            return null;
        }

        $sibling = LessonBooking::query()
            ->where('group_session_key', $booking->group_session_key)
            ->whereNotNull('classroom_meeting_id')
            ->where('id', '!=', $booking->id)
            ->first();

        return $sibling?->classroomMeeting;
    }

    protected function resolveMaxParticipants(LessonBooking $booking): int
    {
        if ($booking->session_type === StudentLearningProfile::SESSION_SMALL_GROUP || $booking->group_session_key) {
            $size = (int) ($booking->max_group_size ?? 0);
            if ($booking->group_session_key) {
                $count = LessonBooking::query()
                    ->where('group_session_key', $booking->group_session_key)
                    ->whereNotIn('status', [LessonBooking::STATUS_CANCELLED])
                    ->count();
                $size = max($size, $count);
            }

            return max(3, min(30, $size > 0 ? $size + 1 : 7));
        }

        return 2;
    }

    /**
     * @param  string|null  $sessionType  نوع الجلسة المطلوب حجزها
     * @param  int  $seatsNeeded  عدد المقاعد المطلوبة دفعة واحدة (مجموعة جديدة)
     * @param  int|null  $maxGroupSize  الحد الأقصى للمجموعة
     * @param  bool  $requireAvailabilityWindow  التحقق من نوافذ التوفر الأسبوعية
     */
    public function isSlotAvailable(
        int $instructorId,
        Carbon $scheduledAt,
        int $durationMinutes,
        ?string $sessionType = null,
        int $seatsNeeded = 1,
        ?int $maxGroupSize = null,
        bool $requireAvailabilityWindow = true
    ): bool {
        if ($seatsNeeded <= 0) {
            return true;
        }

        if ($requireAvailabilityWindow) {
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
        }

        $endAt = $scheduledAt->copy()->addMinutes($durationMinutes);

        $overlapping = LessonBooking::query()
            ->where('instructor_id', $instructorId)
            ->whereIn('status', [
                LessonBooking::STATUS_PENDING,
                LessonBooking::STATUS_CONFIRMED,
                LessonBooking::STATUS_IN_PROGRESS,
            ])
            ->whereRaw('scheduled_at < ?', [$endAt])
            ->whereRaw('DATE_ADD(scheduled_at, INTERVAL duration_minutes MINUTE) > ?', [$scheduledAt])
            ->get();

        if ($overlapping->isEmpty()) {
            return true;
        }

        $wantsGroup = $sessionType === StudentLearningProfile::SESSION_SMALL_GROUP;
        $hasOneToOne = $overlapping->contains(
            fn (LessonBooking $b) => $b->session_type !== StudentLearningProfile::SESSION_SMALL_GROUP
        );

        // فردي يتعارض مع أي تداخل؛ مجموعة تتعارض مع أي حجز فردي على نفس الشريحة
        if (! $wantsGroup || $hasOneToOne) {
            return false;
        }

        $existingMax = (int) $overlapping->max('max_group_size');
        $cap = $maxGroupSize ?: ($existingMax > 0 ? $existingMax : 5);
        if ($existingMax > 0) {
            $cap = min($cap, $existingMax);
        }

        return ($overlapping->count() + $seatsNeeded) <= $cap;
    }

    public static function bookableInstructorsQuery(?string $matchingMode = null, ?int $subjectId = null)
    {
        $q = InstructorProfile::query()
            ->offersTutorBooking()
            ->whereHas('user', fn ($userQuery) => $userQuery->where('is_active', true))
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
     * معلّمون يظهرون للطالب بعد قبول الإدارة — مستقل عن show_on_homepage.
     */
    public static function studentVisibleInstructorsQuery(?int $subjectId = null)
    {
        $q = InstructorProfile::query()
            ->where('status', InstructorProfile::STATUS_APPROVED)
            ->where(function ($portal) {
                $portal->whereNull('instructor_portal_mode')
                    ->orWhereIn('instructor_portal_mode', [
                        InstructorProfile::PORTAL_TUTOR_LESSONS,
                        InstructorProfile::PORTAL_BOTH,
                    ]);
            })
            ->whereHas('user', fn ($userQuery) => $userQuery->where('is_active', true))
            ->with('user')
            ->orderByDesc('tutor_activated_at')
            ->orderByDesc('id');

        if ($subjectId) {
            $q->where(function ($sub) use ($subjectId) {
                $sub->whereJsonContains('tutor_subject_ids', (int) $subjectId)
                    ->orWhereJsonContains('tutor_subject_ids', (string) $subjectId);
            });
        }

        return $q;
    }

    /**
     * مواعيد متاحة لمعلم واحد من جدوله الأسبوعي (ناقص الحجوزات المتعارضة).
     *
     * @return list<array{scheduled_at: string, value: string, label: string, day_label: string}>
     */
    public function availableSlotsForInstructor(
        int $instructorId,
        int $durationMinutes = 60,
        string $sessionType = StudentLearningProfile::SESSION_ONE_TO_ONE,
        int $seatsNeeded = 1,
        ?int $maxGroupSize = null,
        int $days = 14
    ): array {
        $duration = max(15, min(240, $durationMinutes));
        $step = max(15, (int) (TutorLessonQuotaService::settings()['slot_step_minutes'] ?? 30));
        $days = max(1, min(60, $days));
        $seatsNeeded = max(1, $seatsNeeded);
        $now = now();
        $slots = [];

        for ($d = 0; $d < $days; $d++) {
            $date = $now->copy()->startOfDay()->addDays($d);
            $dayOfWeek = (int) $date->dayOfWeek;

            $windows = TutorAvailability::query()
                ->where('instructor_id', $instructorId)
                ->where('day_of_week', $dayOfWeek)
                ->where('is_active', true)
                ->get();

            foreach ($windows as $window) {
                $cursor = $date->copy()->setTimeFromTimeString(substr((string) $window->start_time, 0, 8));
                $windowEnd = $date->copy()->setTimeFromTimeString(substr((string) $window->end_time, 0, 8));

                while ($cursor->copy()->addMinutes($duration)->lte($windowEnd)) {
                    if ($cursor->gt($now) && $this->isSlotAvailable(
                        $instructorId,
                        $cursor->copy(),
                        $duration,
                        $sessionType,
                        $seatsNeeded,
                        $maxGroupSize,
                        true
                    )) {
                        $key = $cursor->format('Y-m-d\TH:i');
                        $slots[$key] = [
                            'scheduled_at' => $cursor->toIso8601String(),
                            'value' => $key,
                            'label' => $cursor->locale('ar')->translatedFormat('l j F — H:i'),
                            'day_label' => TutorAvailability::dayLabels()[$dayOfWeek] ?? '',
                        ];
                    }
                    $cursor->addMinutes($step);
                }
            }
        }

        ksort($slots);

        return array_values($slots);
    }

    /**
     * مجموعات مفتوحة يمكن إضافة طلاب إليها.
     *
     * @return list<array<string, mixed>>
     */
    public function listOpenGroupSessions(?int $instructorId = null): array
    {
        $query = LessonBooking::query()
            ->with(['instructor', 'subject', 'student'])
            ->whereNotNull('group_session_key')
            ->where('session_type', StudentLearningProfile::SESSION_SMALL_GROUP)
            ->whereIn('status', [
                LessonBooking::STATUS_PENDING,
                LessonBooking::STATUS_CONFIRMED,
            ])
            ->where('scheduled_at', '>', now())
            ->orderBy('scheduled_at');

        if ($instructorId) {
            $query->where('instructor_id', $instructorId);
        }

        $rows = $query->get()->groupBy('group_session_key');
        $out = [];

        foreach ($rows as $key => $bookings) {
            /** @var \Illuminate\Support\Collection<int, LessonBooking> $bookings */
            $first = $bookings->first();
            $cap = max(2, (int) ($bookings->max('max_group_size') ?: 5));
            $taken = $bookings->count();
            if ($taken >= $cap) {
                continue;
            }

            $out[] = [
                'group_session_key' => (string) $key,
                'instructor_id' => (int) $first->instructor_id,
                'instructor_name' => $first->instructor?->name,
                'scheduled_at' => optional($first->scheduled_at)->format('Y-m-d\TH:i'),
                'scheduled_label' => optional($first->scheduled_at)?->locale('ar')->translatedFormat('l j F — H:i'),
                'duration_minutes' => (int) $first->duration_minutes,
                'max_group_size' => $cap,
                'taken' => $taken,
                'seats_left' => $cap - $taken,
                'academic_subject_id' => $first->academic_subject_id,
                'tutor_group_offer_id' => $first->tutor_group_offer_id,
                'subject_name' => $first->subject?->name,
                'student_names' => $bookings->pluck('student.name')->filter()->values()->all(),
                'status' => $first->status,
            ];
        }

        return $out;
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
                        if ($cursor->gt($now) && $this->isSlotAvailable(
                            $instructorProfile->user_id,
                            $cursor,
                            $duration,
                            $profile->preferred_session_type
                        )) {
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
            if (! $this->isSlotAvailable($instructorProfile->user_id, $scheduledAt, $duration, $sessionType)) {
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
