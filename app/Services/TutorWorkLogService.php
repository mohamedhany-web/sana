<?php

namespace App\Services;

use App\Models\LessonBooking;
use App\Models\TutorWorkLog;
use Carbon\Carbon;

class TutorWorkLogService
{
    public static function recordFromBooking(LessonBooking $booking): void
    {
        $minutes = max(1, (int) $booking->billable_minutes);
        if ($minutes <= 0) {
            return;
        }

        $date = ($booking->completed_at ?? now())->toDateString();

        TutorWorkLog::updateOrCreate(
            [
                'instructor_id' => $booking->instructor_id,
                'work_date' => $date,
                'source' => 'lesson_'.$booking->id,
            ],
            [
                'minutes' => $minutes,
                'notes' => __('tutor.work_log_from_lesson', ['code' => $booking->code]),
            ]
        );
    }

    public static function recordManual(int $instructorId, Carbon $date, int $minutes, ?string $notes = null): TutorWorkLog
    {
        return TutorWorkLog::updateOrCreate(
            [
                'instructor_id' => $instructorId,
                'work_date' => $date->toDateString(),
                'source' => 'manual',
            ],
            [
                'minutes' => max(0, $minutes),
                'notes' => $notes,
            ]
        );
    }

    public static function minutesToday(int $instructorId): int
    {
        return (int) TutorWorkLog::query()
            ->where('instructor_id', $instructorId)
            ->whereDate('work_date', today())
            ->sum('minutes');
    }
}
