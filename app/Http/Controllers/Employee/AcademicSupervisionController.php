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

        $studentIds = $students->pluck('id');

        $liveMeetings = ClassroomMeeting::query()
            ->whereIn('user_id', $studentIds)
            ->whereNotNull('started_at')
            ->whereNull('ended_at')
            ->with(['user:id,name,email'])
            ->withCount('participants')
            ->get()
            ->keyBy('user_id');

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
        $limits = SubscriptionLimitService::limitsForUser($student);
        $usedMeetingsThisMonth = SubscriptionLimitService::monthlyClassroomUsage($student);
        $hasClassroom = $student->hasSubscriptionFeature('classroom_access');

        $meetings = ClassroomMeeting::query()
            ->where('user_id', $student->id)
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

        $student = $meeting->user;
        if (! $student || $student->role !== 'student') {
            abort(404);
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

        $limits = SubscriptionLimitService::limitsForUser($student);
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
        $useInstructorRoutes = false;
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
            'useInstructorRoutes',
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
}
