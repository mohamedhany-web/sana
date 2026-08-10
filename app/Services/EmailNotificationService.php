<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EmailNotificationService
{
    /**
     * إرسال رسالة بريد إلكتروني لمستخدم واحد.
     */
    public function sendToUser(User $user, string $message, ?string $subject = null): array
    {
        if (empty($user->email)) {
            return [
                'success' => false,
                'error' => 'لا يوجد بريد إلكتروني مرفوع لهذا المستخدم.',
            ];
        }

        return $this->sendToAddress((string) $user->email, $message, $subject, [
            'user_id' => $user->id,
        ]);
    }

    /**
     * إرسال بريد إلى عنوان مباشر (رد على إشعار/رسالة تواصل).
     */
    public function sendToAddress(string $email, string $message, ?string $subject = null, array $context = []): array
    {
        $email = trim($email);
        if ($email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return [
                'success' => false,
                'error' => 'عنوان البريد الإلكتروني غير صالح.',
            ];
        }

        $subject = $subject ?: 'رسالة من منصة ' . config('app.name', 'Sana');

        try {
            Mail::raw($message, function ($mail) use ($email, $subject) {
                $mail->to($email)->subject($subject);
            });

            return ['success' => true];
        } catch (\Throwable $e) {
            Log::error('Email send failed', array_merge($context, [
                'email' => $email,
                'error' => $e->getMessage(),
            ]));

            return [
                'success' => false,
                'error' => 'تعذر إرسال البريد الإلكتروني: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * إرسال رسالة بريدية جماعية.
     *
     * @param iterable<User> $users
     */
    public function sendBulk(iterable $users, string $message, ?string $subject = null): array
    {
        $success = 0;
        $failed = 0;

        foreach ($users as $user) {
            $result = $this->sendToUser($user, $message, $subject);
            if ($result['success']) {
                $success++;
            } else {
                $failed++;
            }
        }

        return [
            'success' => $success,
            'failed' => $failed,
        ];
    }
}

