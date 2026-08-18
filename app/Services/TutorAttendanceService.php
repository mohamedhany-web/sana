<?php

namespace App\Services;

use App\Models\ClassroomMeeting;
use App\Models\ClassroomMeetingParticipant;
use App\Models\LessonBooking;
use App\Services\LiveKit\LiveKitRoomService;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class TutorAttendanceService
{
    public const PRESENCE_GRACE_SECONDS = 70;

    /** مهلة قصيرة بعد آخر نبضة حتى لا يُحسب وقت بعد ما الشخص يقفل الصفحة. */
    public const BILLING_TAIL_SECONDS = 15;

    public function handleParticipantJoined(ClassroomMeeting $meeting, ClassroomMeetingParticipant $participant): void
    {
        $this->expireStaleParticipants($meeting);
        foreach ($this->bookingsForMeeting($meeting) as $booking) {
            if ($booking->status === LessonBooking::STATUS_CONFIRMED) {
                $booking->update(['status' => LessonBooking::STATUS_IN_PROGRESS]);
            }
            $this->evaluateCoPresence($booking->fresh(), $meeting);
        }

        if (LessonMeetingAccess::isLessonMeeting($meeting)) {
            app(LessonRecordingService::class)->startForMeeting($meeting->fresh());
        }
    }

    public function handleParticipantLeft(ClassroomMeeting $meeting, ClassroomMeetingParticipant $participant): void
    {
        $endedAt = $participant->left_at ?? $participant->last_seen_at ?? now();
        foreach ($this->bookingsForMeeting($meeting) as $booking) {
            if ($booking->co_presence_started_at) {
                $this->closeCoPresenceSegment($booking, $endedAt);
            }
            $this->evaluateCoPresence($booking->fresh(), $meeting);
        }
    }

    public function ensureInstructorPresence(ClassroomMeeting $meeting, $user): void
    {
        if (! $user) {
            return;
        }

        if (! $meeting->started_at) {
            $meeting->update(['started_at' => now()]);
            $meeting->refresh();
        }

        $existing = ClassroomMeetingParticipant::query()
            ->where('classroom_meeting_id', $meeting->id)
            ->where('user_id', $user->id)
            ->where('participant_role', 'instructor')
            ->whereNull('left_at')
            ->latest('id')
            ->first();

        if ($existing) {
            $existing->update(['last_seen_at' => now()]);
            $participant = $existing;
        } else {
            $participant = ClassroomMeetingParticipant::create([
                'classroom_meeting_id' => $meeting->id,
                'user_id' => $user->id,
                'participant_role' => 'instructor',
                'token' => Str::random(48),
                'display_name' => $user->name,
                'joined_at' => now(),
                'last_seen_at' => now(),
            ]);
        }

        $this->handleParticipantJoined($meeting->fresh(), $participant);
    }

    public function touchInstructorHeartbeat(ClassroomMeeting $meeting, $user): array
    {
        $this->ensureInstructorPresence($meeting, $user);
        $this->expireStaleParticipants($meeting->fresh());
        foreach ($this->bookingsForMeeting($meeting) as $booking) {
            $this->evaluateCoPresence($booking->fresh(), $meeting);
        }

        return $this->presenceSnapshot($meeting->fresh());
    }

    public function markInstructorLeft(ClassroomMeeting $meeting, $user): void
    {
        if (! $user) {
            return;
        }

        $rows = ClassroomMeetingParticipant::query()
            ->where('classroom_meeting_id', $meeting->id)
            ->where('user_id', $user->id)
            ->whereNull('left_at')
            ->get();

        foreach ($rows as $row) {
            $row->update(['left_at' => now(), 'last_seen_at' => now()]);
            $this->handleParticipantLeft($meeting, $row);
        }
    }

    public function evaluateCoPresence(LessonBooking $booking, ClassroomMeeting $meeting): void
    {
        $instructorActive = $this->hasActiveRole($meeting->id, 'instructor', $booking->instructor_id);
        $studentActive = $this->hasActiveStudent($meeting->id, $booking->student_id);

        if ($instructorActive && $studentActive) {
            if (! $booking->co_presence_started_at) {
                $booking->update([
                    'co_presence_started_at' => now(),
                    'status' => LessonBooking::STATUS_IN_PROGRESS,
                ]);
            }

            return;
        }

        if ($booking->co_presence_started_at && (! $studentActive || ! $instructorActive)) {
            $this->closeCoPresenceSegment($booking);
        }
    }

    public function closeCoPresenceSegment(LessonBooking $booking, ?CarbonInterface $endedAt = null): void
    {
        if (! $booking->co_presence_started_at) {
            return;
        }

        $start = $booking->co_presence_started_at;
        $end = $endedAt ?? now();
        if ($end->lt($start)) {
            $end = $start;
        }
        $seconds = max(0, abs((int) $start->diffInSeconds($end)));
        $payload = [
            'co_presence_started_at' => null,
            'co_presence_ended_at' => $end,
        ];

        if (Schema::hasColumn('lesson_bookings', 'billable_seconds')) {
            $totalSeconds = (int) $booking->billable_seconds + $seconds;
            $payload['billable_seconds'] = $totalSeconds;
            $payload['billable_minutes'] = self::secondsToMinutes($totalSeconds);
        } else {
            $payload['billable_minutes'] = (int) $booking->billable_minutes + self::secondsToMinutes($seconds);
        }

        $booking->update($payload);
    }

    /**
     * دقائق الحضور المشترك الفعلية بين هذا الطالب وهذا المعلم في غرفة اللايف.
     */
    public function resolveBillableMinutes(LessonBooking $booking): int
    {
        return self::secondsToMinutes($this->resolveBillableSeconds($booking));
    }

    public function resolveBillableSeconds(LessonBooking $booking): int
    {
        $booking->loadMissing('classroomMeeting');
        $meeting = $booking->classroomMeeting;

        if ($booking->co_presence_started_at) {
            $this->closeCoPresenceSegment($booking);
            $booking->refresh();
        }

        $fromSegments = Schema::hasColumn('lesson_bookings', 'billable_seconds')
            ? (int) $booking->billable_seconds
            : ((int) $booking->billable_minutes * 60);

        $fromOverlap = 0;
        if ($meeting && Schema::hasTable('classroom_meeting_participants')) {
            $fromOverlap = $this->overlapSecondsFromParticipants($meeting, $booking);
        }

        $seconds = max($fromSegments, $fromOverlap);
        $planned = max(1, (int) ($booking->duration_minutes ?: 60)) * 60;

        return min($planned, max(0, $seconds));
    }

    public static function secondsToMinutes(int $seconds): int
    {
        if ($seconds <= 0) {
            return 0;
        }

        return (int) round($seconds / 60);
    }

    public function endMeetingAndSync(ClassroomMeeting $meeting): void
    {
        $settings = is_array($meeting->settings) ? $meeting->settings : [];
        $settings['host_ended'] = true;
        $payload = ['settings' => $settings];
        if (! $meeting->ended_at) {
            $payload['ended_at'] = now();
        }
        $meeting->update($payload);
        $meeting->refresh();

        $this->syncOnMeetingEnd($meeting);
        if (LessonMeetingAccess::isLessonMeeting($meeting)) {
            app(LessonRecordingService::class)->stopForMeeting($meeting);
        }

        ClassroomMeetingParticipant::query()
            ->where('classroom_meeting_id', $meeting->id)
            ->whereNull('left_at')
            ->get()
            ->each(function (ClassroomMeetingParticipant $row) {
                $ended = $row->last_seen_at ?? now();
                $row->update([
                    'left_at' => $ended,
                    'last_seen_at' => $ended,
                ]);
            });

        app(LiveKitRoomService::class)->deleteForMeeting($meeting);
    }

    public function syncOnMeetingEnd(ClassroomMeeting $meeting): void
    {
        $bookings = $this->bookingsForMeeting($meeting);
        if ($bookings->isEmpty()) {
            return;
        }

        $service = app(LessonBookingService::class);

        foreach ($bookings as $booking) {
            if ($booking->status === LessonBooking::STATUS_COMPLETED
                || $booking->status === LessonBooking::STATUS_CANCELLED) {
                continue;
            }

            if (in_array($booking->status, [
                LessonBooking::STATUS_PENDING,
                LessonBooking::STATUS_CONFIRMED,
                LessonBooking::STATUS_IN_PROGRESS,
            ], true)) {
                $service->complete($booking->fresh());
            }
        }
    }

    public function presenceSnapshot(ClassroomMeeting $meeting, ?int $forStudentId = null): array
    {
        $this->expireStaleParticipants($meeting);
        $bookings = $this->bookingsForMeeting($meeting);
        $booking = $forStudentId
            ? ($bookings->firstWhere('student_id', $forStudentId) ?: $bookings->first())
            : $bookings->first();
        $teacherPresent = $booking
            ? $this->hasActiveRole($meeting->id, 'instructor', (int) $booking->instructor_id)
            : $this->hasActiveRole($meeting->id, 'instructor', (int) $meeting->user_id);
        $studentPresent = false;
        if ($forStudentId && $booking) {
            $studentPresent = $this->hasActiveStudent($meeting->id, (int) $booking->student_id);
        } elseif ($bookings->isNotEmpty()) {
            $studentPresent = $bookings->contains(
                fn (LessonBooking $b) => $this->hasActiveStudent($meeting->id, (int) $b->student_id)
            );
            if (! $booking) {
                $booking = $bookings->first();
            }
        }
        $billedRunning = $teacherPresent && $studentPresent;
        $openSeconds = 0;
        if ($booking?->co_presence_started_at) {
            $openSeconds = max(0, abs((int) $booking->co_presence_started_at->diffInSeconds(now())));
        }
        $storedSeconds = $booking
            ? (int) ($booking->billable_seconds ?? ((int) ($booking->billable_minutes ?? 0) * 60))
            : 0;
        $billedSeconds = $storedSeconds + ($billedRunning ? $openSeconds : 0);
        $planned = max(1, (int) ($booking->duration_minutes ?? $meeting->planned_duration_minutes ?? 60)) * 60;

        $people = ClassroomMeetingParticipant::query()
            ->where('classroom_meeting_id', $meeting->id)
            ->whereNull('left_at')
            ->where('last_seen_at', '>=', now()->subSeconds(self::PRESENCE_GRACE_SECONDS))
            ->get(['user_id', 'participant_role', 'display_name'])
            ->map(fn ($p) => [
                'user_id' => $p->user_id,
                'role' => $p->participant_role ?: 'guest',
                'name' => $p->display_name,
            ])
            ->values()
            ->all();

        return [
            'teacher_present' => $teacherPresent,
            'student_present' => $studentPresent,
            'billed_running' => $billedRunning,
            'billed_seconds' => $billedSeconds,
            'planned_seconds' => $planned,
            'active_participants' => count($people),
            'participants' => $people,
        ];
    }

    public function expireStaleParticipants(ClassroomMeeting $meeting): void
    {
        $cutoff = now()->subSeconds(self::PRESENCE_GRACE_SECONDS);
        $stale = ClassroomMeetingParticipant::query()
            ->where('classroom_meeting_id', $meeting->id)
            ->whereNull('left_at')
            ->where(function ($q) use ($cutoff) {
                $q->whereNull('last_seen_at')->orWhere('last_seen_at', '<', $cutoff);
            })
            ->get();

        foreach ($stale as $row) {
            $ended = $row->last_seen_at ?? now();
            $row->update(['left_at' => $ended]);
            $this->handleParticipantLeft($meeting, $row);
        }
    }

    public function expireAllStale(): int
    {
        $cutoff = now()->subSeconds(self::PRESENCE_GRACE_SECONDS);
        $meetingIds = ClassroomMeetingParticipant::query()
            ->whereNull('left_at')
            ->where(function ($q) use ($cutoff) {
                $q->whereNull('last_seen_at')->orWhere('last_seen_at', '<', $cutoff);
            })
            ->distinct()
            ->pluck('classroom_meeting_id');

        $count = 0;
        foreach ($meetingIds as $id) {
            $meeting = ClassroomMeeting::find($id);
            if ($meeting && ! $meeting->ended_at) {
                $this->expireStaleParticipants($meeting);
                $count++;
            }
        }

        return $count;
    }

    /** @return Collection<int, LessonBooking> */
    public function bookingsForMeeting(ClassroomMeeting $meeting): Collection
    {
        $query = LessonBooking::query()->where(function ($q) use ($meeting) {
            $q->where('classroom_meeting_id', $meeting->id);
            if ($meeting->lesson_booking_id) {
                $q->orWhere('id', $meeting->lesson_booking_id);
            }
        });

        return $query->get()->unique('id')->values();
    }

    private function hasActiveRole(int $meetingId, string $role, ?int $userId = null): bool
    {
        $query = ClassroomMeetingParticipant::query()
            ->where('classroom_meeting_id', $meetingId)
            ->where('participant_role', $role)
            ->whereNull('left_at')
            ->where('last_seen_at', '>=', now()->subSeconds(self::PRESENCE_GRACE_SECONDS));

        if ($userId) {
            $query->where('user_id', $userId);
        }

        return $query->exists();
    }

    private function hasActiveStudent(int $meetingId, int $studentId): bool
    {
        return ClassroomMeetingParticipant::query()
            ->where('classroom_meeting_id', $meetingId)
            ->where('user_id', $studentId)
            ->whereNull('left_at')
            ->where('last_seen_at', '>=', now()->subSeconds(self::PRESENCE_GRACE_SECONDS))
            ->exists();
    }

    private function overlapSecondsFromParticipants(ClassroomMeeting $meeting, LessonBooking $booking): int
    {
        $now = $meeting->ended_at ?? now();
        $instructorIntervals = $this->participantIntervals($meeting->id, $booking->instructor_id, 'instructor', $now);
        $studentIntervals = $this->participantIntervals($meeting->id, $booking->student_id, 'student', $now);

        if ($instructorIntervals === [] || $studentIntervals === []) {
            return 0;
        }

        $seconds = 0;
        foreach ($instructorIntervals as [$iStart, $iEnd]) {
            foreach ($studentIntervals as [$sStart, $sEnd]) {
                $start = $iStart->greaterThan($sStart) ? $iStart : $sStart;
                $end = $iEnd->lessThan($sEnd) ? $iEnd : $sEnd;
                if ($end->gt($start)) {
                    $seconds += abs((int) $start->diffInSeconds($end));
                }
            }
        }

        return max(0, $seconds);
    }

    /**
     * @return list<array{0: CarbonInterface, 1: CarbonInterface}>
     */
    private function participantIntervals(int $meetingId, int $userId, string $role, CarbonInterface $openEnd): array
    {
        $rows = ClassroomMeetingParticipant::query()
            ->where('classroom_meeting_id', $meetingId)
            ->where(function ($q) use ($userId, $role) {
                $q->where('user_id', $userId)
                    ->orWhere(function ($inner) use ($userId, $role) {
                        $inner->where('participant_role', $role)->where('user_id', $userId);
                    });
            })
            ->orderBy('joined_at')
            ->get();

        $intervals = [];
        foreach ($rows as $row) {
            $from = $row->joined_at ?? $row->created_at;
            $to = $row->left_at ?? $openEnd;
            if ($row->last_seen_at && $row->last_seen_at->lt($to)) {
                $billedTo = $row->last_seen_at->copy()->addSeconds(self::BILLING_TAIL_SECONDS);
                if ($billedTo->lt($to)) {
                    $to = $billedTo;
                }
            }
            if ($from && $to && $to->gt($from)) {
                $intervals[] = [$from, $to];
            }
        }

        return $intervals;
    }

    public static function inferRole(?int $userId, ClassroomMeeting $meeting): ?string
    {
        if (! $userId) {
            return 'guest';
        }
        if ((int) $meeting->user_id === (int) $userId) {
            return 'instructor';
        }

        $isBookedStudent = LessonBooking::query()
            ->where(function ($q) use ($meeting) {
                $q->where('classroom_meeting_id', $meeting->id);
                if ($meeting->lesson_booking_id) {
                    $q->orWhere('id', $meeting->lesson_booking_id);
                }
            })
            ->where('student_id', $userId)
            ->exists();

        if ($isBookedStudent) {
            return 'student';
        }

        return 'guest';
    }
}
