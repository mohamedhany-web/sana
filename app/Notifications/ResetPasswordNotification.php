<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends BaseResetPassword
{
    public function toMail($notifiable): MailMessage
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        $expire = (int) config('auth.passwords.'.config('auth.defaults.passwords').'.expire', 60);

        return (new MailMessage)
            ->from(config('mail.from.address', 'info@sanaedu.com'), config('mail.from.name', config('app.name')))
            ->subject('إعادة تعيين كلمة المرور — '.config('app.name', 'Sana'))
            ->view('emails.reset-password', [
                'url' => $url,
                'user' => $notifiable,
                'expire' => $expire,
            ]);
    }
}
