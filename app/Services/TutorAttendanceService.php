<?php

namespace App\Services;

use App\Models\ClassroomMeeting;
use App\Models\ClassroomMeetingParticipant;
use App\Models\LessonBooking;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class TutorAttendanceService
{
    public function handleParticipantJoined(ClassroomMeeting $meeting, ClassroomMeetingParticipant $participant): void
    {
        foreach ($this->bookingsForMeeting($meeting) as $booking) {
            if ($booking->status === LessonBooking::STATUS_CONFIRMED) {
                $booking->update(['status' => LessonBooking::STATUS_IN_PROGRESS]);
            }
            $this->evaluateCoPresence($booking->fresh(), $meeting);
        }
    }

    public function handleParticipantLeft(ClassroomMeeting $meeting, ClassroomMeetingParticipant $participant): void
    {
        foreach ($this->bookingsForMeeting($meeting) as $booking) {
            if ($booking->co_presence_started_at) {
                $this->closeCoPresenceSegment($booking);
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

    public function evaluateCoPresence(LessonBooking $booking, ClassroomMeeting $meeting): void
    {
        $instructorActive = $this->hasActiveRole($meeting->id, 'instructor')
            || ($meeting->started_at && ! $meeting->ended_at && (int) $meeting->user_id === (int) $booking->instructor_id);
        $studentActive = $this->hasActiveRole($meeting->id, 'student');

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

    public function closeCoPresenceSegment(LessonBooking $booking): void
    {
        if (! $booking->co_presence_started_at) {
            return;
        }

        $start = $booking->co_presence_started_at;
        $end = now();
        $segmentMinutes = max(0, (int) $start->diffInMinutes($end));

        $booking->update([
            'billable_minutes' => (int) $booking->billable_minutes + $segmentMinutes,
            'co_presence_started_at' => null,
            'co_presence_ended_at' => $end,
        ]);
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

            if ($booking->co_presence_started_at) {
                $this->closeCoPresenceSegment($booking);
                $booking->refresh();
            }

            $minutes = (int) $booking->billable_minutes;
            if ($minutes <= 0) {
                $minutes = $this->fallbackBillableMinutes($booking, $meeting);
                if ($minutes > 0) {
                    $booking->update(['billable_minutes' => $minutes]);
                    $booking->refresh();
                }
            }

            if (in_array($booking->status, [
                LessonBooking::STATUS_CONFIRMED,
                LessonBooking::STATUS_IN_PROGRESS,
            ], true)) {
                $service->complete($booking->fresh());
            }
        }
    }

    private function fallbackBillableMinutes(LessonBooking $booking, ClassroomMeeting $meeting): int
    {
        $planned = max(1, (int) ($booking->duration_minutes ?: $meeting->planned_duration_minutes ?: 60));

        if ($meeting->started_at) {
            $end = $meeting->ended_at ?? now();
            $elapsed = max(1, (int) $meeting->started_at->diffInMinutes($end));

            return min($planned, $elapsed);
        }

        // جلسة مؤكدة وانتهت بدون تتبع حضور — خصم مدة الحجز المخططة
        return $planned;
    }

    /** @return Collection<int, LessonBooking> */
    private function bookingsForMeeting(ClassroomMeeting $meeting): Collection
    {
        $query = LessonBooking::query()->where(function ($q) use ($meeting) {
            $q->where('classroom_meeting_id', $meeting->id);
            if ($meeting->lesson_booking_id) {
                $q->orWhere('id', $meeting->lesson_booking_id);
            }
        });

        return $query->get()->unique('id')->values();
    }

    private function hasActiveRole(int $meetingId, string $role): bool
    {
        return ClassroomMeetingParticipant::query()
            ->where('classroom_meeting_id', $meetingId)
            ->where('participant_role', $role)
            ->whereNull('left_at')
            ->where('last_seen_at', '>=', now()->subMinutes(5))
            ->exists();
    }

    public static function inferRole(?int $userId, ClassroomMeeting $meeting): ?string
    {
        if (! $userId) {
            return 'guest';
        }
        if ((int) $meeting->user_id === (int) $userId) {
            return 'instructor';
        }
        $booking = LessonBooking::where('classroom_meeting_id', $meeting->id)->first();
        if ($booking && (int) $booking->student_id === (int) $userId) {
            return 'student';
        }

        return 'guest';
    }
}
