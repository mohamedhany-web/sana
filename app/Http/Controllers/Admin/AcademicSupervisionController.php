<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassroomMeeting;
use App\Models\EmployeeJob;
use App\Models\LiveSetting;
use App\Models\User;
use App\Services\SubscriptionLimitService;
use Illuminate\Http\Request;

class AcademicSupervisionController extends Controller
{
    public function index(Request $request)
    {
        $job = EmployeeJob::query()->where('code', 'academic_supervisor')->first();

        $query = User::query()
            ->employees()
            ->where('employee_job_id', $job?->id)
            ->withCount(['supervisedStudentsAsAcademic'])
            ->with('employeeJob:id,name,code');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                    ->orWhere('email', 'like', "%{$s}%")
                    ->orWhere('phone', 'like', "%{$s}%")
                    ->orWhere('employee_code', 'like', "%{$s}%");
            });
        }

        $supervisors = $query->orderBy('name')->paginate(25)->withQueryString();

        return view('admin.academic-supervision.index', compact('supervisors', 'job'));
    }

    public function show(User $supervisor)
    {
        if (! $supervisor->is_employee || $supervisor->employeeJob?->code !== 'academic_supervisor') {
            abort(404);
        }

        $supervisor->load(['employeeJob']);
        $students = $supervisor->supervisedStudentsAsAcademic()
            ->where('users.role', 'student')
            ->orderBy('users.name')
            ->paginate(30);

        $allStudents = User::query()
            ->where('role', 'student')
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'phone']);

        return view('admin.academic-supervision.show', compact('supervisor', 'students', 'allStudents'));
    }

    public function searchStudents(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        if (mb_strlen($q) < 2) {
            return response()->json(['users' => []]);
        }

        $users = User::query()
            ->where('role', 'student')
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%");
            })
            ->orderBy('name')
            ->limit(20)
            ->get(['id', 'name', 'email', 'phone']);

        return response()->json(['users' => $users]);
    }

    public function attachStudent(Request $request, User $supervisor)
    {
        if (! $supervisor->is_employee || $supervisor->employeeJob?->code !== 'academic_supervisor') {
            abort(404);
        }

        $data = $request->validate([
            'student_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        $student = User::query()->findOrFail($data['student_id']);
        if ($student->role !== 'student') {
            return back()->with('error', 'المستخدم المحدد ليس طالباً.');
        }

        if ($supervisor->supervisedStudentsAsAcademic()->whereKey($student->id)->exists()) {
            return back()->with('info', 'هذا الطالب مربوط بالمشرف مسبقاً.');
        }

        $supervisor->supervisedStudentsAsAcademic()->attach($student->id, [
            'assigned_by' => auth()->id(),
        ]);

        return back()->with('success', 'تم ربط الطالب بالمشرف.');
    }

    public function detachStudent(User $supervisor, User $student)
    {
        if (! $supervisor->is_employee || $supervisor->employeeJob?->code !== 'academic_supervisor') {
            abort(404);
        }
        if ($student->role !== 'student') {
            abort(404);
        }

        $supervisor->supervisedStudentsAsAcademic()->detach($student->id);

        return back()->with('success', 'تم إلغاء ربط الطالب.');
    }

    public function studentShow(User $supervisor, User $student)
    {
        if (! $supervisor->is_employee || $supervisor->employeeJob?->code !== 'academic_supervisor') {
            abort(404);
        }
        if ($student->role !== 'student') {
            abort(404);
        }
        if (! $supervisor->supervisedStudentsAsAcademic()->whereKey($student->id)->exists()) {
            abort(404);
        }

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

        return view('admin.academic-supervision.student-show', compact(
            'supervisor',
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

    public function observeMeeting(User $supervisor, ClassroomMeeting $meeting)
    {
        if (! $supervisor->is_employee || $supervisor->employeeJob?->code !== 'academic_supervisor') {
            abort(404);
        }

        $student = $meeting->user;
        if (! $student || $student->role !== 'student') {
            abort(404);
        }

        if (! $supervisor->supervisedStudentsAsAcademic()->whereKey($student->id)->exists()) {
            abort(403);
        }

        if (! $meeting->isLive()) {
            return redirect()
                ->route('admin.academic-supervision.supervisors.students.show', [$supervisor, $student])
                ->with('error', 'الاجتماع غير نشط حالياً.');
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
                ->route('admin.academic-supervision.supervisors.students.show', [$supervisor, $student])
                ->with('error', 'انتهت مدة الاجتماع.');
        }

        $admin = auth()->user();
        $jitsiDomain = LiveSetting::getJitsiDomain();
        $isDemoJitsi = (strpos($jitsiDomain, 'meet.jit.si') !== false);
        $meetingEndsAt = $meeting->started_at ? $meeting->started_at->copy()->addMinutes($effectiveDurationMinutes) : null;
        $useInstructorRoutes = false;
        $user = $admin;
        $academicObserverMode = true;
        $academicObserverExitUrl = route('admin.academic-supervision.supervisors.students.show', [$supervisor, $student]);
        $jitsiDisplayName = 'إدارة: '.$admin->name;
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
}
