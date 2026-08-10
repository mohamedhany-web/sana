<?php

namespace App\Notifications;

use App\Models\LessonBooking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TutorLessonReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public LessonBooking $booking,
        public int $minutesBefore = 5,
        public string $audience = 'student'
    ) {}

    public function via(object $notifiable): array
    {
        // الإشعار داخل المنصة يُرسل عبر TutorNotificationService — هنا الإيميل فقط
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $when = $this->booking->scheduled_at?->timezone(config('app.timezone'))->format('Y-m-d H:i') ?? '—';
        $teacher = $this->booking->instructor?->name ?? 'المعلم';
        $student = $this->booking->student?->name ?? 'الطالب';
        $url = $this->actionUrl();

        $mail = (new MailMessage)
            ->subject("تذكير: حصتك تبدأ بعد {$this->minutesBefore} دقائق")
            ->greeting("مرحباً {$notifiable->name}");

        if ($this->audience === 'instructor') {
            $mail->line("حصتك مع الطالب **{$student}** تبدأ بعد {$this->minutesBefore} دقائق.")
                ->line("الموعد: {$when}")
                ->action('دخول الحصة', $url)
                ->line('يرجى الدخول في الموعد المحدد.');
        } elseif ($this->audience === 'parent') {
            $mail->line("تذكير: حصة **{$student}** مع المعلم **{$teacher}** تبدأ بعد {$this->minutesBefore} دقائق.")
                ->line("الموعد: {$when}")
                ->action('عرض الحجز', $url);
        } else {
            $mail->line("حصتك مع المعلم **{$teacher}** تبدأ بعد {$this->minutesBefore} دقائق.")
                ->line("الموعد: {$when}")
                ->action('دخول الحصة', $url)
                ->line('جهّز نفسك وادخل في الوقت المحدد.');
        }

        return $mail;
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'tutor_lesson_reminder',
            'booking_id' => $this->booking->id,
            'booking_code' => $this->booking->code,
            'audience' => $this->audience,
            'minutes_before' => $this->minutesBefore,
            'scheduled_at' => $this->booking->scheduled_at?->toISOString(),
            'message' => "حصة #{$this->booking->code} تبدأ بعد {$this->minutesBefore} دقائق",
            'url' => $this->actionUrl(),
        ];
    }

    private function actionUrl(): string
    {
        return match ($this->audience) {
            'instructor' => url('/instructor/tutor-lessons/bookings/'.$this->booking->id),
            'parent' => url('/parent/tutor-lessons/bookings/'.$this->booking->id),
            default => url('/tutor-lessons/bookings/'.$this->booking->id),
        };
    }
}
