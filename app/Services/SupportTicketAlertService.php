<?php

namespace App\Services;

use App\Mail\SupportTicketNewMail;
use App\Models\Notification;
use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SupportTicketAlertService
{
    /**
     * إشعار المشرفين الذين يملكون صلاحية تذاكر الدعم + بريد تنبيه (Gmail/SMTP).
     */
    public function notifyNewTicket(SupportTicket $ticket): void
    {
        $ticket->loadMissing(['user', 'inquiryCategory']);

        $recipientIds = $this->supportStaffUserIds();
        if ($recipientIds === []) {
            Log::warning('Support ticket created but no admin recipients for in-app notification.', [
                'ticket_id' => $ticket->id,
                'hint' => 'تأكد من وجود مستخدمين نشطين بدور admin أو super_admin أو لديهم صلاحية manage.support-tickets',
            ]);
        }
        if ($recipientIds !== []) {
            $studentName = $ticket->user?->name ?? 'طالب';
            $title = 'تذكرة دعم فني جديدة';
            $message = $studentName.' — '.$ticket->subject;

            foreach ($recipientIds as $userId) {
                $notification = Notification::create([
                    'user_id' => $userId,
                    'sender_id' => $ticket->user_id,
                    'title' => $title,
                    'message' => $message,
                    'type' => 'system',
                    'priority' => $this->mapPriority($ticket->priority),
                    'action_url' => null,
                    'action_text' => 'فتح التذكرة',
                    'target_type' => 'individual',
                    'target_id' => $ticket->id,
                    'audience' => 'admin',
                    'is_read' => false,
                    'data' => ['support_ticket_id' => $ticket->id],
                ]);
                $notification->update([
                    'action_url' => route('admin.notifications.open-support-ticket', $notification),
                ]);
            }
        }

        $this->sendSupportEmail($ticket);
    }

    /**
     * إشعار الطالب داخل المنصة عند رد فريق الدعم.
     */
    public function notifyStudentOfAdminReply(SupportTicket $ticket, string $replyPreview): void
    {
        $ticket->loadMissing('user');
        $student = $ticket->user;
        if (! $student || ! $student->isStudent()) {
            return;
        }

        $preview = mb_strlen($replyPreview) > 120
            ? mb_substr($replyPreview, 0, 120).'…'
            : $replyPreview;

        try {
            Notification::create([
                'user_id' => $student->id,
                'sender_id' => auth()->id(),
                'title' => 'رد جديد على تذكرة الدعم',
                'message' => $ticket->subject.' — '.$preview,
                'type' => 'system',
                'priority' => $this->mapPriority($ticket->priority),
                'action_url' => route('student.support.show', $ticket),
                'action_text' => 'عرض التذكرة',
                'target_type' => 'individual',
                'target_id' => $ticket->id,
                'audience' => 'student',
                'is_read' => false,
                'data' => ['support_ticket_id' => $ticket->id],
            ]);
        } catch (\Throwable $e) {
            Log::error('Support ticket student notification failed: '.$e->getMessage(), [
                'ticket_id' => $ticket->id,
                'student_id' => $student->id,
            ]);
        }
    }

    /**
     * مستخدمو لوحة الإدارة الذين يصلهم تنبيه التذكرة:
     * - role = admin أو super_admin (نشط)
     * - أو صلاحية manage.support-tickets
     * - أو موظف (is_employee) بدور RBAC ولديه admin.access (يدخل لوحة الإدارة)
     *
     * @return list<int>
     */
    private function supportStaffUserIds(): array
    {
        return User::query()
            ->where('is_active', true)
            ->get()
            ->filter(function (User $u) {
                if (in_array((string) $u->role, ['super_admin', 'admin'], true)) {
                    return true;
                }
                if ($u->hasPermission('manage.support-tickets')) {
                    return true;
                }
                if ($u->is_employee && $u->roles()->exists() && $u->hasPermission('admin.access')) {
                    return true;
                }

                return false;
            })
            ->pluck('id')
            ->unique()
            ->values()
            ->all();
    }

    private function mapPriority(string $priority): string
    {
        return match ($priority) {
            'urgent' => 'urgent',
            'high' => 'high',
            'low' => 'low',
            default => 'normal',
        };
    }

    private function sendSupportEmail(SupportTicket $ticket): void
    {
        $addresses = config('mail.support_alert_addresses', []);
        if (! is_array($addresses) || $addresses === []) {
            return;
        }

        try {
            Mail::to($addresses)->send(new SupportTicketNewMail($ticket));
        } catch (\Throwable $e) {
            Log::error('Support ticket email alert failed: '.$e->getMessage(), [
                'ticket_id' => $ticket->id,
            ]);
        }
    }
}
