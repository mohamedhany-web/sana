<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $query = $this->inboxQuery();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->status === 'read') {
            $query->where('is_read', true);
        } elseif ($request->status === 'unread') {
            $query->where('is_read', false);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        $notifications = $query
            ->orderBy('is_read')
            ->orderByDesc('priority')
            ->orderByDesc('created_at')
            ->paginate(20);

        $base = $this->inboxQuery();
        $stats = [
            'total' => (clone $base)->count(),
            'unread' => (clone $base)->unread()->count(),
            'today' => (clone $base)->whereDate('created_at', today())->count(),
            'urgent' => (clone $base)->where('priority', 'urgent')->unread()->count(),
        ];

        $notificationTypes = Notification::getTypes();
        $priorities = Notification::getPriorities();

        return view('instructor.notifications.index', compact(
            'notifications',
            'stats',
            'notificationTypes',
            'priorities'
        ));
    }

    public function show(Notification $notification)
    {
        $this->authorizeInboxNotification($notification);

        if (! $notification->is_read) {
            $notification->markAsRead();
        }

        $notification->load(['sender']);

        return view('instructor.notifications.show', compact('notification'));
    }

    public function go(Notification $notification)
    {
        $this->authorizeInboxNotification($notification);

        if (! $notification->is_read) {
            $notification->markAsRead();
        }

        if (empty($notification->action_url)) {
            return redirect()->route('instructor.notifications.show', $notification);
        }

        $url = $notification->action_url;
        $parsed = parse_url($url);
        $path = $parsed['path'] ?? '/';
        $host = $parsed['host'] ?? null;
        $appHost = parse_url((string) config('app.url'), PHP_URL_HOST);

        if ($host && $appHost && strcasecmp($host, $appHost) !== 0) {
            return redirect()->route('instructor.notifications')
                ->with('error', 'رابط غير مسموح');
        }

        if (preg_match('#^/(admin|employee|parent)(/|$)#', $path)) {
            return redirect()->route('instructor.notifications')
                ->with('error', 'رابط غير مسموح للمدرب');
        }

        return redirect()->to($url);
    }

    public function markAsRead(Notification $notification)
    {
        $this->authorizeInboxNotification($notification);
        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        $count = $this->inboxQuery(false)
            ->unread()
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return response()->json([
            'success' => true,
            'message' => "تم تحديد {$count} إشعار كمقروء",
            'count' => $count,
        ]);
    }

    public function destroy(Notification $notification)
    {
        $this->authorizeInboxNotification($notification);
        $notification->delete();

        return response()->json(['success' => true, 'message' => 'تم حذف الإشعار']);
    }

    private function inboxQuery(bool $withSender = true): Builder
    {
        $query = Auth::user()
            ->customNotifications()
            ->where(function ($q) {
                $q->whereNull('audience')
                    ->orWhereIn('audience', ['instructor', 'teacher']);
            })
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            });

        return $withSender ? $query->with(['sender']) : $query;
    }

    private function authorizeInboxNotification(Notification $notification): void
    {
        if ($notification->user_id !== Auth::id()) {
            abort(403, 'غير مصرح لك بعرض هذا الإشعار');
        }

        $audience = $notification->audience;
        if ($audience !== null && ! in_array($audience, ['instructor', 'teacher'], true)) {
            abort(403, 'هذا الإشعار غير موجّه للمدرب');
        }
    }
}
