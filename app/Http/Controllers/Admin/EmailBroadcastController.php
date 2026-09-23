<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendEmailBroadcastJob;
use App\Models\EmailBroadcast;
use App\Models\EmailBroadcastRecipient;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
// no extra imports

class EmailBroadcastController extends Controller
{
    private function gate(): void
    {
        $u = Auth::user();
        abort_unless(
            $u && ($u->isSuperAdmin() || $u->hasPermission('manage.email-broadcasts')),
            403
        );
    }

    public function index(string $audience)
    {
        $this->gate();
        abort_unless(array_key_exists($audience, EmailBroadcast::audienceLabels()), 404);

        $broadcasts = EmailBroadcast::query()
            ->where('audience', $audience)
            ->with('creator:id,name')
            ->latest()
            ->paginate(20);

        return view('admin.email-broadcasts.index', [
            'audience' => $audience,
            'audienceLabel' => EmailBroadcast::audienceLabels()[$audience],
            'broadcasts' => $broadcasts,
        ]);
    }

    public function create(string $audience)
    {
        $this->gate();
        abort_unless(array_key_exists($audience, EmailBroadcast::audienceLabels()), 404);

        return view('admin.email-broadcasts.create', [
            'audience' => $audience,
            'audienceLabel' => EmailBroadcast::audienceLabels()[$audience],
        ]);
    }

    public function store(Request $request, string $audience)
    {
        $this->gate();
        abort_unless(array_key_exists($audience, EmailBroadcast::audienceLabels()), 404);

        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'body' => 'required|string|max:20000',
            'mode' => 'nullable|string|in:audience,single_email',
            'single_email' => 'nullable|email|max:255',
            'single_name' => 'nullable|string|max:255',
        ]);

        $mode = $validated['mode'] ?? 'audience';

        if ($mode === 'single_email') {
            $email = strtolower(trim((string) ($validated['single_email'] ?? '')));
            abort_unless($email !== '', 422);

            $u = User::query()->where('email', $email)->first(['id', 'name', 'email']);

            DB::beginTransaction();
            try {
                $broadcast = EmailBroadcast::create([
                    'created_by' => Auth::id(),
                    'audience' => $audience,
                    'subject' => $validated['subject'],
                    'body' => $validated['body'],
                    'status' => EmailBroadcast::STATUS_DRAFT,
                    'stats' => ['total' => 1, 'sent' => 0, 'failed' => 0, 'remaining' => 1],
                ]);

                EmailBroadcastRecipient::create([
                    'email_broadcast_id' => $broadcast->id,
                    'user_id' => $u?->id,
                    'email' => $email,
                    'name' => $validated['single_name'] ?? ($u?->name),
                    'status' => EmailBroadcastRecipient::STATUS_QUEUED,
                ]);

                DB::commit();
            } catch (\Throwable $e) {
                DB::rollBack();
                throw $e;
            }

            SendEmailBroadcastJob::dispatch($broadcast->id);

            return redirect()->route('admin.email-broadcasts.show', [$audience, $broadcast])
                ->with('success', __('تم بدء الإرسال للإيميل المحدد.'));
        }

        $usersQuery = User::query()->where('is_active', true)->whereNotNull('email');
        if ($audience === EmailBroadcast::AUDIENCE_STUDENTS) {
            $usersQuery->where('role', 'student');
        } elseif ($audience === EmailBroadcast::AUDIENCE_INSTRUCTORS) {
            $usersQuery->where('role', 'instructor');
        } elseif ($audience === EmailBroadcast::AUDIENCE_EMPLOYEES) {
            $usersQuery->where('is_employee', true);
        } elseif ($audience === EmailBroadcast::AUDIENCE_ALL_USERS) {
            // all active with email
        } else {
            abort(404);
        }

        $users = $usersQuery->get(['id', 'name', 'email']);

        DB::beginTransaction();
        try {
            $broadcast = EmailBroadcast::create([
                'created_by' => Auth::id(),
                'audience' => $audience,
                'subject' => $validated['subject'],
                'body' => $validated['body'],
                'status' => EmailBroadcast::STATUS_DRAFT,
                'stats' => ['total' => $users->count(), 'sent' => 0, 'failed' => 0, 'remaining' => $users->count()],
            ]);

            $rows = [];
            foreach ($users as $u) {
                $rows[] = [
                    'email_broadcast_id' => $broadcast->id,
                    'user_id' => $u->id,
                    'email' => $u->email,
                    'name' => $u->name,
                    'status' => EmailBroadcastRecipient::STATUS_QUEUED,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            if ($rows !== []) {
                EmailBroadcastRecipient::insert($rows);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        SendEmailBroadcastJob::dispatch($broadcast->id);

        return redirect()->route('admin.email-broadcasts.show', [$audience, $broadcast])
            ->with('success', __('تم إنشاء الحملة وبدء الإرسال عبر البريد.'));
    }

    public function show(string $audience, EmailBroadcast $email_broadcast)
    {
        $this->gate();
        abort_unless($email_broadcast->audience === $audience, 404);

        $email_broadcast->load('creator:id,name');

        $recipients = EmailBroadcastRecipient::query()
            ->where('email_broadcast_id', $email_broadcast->id)
            ->latest('id')
            ->paginate(40);

        $counts = EmailBroadcastRecipient::query()
            ->selectRaw('status, COUNT(*) as c')
            ->where('email_broadcast_id', $email_broadcast->id)
            ->groupBy('status')
            ->pluck('c', 'status');

        return view('admin.email-broadcasts.show', [
            'audience' => $audience,
            'audienceLabel' => EmailBroadcast::audienceLabels()[$audience] ?? $audience,
            'broadcast' => $email_broadcast,
            'recipients' => $recipients,
            'counts' => $counts,
        ]);
    }
}
