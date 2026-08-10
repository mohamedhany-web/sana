<?php

namespace App\Services;

use App\Models\ContactMessage;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ContactMessageAlertService
{
    /**
     * إشعار مستخدمي لوحة الإدارة المناسبين برسالة تواصل جديدة من صفحة /contact.
     */
    public function notifyAdmins(ContactMessage $contactMessage): void
    {
        $recipientIds = $this->recipientUserIds();
        if ($recipientIds === []) {
            Log::warning('Contact message stored but no admin recipients for in-app notification.', [
                'contact_message_id' => $contactMessage->id,
                'hint' => 'تأكد من وجود مستخدمين نشطين بدور admin أو super_admin أو لديهم صلاحية manage.contact-messages',
            ]);

            return;
        }

        $title = 'رسالة تواصل جديدة';
        $preview = Str::limit(strip_tags((string) $contactMessage->message), 160);
        $message = $contactMessage->name.' — '.$contactMessage->subject;
        if ($preview !== '') {
            $message .= "\n".$preview;
        }

        $actionUrl = route('admin.contact-messages.show', $contactMessage);

        foreach ($recipientIds as $userId) {
            Notification::create([
                'user_id' => $userId,
                'sender_id' => null,
                'title' => $title,
                'message' => $message,
                'type' => 'system',
                'priority' => 'normal',
                'action_url' => $actionUrl,
                'action_text' => 'عرض الرسالة',
                'target_type' => 'individual',
                'target_id' => $contactMessage->id,
                'audience' => 'admin',
                'is_read' => false,
                'data' => [
                    'contact_message_id' => $contactMessage->id,
                    'reply_email' => $contactMessage->email,
                    'reply_name' => $contactMessage->name,
                    'source' => 'contact_page',
                ],
            ]);
        }
    }

    /**
     * @return list<int>
     */
    private function recipientUserIds(): array
    {
        return User::query()
            ->where('is_active', true)
            ->get()
            ->filter(function (User $u) {
                if (in_array((string) $u->role, ['super_admin', 'admin'], true)) {
                    return true;
                }

                return $u->hasPermission('manage.contact-messages');
            })
            ->pluck('id')
            ->unique()
            ->values()
            ->all();
    }
}
