<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminCenterNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $subjectLine,
        public string $body,
        public ?string $recipientName = null,
        public ?string $actionUrl = null,
        public ?string $actionText = null,
    ) {}

    public function envelope(): Envelope
    {
        $fromAddress = (string) config('mail.from.address', 'info@sanaedu.com');
        $fromName = (string) config('mail.from.name', config('app.name', 'Sana'));

        return new Envelope(
            subject: $this->subjectLine.' — '.config('app.name', 'Sana'),
            from: new Address($fromAddress, $fromName),
            replyTo: [new Address($fromAddress, $fromName)],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin-center-notification',
        );
    }
}
