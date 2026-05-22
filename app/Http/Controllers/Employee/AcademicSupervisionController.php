<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\ClassroomMeeting;
use App\Models\LiveSetting;
use App\Models\User;
use App\Services\SubscriptionLimitService;
use Illuminate\Support\Facades\Auth;

class AcademicSupervisionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $supervisor = Auth::user();
        $this->ensureAcademicSupervisor($supervisor);

        $students = $supervisor
            ->supervisedStudentsAsAcademic()
            ->where('users.role', 'student')
            ->orderBy('users.name')
            ->get();

        $liveMeetings = collect();
        foreach ($students as $student) {
            $instructorIds = $this->instructorIdsForStudent($student);
            if ($instructorIds === []) {
                continue;
            }
            $live = ClassroomMeeting::query()
                ->whereIn('user_id', $instructorIds)
                ->whereNotNull('started_at')
                ->whereNull('ended_at')
                ->with(['user:id,name,email'])
                ->withCount('participants')
                ->first();
            if ($live) {
                $liveMeetings[$student->id] = $live;
            }
        }

        return view('employee.academic-supervision.index', compact('students', 'liveMeetings'));
    }

    public function show(User $student)
    {
        $supervisor = Auth::user();
        $this->ensureAcademicSupervisor($supervisor);
        $this->ensureSupervises($supervisor, $student);

        $student->loadCount('courseEnrollments');
        $enrollments = $student->courseEnrollments()
            ->with(['course:id,title'])
            ->orderByDesc('enrolled_at')
            ->limit(30)
            ->get();

        $subscription = $student->activeSubscription();
        $instructorIds = $this->instructorIdsForStudent($student);
        $hasClassroom = collect($instructorIds)->contains(
            fn (int $id) => User::find($id)?->hasSubscriptionFeature('classroom_access') ?? false
        );
        $usedMeetingsThisMonth = 0;
        $limits = ['classroom_meetings_per_month' => 0, 'classroom_max_participants' => 0, 'classroom_max_duration_minutes' => 0];

        $meetings = $instructorIds === []
            ? collect()
            : ClassroomMeeting::query()
                ->whereIn('user_id', $instructorIds)
                ->with(['user:id,name,email'])
                ->withCount('participants')
                ->orderByDesc('created_at')
                ->limit(25)
                ->get();

        $liveMeeting = $meetings->first(fn ($m) => $m->isLive());

        return view('employee.academic-supervision.show', compact(
            'student',
            'enrollments',
            'subscription',
            'limits',
            'usedMeetingsThisMonth',
            'hasClassroom',
            'meetings',
            'liveMeeting'
        ));
    }

    public function observerRoom(ClassroomMeeting $meeting)
    {
        $supervisor = Auth::user();
        $this->ensureAcademicSupervisor($supervisor);

        $host = $meeting->user;
        if (! $host || ! in_array($host->role, ['instructor', 'teacher'], true)) {
            abort(404);
        }

        $student = $supervisor->supervisedStudentsAsAcademic()
            ->whereHas('courseEnrollments', function ($q) use ($host) {
                $q->where('status', 'active')
                    ->whereHas('course', fn ($c) => $c->where('instructor_id', $host->id));
            })
            ->first();

        if (! $student) {
            abort(403);
        }

        $this->ensureSupervises($supervisor, $student);

        if (! $meeting->isLive()) {
            return redirect()
                ->route('employee.academic-supervision.show', $student)
                ->with('error', 'الاجتماع غير نشط حالياً.');
        }

        if ($meeting->ended_at) {
            return redirect()
                ->route('employee.academic-supervision.show', $student)
                ->with('error', 'انتهى هذا الاجتماع.');
        }

        $limits = SubscriptionLimitService::limitsForUser($host);
        $maxDurationMinutes = (int) $limits['classroom_max_duration_minutes'];
        $effectiveDurationMinutes = (int) ($meeting->planned_duration_minutes ?: $maxDurationMinutes);
        if ($effectiveDurationMinutes > $maxDurationMinutes) {
            $effectiveDurationMinutes = $maxDurationMinutes;
        }

        if ($meeting->started_at && $meeting->started_at->copy()->addMinutes($effectiveDurationMinutes)->isPast()) {
            if (! $meeting->ended_at) {
                $meeting->update(['ended_at' => now()]);
            }

            return redirect()
                ->route('employee.academic-supervision.show', $student)
                ->with('error', 'انتهت مدة الاجتماع.');
        }

        $jitsiDomain = LiveSetting::getJitsiDomain();
        $isDemoJitsi = (strpos($jitsiDomain, 'meet.jit.si') !== false);
        $meetingEndsAt = $meeting->started_at ? $meeting->started_at->copy()->addMinutes($effectiveDurationMinutes) : null;
        $routePrefix = 'instructor.';
        $user = $supervisor;
        $academicObserverMode = true;
        $academicObserverExitUrl = route('employee.academic-supervision.show', $student);
        $jitsiDisplayName = 'مشرف: '.$supervisor->name;
        $subscriptionFeatureMenuItems = [];
        $subscriptionPackageLabel = null;

        return view('student.classroom.room', compact(
            'meeting',
            'jitsiDomain',
            'user',
            'isDemoJitsi',
            'maxDurationMinutes',
            'effectiveDurationMinutes',
            'meetingEndsAt',
            'routePrefix',
            'academicObserverMode',
            'academicObserverExitUrl',
            'jitsiDisplayName',
            'subscriptionFeatureMenuItems',
            'subscriptionPackageLabel'
        ));
    }

    private function ensureAcademicSupervisor(User $user): void
    {
        if (! $user->is_employee || $user->employeeJob?->code !== 'academic_supervisor') {
            abort(403, 'هذا القسم متاح لمشرف أكاديمي فقط.');
        }
        if (! $user->employeeCan('academic_supervision_desk')) {
            abort(403);
        }
    }

    private function ensureSupervises(User $supervisor, User $student): void
    {
        if ($student->role !== 'student') {
            abort(404);
        }
        if (! $supervisor->supervisedStudentsAsAcademic()->whereKey($student->id)->exists()) {
            abort(403, 'هذا الطالب غير ضمن قائمة إشرافك.');
        }
    }

    /** @return list<int> */
    private function instructorIdsForStudent(User $student): array
    {
        return $student->courseEnrollments()
            ->where('status', 'active')
            ->whereHas('course', fn ($q) => $q->whereNotNull('instructor_id'))
            ->with('course:id,instructor_id')
            ->get()
            ->pluck('course.instructor_id')
            ->filter()
            ->unique()
            ->values()
            ->map(fn ($id) => (int) $id)
            ->all();
    }
}
