<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Instructor\ClassroomController;
use App\Models\ClassroomMeeting;
use App\Models\LiveSetting;
use App\Support\PlatformBranding;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/**
 * ميتينجات الإدارة عبر Classroom + LiveKit — أي ضيف يدخل برابط/كود.
 */
class AdminClassroomController extends ClassroomController
{
    protected string $routePrefix = 'admin.';

    protected function applyClassroomMiddleware(): void
    {
        // صلاحيات الأدمن على مستوى الراوت (admin.access + manage.live-sessions)
    }

    protected function ensureClassroomAccess($user, ?ClassroomMeeting $meeting = null): void
    {
        if (! $user || ! $this->userCanHostAdminMeeting($user)) {
            abort(403, 'ميتينج الإدارة متاح لفريق الإدارة فقط.');
        }
    }

    protected function userCanHostAdminMeeting($user): bool
    {
        $role = (string) ($user->role ?? '');

        return in_array($role, ['super_admin', 'admin'], true)
            || (method_exists($user, 'isEmployee') && $user->isEmployee());
    }

    protected function ensureStandaloneClassroomManagement($user, ?ClassroomMeeting $meeting = null): void
    {
        $this->ensureClassroomAccess($user, $meeting);
    }

    protected function ensureMeetingOwnership(ClassroomMeeting $meeting, $user): void
    {
        $this->ensureClassroomAccess($user, $meeting);

        if ((int) $meeting->user_id === (int) $user->id) {
            return;
        }

        // أي أدمن يقدر يدير ميتينج أنشأته الإدارة (مش ميتينجات المعلمين)
        if ($this->isAdminHostedMeeting($meeting)) {
            return;
        }

        abort(403);
    }

    protected function classroomLimitsFor($user): array
    {
        return [
            'plan_key' => 'admin',
            'classroom_meetings_per_month' => 10000,
            'classroom_max_participants' => 500,
            'classroom_default_duration_minutes' => 120,
            'classroom_max_duration_minutes' => 480,
            'personal_marketing_profile_sections' => 0,
            'personal_marketing_priority_score' => 0,
            'personal_marketing_monthly_featured_days' => 0,
        ];
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $this->ensureStandaloneClassroomManagement($user);

        $status = (string) $request->get('status', 'all');
        if (! in_array($status, ['all', 'live', 'scheduled', 'ended'], true)) {
            $status = 'all';
        }

        $meetingsQuery = ClassroomMeeting::query()
            ->where(function ($q) {
                $q->where('settings->hosted_by_admin', true)
                    ->orWhereHas('user', function ($uq) {
                        $uq->whereIn('role', ['super_admin', 'admin'])
                            ->orWhere('is_employee', true);
                    });
            })
            ->with(['user'])
            ->withCount('participants');

        $baseAdminMeetings = ClassroomMeeting::query()
            ->where(function ($q) {
                $q->where('settings->hosted_by_admin', true)
                    ->orWhereHas('user', function ($uq) {
                        $uq->whereIn('role', ['super_admin', 'admin'])
                            ->orWhere('is_employee', true);
                    });
            });

        if ($status === 'live') {
            $meetingsQuery->whereNotNull('started_at')->whereNull('ended_at');
        } elseif ($status === 'scheduled') {
            $meetingsQuery->whereNull('started_at')->whereNull('ended_at');
        } elseif ($status === 'ended') {
            $meetingsQuery->whereNotNull('ended_at');
        }

        $meetings = $meetingsQuery
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        $joinBaseUrl = url('classroom/join');
        $stats = [
            'total' => (clone $baseAdminMeetings)->count(),
            'live' => (clone $baseAdminMeetings)->whereNotNull('started_at')->whereNull('ended_at')->count(),
            'ended' => (clone $baseAdminMeetings)->whereNotNull('ended_at')->count(),
            'scheduled' => (clone $baseAdminMeetings)->whereNull('started_at')->whereNull('ended_at')->count(),
        ];

        $limits = $this->classroomLimitsFor($user);
        $routePrefix = $this->routePrefix;

        return view('admin.live-meetings.index', compact(
            'meetings',
            'joinBaseUrl',
            'stats',
            'status',
            'limits',
            'routePrefix'
        ));
    }

    public function create()
    {
        $user = Auth::user();
        $this->ensureStandaloneClassroomManagement($user);
        $limits = $this->classroomLimitsFor($user);
        $routePrefix = $this->routePrefix;

        return view('admin.live-meetings.create', compact('limits', 'routePrefix'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $this->ensureStandaloneClassroomManagement($user);
        $limits = $this->classroomLimitsFor($user);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'max_participants' => ['required', 'integer', 'min:2', 'max:'.$limits['classroom_max_participants']],
            'start_now' => ['nullable', Rule::in(['0', '1'])],
            'scheduled_for' => ['nullable', 'date'],
            'planned_duration_minutes' => ['nullable', 'integer', 'min:15', 'max:'.$limits['classroom_max_duration_minutes']],
        ]);

        $code = ClassroomMeeting::generateCode();
        $roomName = PlatformBranding::classroomRoomName($code);
        $startNow = (string) ($data['start_now'] ?? '1') === '1';

        $meeting = ClassroomMeeting::create([
            'user_id' => $user->id,
            'code' => $code,
            'room_name' => $roomName,
            'title' => $data['title'],
            'scheduled_for' => $startNow ? null : ($data['scheduled_for'] ?? null),
            'planned_duration_minutes' => (int) ($data['planned_duration_minutes'] ?? $limits['classroom_default_duration_minutes']),
            'max_participants' => (int) $data['max_participants'],
            'started_at' => $startNow ? now() : null,
            'settings' => [
                'hosted_by_admin' => true,
            ],
        ]);

        if ($startNow) {
            return redirect()->to($this->classroomRoute('room', $meeting));
        }

        return redirect()->to($this->classroomRoute('show', $meeting))
            ->with('success', 'تم إنشاء ميتينج الإدارة. انسخي رابط الدخول وأرسليه لأي شخص.');
    }

