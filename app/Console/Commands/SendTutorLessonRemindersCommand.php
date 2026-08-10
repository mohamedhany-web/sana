<?php

namespace App\Console\Commands;

use App\Models\LessonBooking;
use App\Services\TutorNotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendTutorLessonRemindersCommand extends Command
{
    protected $signature = 'tutor:send-lesson-reminders {--minutes=5 : Minutes before lesson to send reminder}';

    protected $description = 'Send email + in-app reminders to student, instructor, and parent before tutor lessons';

    public function handle(): int
    {
        $minutes = max(1, (int) $this->option('minutes'));
        $windowStart = now()->addMinutes($minutes - 1);
        $windowEnd = now()->addMinutes($minutes + 1);

        $bookings = LessonBooking::query()
            ->whereIn('status', [LessonBooking::STATUS_CONFIRMED, LessonBooking::STATUS_IN_PROGRESS])
            ->whereNull('reminder_sent_at')
            ->whereBetween('scheduled_at', [$windowStart, $windowEnd])
            ->with(['student', 'instructor', 'parent'])
            ->get();

        if ($bookings->isEmpty()) {
            $this->info("No tutor lessons starting in ~{$minutes} minutes.");

            return self::SUCCESS;
        }

        $sent = 0;

        foreach ($bookings as $booking) {
            try {
                TutorNotificationService::sendScheduledLessonReminder($booking, $minutes);
                $booking->update(['reminder_sent_at' => now()]);
                $sent++;
            } catch (\Throwable $e) {
                Log::warning('tutor lesson reminder failed', [
                    'booking_id' => $booking->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->info("Sent reminders for {$sent} tutor lesson(s).");

        return self::SUCCESS;
    }
}
