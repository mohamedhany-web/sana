<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\SupportTicketReply;
use App\Services\SupportTicketAlertService;
use App\Support\SearchInput;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SupportTicketController extends Controller
{
    public function index(Request $request)
    {
        $status = (string) $request->get('status', 'all');
        $priority = (string) $request->get('priority', 'all');
        $categoryId = $request->get('category_id');
        $view = (string) $request->get('view', 'needs_reply');
        $search = SearchInput::sanitizeForLike((string) $request->get('search', ''));

        $query = SupportTicket::query()
            ->with(['user', 'assignedAdmin', 'inquiryCategory', 'latestReply'])
            ->fromStudents()
            ->latest('last_reply_at')
            ->latest();

        if (in_array($status, ['open', 'in_progress', 'resolved', 'closed'], true)) {
            $query->where('status', $status);
        }
        if (in_array($priority, ['low', 'normal', 'high', 'urgent'], true)) {
            $query->where('priority', $priority);
        }
        if ($categoryId !== null && $categoryId !== '' && $categoryId !== 'all') {
            $query->where('support_inquiry_category_id', (int) $categoryId);
        }

        if ($view === 'needs_reply') {
            $query->awaitingAdminResponse();
        } elseif ($view === 'active') {
            $query->whereIn('status', ['open', 'in_progress']);
        } elseif ($view === 'closed') {
            $query->whereIn('status', ['resolved', 'closed']);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
            });
        }

        $tickets = $query->paginate(18)->withQueryString();
        $inquiryCategories = \App\Models\SupportInquiryCategory::query()->ordered()->get();

        $studentBase = SupportTicket::query()->fromStudents();
        $stats = [
            'total' => (clone $studentBase)->count(),
            'needs_reply' => (clone $studentBase)->awaitingAdminResponse()->count(),
            'open' => (clone $studentBase)->where('status', 'open')->count(),
            'in_progress' => (clone $studentBase)->where('status', 'in_progress')->count(),
            'resolved' => (clone $studentBase)->where('status', 'resolved')->count(),
            'urgent_active' => (clone $studentBase)->where('priority', 'urgent')
                ->whereIn('status', ['open', 'in_progress'])->count(),
            'today' => (clone $studentBase)->whereDate('created_at', today())->count(),
        ];

        $categoryCounts = (clone $studentBase)
            ->selectRaw('support_inquiry_category_id, count(*) as total')
            ->whereIn('status', ['open', 'in_progress'])
            ->groupBy('support_inquiry_category_id')
            ->pluck('total', 'support_inquiry_category_id');

        return view('admin.support-tickets.index', compact(
            'tickets',
            'stats',
            'status',
            'priority',
            'inquiryCategories',
            'categoryId',
            'view',
            'search',
            'categoryCounts'
        ));
    }

    public function show(SupportTicket $ticket)
    {
        $ticket->load(['user', 'assignedAdmin', 'inquiryCategory', 'replies.user', 'latestReply']);

        return view('admin.support-tickets.show', compact('ticket'));
    }

    public function updateStatus(Request $request, SupportTicket $ticket)
    {
        $data = $request->validate([
            'status' => ['required', 'in:open,in_progress,resolved,closed'],
            'priority' => ['nullable', 'in:low,normal,high,urgent'],
        ]);

        $updates = [
            'status' => $data['status'],
            'assigned_admin_id' => auth()->id(),
            'resolved_at' => in_array($data['status'], ['resolved', 'closed'], true) ? now() : null,
        ];

        if (! empty($data['priority'])) {
            $updates['priority'] = $data['priority'];
        }

        $ticket->update($updates);

        return back()->with('success', 'تم تحديث التذكرة.');
    }

    public function reply(Request $request, SupportTicket $ticket)
    {
        $data = $request->validate([
            'message' => ['required', 'string', 'min:2', 'max:5000'],
            'mark_resolved' => ['nullable', 'boolean'],
        ]);

        SupportTicketReply::create([
            'support_ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'sender_type' => 'admin',
            'message' => $data['message'],
        ]);

        $resolved = $request->boolean('mark_resolved');
        $ticket->update([
            'status' => $resolved ? 'resolved' : 'in_progress',
            'assigned_admin_id' => auth()->id(),
            'last_reply_at' => now(),
            'resolved_at' => $resolved ? now() : null,
        ]);

        try {
            app(SupportTicketAlertService::class)->notifyStudentOfAdminReply($ticket, $data['message']);
        } catch (\Throwable $e) {
            report($e);
            Log::error('Support ticket student reply notification failed', ['ticket_id' => $ticket->id]);
        }

        $msg = $resolved
            ? 'تم إرسال الرد وإغلاق التذكرة (تم الحل).'
            : 'تم إرسال الرد للطالب وسيصله إشعار داخل المنصة.';

        return back()->with('success', $msg);
    }
}