    public function show(ClassroomMeeting $meeting)
    {
        $user = Auth::user();
        $this->ensureMeetingOwnership($meeting, $user);

        $meeting->loadCount('participants');
        $joinUrl = url('classroom/join/'.$meeting->code);
        $limits = $this->classroomLimitsFor($user);
        $routePrefix = $this->routePrefix;

        return view('admin.live-meetings.show', compact(
            'meeting',
            'joinUrl',
            'limits',
            'routePrefix'
        ));
    }

    public function edit(ClassroomMeeting $meeting)
    {
        $user = Auth::user();
        $this->ensureMeetingOwnership($meeting, $user);
        $limits = $this->classroomLimitsFor($user);
        $routePrefix = $this->routePrefix;

        return view('admin.live-meetings.edit', compact('meeting', 'limits', 'routePrefix'));
    }

    public function room(ClassroomMeeting $meeting)
    {
        $user = Auth::user();
        $this->ensureMeetingOwnership($meeting, $user);

        if ($meeting->ended_at) {
            return redirect()->to($this->classroomRoute('show', $meeting))
                ->with('error', 'انتهى هذا الاجتماع ولا يمكن إعادة فتح الغرفة.');
        }

        // لو أدمن تاني دخل غرفة مش ملكه — يبقى منظم الاجتماع هو المالك للتوكن HOST
        // نضمن أن منفتح الغرفة من الإدارة يقدر يبدأ الاجتماع
        if (! $meeting->started_at) {
            $meeting->update(['started_at' => now()]);
        }

        $limits = $this->classroomLimitsFor($user);
        $maxDurationMinutes = (int) $limits['classroom_max_duration_minutes'];
        $effectiveDurationMinutes = (int) ($meeting->planned_duration_minutes ?: $maxDurationMinutes);
        if ($effectiveDurationMinutes > $maxDurationMinutes) {
            $effectiveDurationMinutes = $maxDurationMinutes;
        }

        if ($meeting->started_at && $meeting->started_at->copy()->addMinutes($effectiveDurationMinutes)->isPast()) {
            app(\App\Services\TutorAttendanceService::class)->endMeetingAndSync($meeting->fresh());

            return redirect()->to($this->classroomRoute('show', $meeting))
                ->with('error', 'انتهت مدة الاجتماع المسموح بها.');
        }

        $jitsiDomain = LiveSetting::getLiveKitHost();
        $isDemoJitsi = false;
        $livekitTokenUrl = route('livekit.classroom.token', $meeting);
        $meetingEndsAt = $meeting->started_at ? $meeting->started_at->copy()->addMinutes($effectiveDurationMinutes) : null;
        $routePrefix = $this->routePrefix;
        $isLessonMeeting = false;
        $presenceHeartbeatUrl = $this->classroomRoute('heartbeat', $meeting);
        $presenceLeaveUrl = $this->classroomRoute('leave-presence', $meeting);
        $serverRecordingActive = false;
        $subscriptionFeatureMenuItems = [];
        $subscriptionPackageLabel = 'إدارة Sana';
        $jitsiDisplayName = 'إدارة: '.$user->name;

        return view('student.classroom.room', compact(
            'meeting',
            'jitsiDomain',
            'livekitTokenUrl',
            'user',
            'isDemoJitsi',
            'maxDurationMinutes',
            'effectiveDurationMinutes',
            'meetingEndsAt',
            'routePrefix',
            'isLessonMeeting',
            'presenceHeartbeatUrl',
            'presenceLeaveUrl',
            'serverRecordingActive',
            'subscriptionFeatureMenuItems',
            'subscriptionPackageLabel',
            'jitsiDisplayName'
        ));
    }

    public function end(ClassroomMeeting $meeting)
    {
        $user = Auth::user();
        $this->ensureMeetingOwnership($meeting, $user);
        $meeting->update(['ended_at' => now()]);
        app(\App\Services\TutorAttendanceService::class)->endMeetingAndSync($meeting->fresh());

        return redirect()->to($this->classroomRoute('show', $meeting))->with('success', 'تم إنهاء الاجتماع.');
    }

    private function isAdminHostedMeeting(ClassroomMeeting $meeting): bool
    {
        if ((bool) data_get($meeting->settings, 'hosted_by_admin', false)) {
            return true;
        }

        $owner = $meeting->relationLoaded('user') ? $meeting->user : $meeting->user()->first();

        if (! $owner) {
            return false;
        }

        return in_array((string) $owner->role, ['super_admin', 'admin'], true)
            || (bool) $owner->is_employee;
    }
}
