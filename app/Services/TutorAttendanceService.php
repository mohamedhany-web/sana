<?php

namespace App\Services;

use App\Models\ClassroomMeeting;
use App\Models\ClassroomMeetingParticipant;
use App\Models\LessonBooking;
use Carbon\Carbon;

class TutorAttendanceService
{
    public function handleParticipantJoined(ClassroomMeeting $meeting, ClassroomMeetingParticipant $participant): void
    {
        $booking = $this->bookingForMeeting($meeting);
        if (! $booking) {
            return;
        }

        if ($booking->status === LessonBooking::STATUS_CONFIRMED) {
            $booking->update(['status' => LessonBooking::STATUS_IN_PROGRESS]);
        }

        $this->evaluateCoPresence($booking, $meeting);
    }

    public function handleParticipantLeft(ClassroomMeeting $meeting, ClassroomMeetingParticipant $participant): void
    {
        $booking = $this->bookingForMeeting($meeting);
        if (! $booking) {
            return;
        }

        if ($participant->participant_role === 'student' && $booking->co_presence_started_at) {
            $this->closeCoPresenceSegment($booking);
        }

        $this->evaluateCoPresence($booking, $meeting);
    }

    public function evaluateCoPresence(LessonBooking $booking, ClassroomMeeting $meeting): void
    {
        $instructorActive = $this->hasActiveRole($meeting->id, 'instructor');
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

        if ($booking->co_presence_started_at && ! $booking->co_presence_ended_at && ! $studentActive) {
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
        $booking = $this->bookingForMeeting($meeting);
        if (! $booking || $booking->status === LessonBooking::STATUS_COMPLETED) {
            return;
        }

        if ($booking->co_presence_started_at) {
            $this->closeCoPresenceSegment($booking);
        }

        if ($booking->fresh()->billable_minutes > 0) {
            app(LessonBookingService::class)->complete($booking->fresh());
        }
    }

    private function bookingForMeeting(ClassroomMeeting $meeting): ?LessonBooking
    {
        if ($meeting->lesson_booking_id) {
            return LessonBooking::find($meeting->lesson_booking_id);
        }

        return LessonBooking::where('classroom_meeting_id', $meeting->id)->first();
    }

    private function hasActiveRole(int $meetingId, string $role): bool
    {
        return ClassroomMeetingParticipant::query()
            ->where('classroom_meeting_id', $meetingId)
            ->where('participant_role', $role)
            ->whereNull('left_at')
            ->where('last_seen_at', '>=', now()->subMinutes(2))
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
