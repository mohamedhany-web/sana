<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InstructorAccountActivatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public ?string $adminNote = null
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'تم تفعيل حسابك كمعلم في '.config('app.name', 'أكاديمية سنا'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.instructor-account-activated',
        );
    }
}
