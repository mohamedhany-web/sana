<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassroomMeeting;
use App\Models\LessonBooking;
use App\Models\LiveSetting;
use App\Services\LessonMeetingAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminLessonLiveSessionsController extends Controller
{
    public function index(Request $request)
    {
        $query = ClassroomMeeting::query()->with(['user:id,name,email']);
        LessonMeetingAccess::applyLessonMeetingConstraint($query);

        $status = (string) $request->get('status', '');
        if ($status === 'live') {
            $query->whereNotNull('started_at')->whereNull('ended_at');
        } elseif ($status === 'scheduled') {
            $query->whereNull('started_at')->whereNull('ended_at');
        } elseif ($status === 'ended') {
            $query->whereNotNull('ended_at');
        }

        if ($search = trim((string) $request->get('search', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($u) use ($search) {
                        $u->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    })
                    ->orWhereExists(function ($sub) use ($search) {
                        $sub->selectRaw('1')
                            ->from('lesson_bookings')
                            ->leftJoin('users as students', 'students.id', '=', 'lesson_bookings.student_id')
                            ->leftJoin('users as instructors', 'instructors.id', '=', 'lesson_bookings.instructor_id')
                            ->where(function ($inner) {
                                $inner->whereColumn('lesson_bookings.classroom_meeting_id', 'classroom_meetings.id')
                                    ->orWhereColumn('lesson_bookings.id', 'classroom_meetings.lesson_booking_id');
                            })
                            ->where(function ($names) use ($search) {
                                $names->where('students.name', 'like', "%{$search}%")
                                    ->orWhere('instructors.name', 'like', "%{$search}%")
                                    ->orWhere('lesson_bookings.code', 'like', "%{$search}%");
                            });
                    });
            });
        }

        $query->withCount([
            'participants as online_participants_count' => function ($q) {
                $q->whereNull('left_at');
            },
        ]);

        $meetings = $query
            ->orderByRaw('CASE WHEN started_at IS NOT NULL AND ended_at IS NULL THEN 0 ELSE 1 END')
            ->orderByDesc('started_at')
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        $this->attachBookings($meetings);

        $baseStats = ClassroomMeeting::query();
        LessonMeetingAccess::applyLessonMeetingConstraint($baseStats);

        $stats = [
            'total' => (clone $baseStats)->count(),
            'live' => (clone $baseStats)->whereNotNull('started_at')->whereNull('ended_at')->count(),
            'scheduled' => (clone $baseStats)->whereNull('started_at')->whereNull('ended_at')->count(),
            'ended' => (clone $baseStats)->whereNotNull('ended_at')->count(),
        ];

        return view('admin.lesson-live-sessions.index', compact('meetings', 'stats', 'status'));
    }

    public function observe(ClassroomMeeting $meeting)
    {
        abort_unless(LessonMeetingAccess::isLessonMeeting($meeting), 404);

        $admin = Auth::user();
        abort_unless($admin, 401);

        if (! $meeting->isLive() || data_get($meeting->settings, 'host_ended')) {
            return redirect()
                ->route('admin.lesson-live-sessions.index')
                ->with('error', 'الجلسة ليست مباشرة حالياً.');
        }

        $bookings = LessonMeetingAccess::bookingsFor($meeting)->load(['student:id,name', 'instructor:id,name']);
        $duration = (int) ($bookings->max('duration_minutes') ?: $meeting->planned_duration_minutes ?: 60);
        $maxDurationMinutes = $duration;
        $effectiveDurationMinutes = $duration;

        $jitsiDomain = LiveSetting::getLiveKitHost();
        $isDemoJitsi = false;
        $livekitTokenUrl = route('livekit.classroom.token', $meeting);
        $meetingEndsAt = null;
        $routePrefix = 'instructor.';
        $user = $admin;
        $academicObserverMode = true;
        $academicObserverExitUrl = route('admin.lesson-live-sessions.index');
        $jitsiDisplayName = 'رقابة';
        $isLessonMeeting = true;
        $presenceHeartbeatUrl = null;
        $presenceLeaveUrl = null;
        $serverRecordingActive = true;
        $subscriptionFeatureMenuItems = [];
        $subscriptionPackageLabel = null;
        $livekitExtraBody = ['observe' => true];
        $livekitHiddenObserver = true;

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
            'academicObserverMode',
            'academicObserverExitUrl',
            'jitsiDisplayName',
            'isLessonMeeting',
            'presenceHeartbeatUrl',
            'presenceLeaveUrl',
            'serverRecordingActive',
            'subscriptionFeatureMenuItems',
            'subscriptionPackageLabel',
            'livekitExtraBody',
            'livekitHiddenObserver'
        ));
    }

    protected function attachBookings($meetings): void
    {
        $ids = $meetings->getCollection()->pluck('id')->filter()->all();
        $bookingIds = $meetings->getCollection()->pluck('lesson_booking_id')->filter()->all();

        if ($ids === [] && $bookingIds === []) {
            return;
        }

        $rows = LessonBooking::query()
            ->with(['student:id,name', 'instructor:id,name', 'subject:id,name'])
            ->where(function ($q) use ($ids, $bookingIds) {
                if ($ids !== []) {
                    $q->whereIn('classroom_meeting_id', $ids);
                }
                if ($bookingIds !== []) {
                    $q->orWhereIn('id', $bookingIds);
                }
            })
            ->get();

        $byMeeting = [];
        foreach ($rows as $booking) {
            $mid = (int) ($booking->classroom_meeting_id ?: 0);
            if ($mid > 0) {
                $byMeeting[$mid][] = $booking;
            }
        }

        foreach ($meetings as $meeting) {
            $list = collect($byMeeting[$meeting->id] ?? []);
            if ($meeting->lesson_booking_id) {
                $extra = $rows->firstWhere('id', (int) $meeting->lesson_booking_id);
                if ($extra && $list->where('id', $extra->id)->isEmpty()) {
                    $list->push($extra);
                }
            }
            $meeting->setAttribute('lesson_bookings_display', $list->unique('id')->values());
        }
    }
}
