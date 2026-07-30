<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TutorApplicationReceivedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $user) {}

    public function envelope(): Envelope
    {
        $from = config('mail.from.address', 'info@sanaedu.com');
        $name = config('mail.from.name', config('app.name', 'Sana'));

        return new Envelope(
            from: new Address($from, $name),
            replyTo: [new Address($from, $name)],
            subject: 'تم استلام بياناتك — '.config('app.name', 'Sana'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.tutor-application-received',
        );
    }
}
