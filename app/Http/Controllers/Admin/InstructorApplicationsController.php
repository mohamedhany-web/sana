<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicSubject;
use App\Models\AcademicYear;
use App\Models\InstructorProfile;
use App\Services\InstructorApplicationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class InstructorApplicationsController extends Controller
{
    public function index(Request $request)
    {
        $query = InstructorProfile::query()
            ->with('user')
            ->whereNotNull('submitted_at')
            ->orderByDesc('submitted_at');

        if ($status = $request->string('status')->toString()) {
            $query->where('status', $status);
        }

        if ($request->string('account')->toString() === 'active') {
            $query->whereHas('user', fn ($q) => $q->where('is_active', true));
        } elseif ($request->string('account')->toString() === 'inactive') {
            $query->whereHas('user', fn ($q) => $q->where('is_active', false));
        }

        if ($search = $request->string('search')->trim()->toString()) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $applications = $query->paginate(20)->withQueryString();

        $base = InstructorProfile::query()->whereNotNull('submitted_at');

        $stats = [
            'pending' => (clone $base)->where('status', InstructorProfile::STATUS_PENDING_REVIEW)->count(),
            'approved' => (clone $base)->where('status', InstructorProfile::STATUS_APPROVED)->count(),
            'rejected' => (clone $base)->where('status', InstructorProfile::STATUS_REJECTED)->count(),
            'total' => (clone $base)->count(),
            'active_accounts' => (clone $base)->whereHas('user', fn ($q) => $q->where('is_active', true))->count(),
            'inactive_accounts' => (clone $base)->whereHas('user', fn ($q) => $q->where('is_active', false))->count(),
        ];

        $publicApplyUrl = route('tutor.apply');

        return view('admin.instructor-applications.index', compact('applications', 'stats', 'publicApplyUrl'));
    }

    public function show(InstructorProfile $application)
    {
        if (! $application->submitted_at) {
            abort(404);
        }

        $application->load(['user', 'reviewedByUser']);
        $subjects = AcademicSubject::whereIn('id', $application->tutor_subject_ids ?? [])->get();
        $years = AcademicYear::whereIn('id', $application->tutor_academic_year_ids ?? [])->get();

        return view('admin.instructor-applications.show', compact('application', 'subjects', 'years'));
    }

    public function edit(InstructorProfile $application)
    {
        if (! $application->submitted_at) {
            abort(404);
        }

        $application->load('user');
        $subjects = AcademicSubject::where('is_active', true)->orderBy('name')->get();
        $years = AcademicYear::where('is_active', true)->orderBy('order')->get();

        return view('admin.instructor-applications.edit', compact('application', 'subjects', 'years'));
    }

    public function update(Request $request, InstructorProfile $application)
    {
        if (! $application->submitted_at) {
            abort(404);
        }

        $userId = $application->user_id;

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'phone' => ['nullable', 'string', 'max:30'],
            'headline' => ['required', 'string', 'max:200'],
            'bio' => ['required', 'string', 'max:5000'],
            'years_experience' => ['required', 'integer', 'min:0', 'max:50'],
            'subject_ids' => ['required', 'array', 'min:1'],
            'subject_ids.*' => ['integer', 'exists:academic_subjects,id'],
            'academic_year_ids' => ['required', 'array', 'min:1'],
            'academic_year_ids.*' => ['integer', 'exists:academic_years,id'],
            'matching_modes' => ['required', 'array', 'min:1'],
            'matching_modes.*' => ['in:assisted,self_schedule,pick_teacher'],
            'session_types' => ['required', 'array', 'min:1'],
            'session_types.*' => ['in:one_to_one,small_group'],
        ]);

        try {
            InstructorApplicationService::updateApplication($application, $data, $request->user());
        } catch (\Throwable $e) {
            Log::error('instructor application update failed', [
                'application_id' => $application->id,
                'reviewer_id' => $request->user()?->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withInput()->with('error', 'تعذّر حفظ التعديلات. حاول مرة أخرى.');
        }

        return redirect()
            ->route('admin.instructor-applications.show', $application)
            ->with('success', 'تم تحديث بيانات الطلب بنجاح.');
    }

    public function destroy(Request $request, InstructorProfile $application)
    {
        if (! $application->submitted_at) {
            abort(404);
        }

        $user = $application->user;
        if ($user && InstructorApplicationService::mustKeepAccountActive($user)) {
            return back()->with('error', 'لا يمكن حذف طلب مرتبط بحساب إداري أو موظف.');
        }

        try {
            InstructorApplicationService::destroyApplication($application, $request->user());
        } catch (\Throwable $e) {
            Log::error('instructor application destroy failed', [
                'application_id' => $application->id,
                'reviewer_id' => $request->user()?->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'تعذّر حذف الطلب.');
        }

        return redirect()
            ->route('admin.instructor-applications.index')
            ->with('success', 'تم حذف الطلب وإيقاف حساب المعلم.');
    }

    public function toggleAccount(Request $request, InstructorProfile $application)
    {
        if (! $application->user) {
            return back()->with('error', 'لا يوجد حساب مرتبط بهذا الطلب.');
        }

        try {
            $isActive = InstructorApplicationService::toggleAccountActive($application, $request->user());
        } catch (\Throwable $e) {
            Log::error('instructor application toggle account failed', [
                'application_id' => $application->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'تعذّر تغيير حالة الحساب.');
        }

        $message = $isActive
            ? 'تم تفعيل حساب المعلم — يمكنه تسجيل الدخول.'
            : 'تم إيقاف حساب المعلم — لن يتمكن من تسجيل الدخول.';

        return back()->with('success', $message);
    }

    public function activateAccount(Request $request, InstructorProfile $application)
    {
        try {
            InstructorApplicationService::setAccountActive($application, $request->user(), true);
        } catch (\Throwable $e) {
            return back()->with('error', 'تعذّر تفعيل الحساب.');
        }

        return back()->with('success', 'تم تفعيل حساب المعلم.');
    }

    public function deactivateAccount(Request $request, InstructorProfile $application)
    {
        $user = $application->user;
        if ($user && InstructorApplicationService::mustKeepAccountActive($user)) {
            return back()->with('error', 'لا يمكن إيقاف هذا الحساب.');
        }

        try {
            InstructorApplicationService::setAccountActive($application, $request->user(), false);
        } catch (\Throwable $e) {
            return back()->with('error', 'تعذّر إيقاف الحساب.');
        }

        return back()->with('success', 'تم إيقاف حساب المعلم.');
    }

    public function reopen(Request $request, InstructorProfile $application)
    {
        if ($application->status === InstructorProfile::STATUS_PENDING_REVIEW) {
            return back()->with('info', 'الطلب بانتظار المراجعة بالفعل.');
        }

        try {
            InstructorApplicationService::reopenForReview($application, $request->user());
        } catch (\Throwable $e) {
            Log::error('instructor application reopen failed', [
                'application_id' => $application->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'تعذّر إعادة فتح الطلب.');
        }

        return redirect()
            ->route('admin.instructor-applications.show', $application)
            ->with('success', 'تم إعادة الطلب لقائمة المراجعة وإيقاف الحساب مؤقتاً.');
    }

    public function approve(Request $request, InstructorProfile $application)
    {
        $data = $request->validate([
            'admin_note' => ['nullable', 'string', 'max:2000'],
        ]);

        if ($application->status === InstructorProfile::STATUS_APPROVED) {
            return back()->with('info', 'هذا الطلب مقبول مسبقاً.');
        }

        try {
            InstructorApplicationService::approve($application, $request->user(), $data['admin_note'] ?? null);
        } catch (\Throwable $e) {
            Log::error('instructor application approve failed', [
                'application_id' => $application->id,
                'reviewer_id' => $request->user()?->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'تعذّر إتمام القبول. حاول مرة أخرى أو راجع سجل الأخطاء.');
        }

        return redirect()
            ->route('admin.instructor-applications.index', ['status' => InstructorProfile::STATUS_PENDING_REVIEW])
            ->with('success', 'تم قبول المعلم وتفعيل حسابه — يمكنه تسجيل الدخول الآن.');
    }

    public function reject(Request $request, InstructorProfile $application)
    {
        $data = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:5000'],
        ]);

        try {
            InstructorApplicationService::reject($application, $request->user(), $data['rejection_reason']);
        } catch (\Throwable $e) {
            Log::error('instructor application reject failed', [
                'application_id' => $application->id,
                'reviewer_id' => $request->user()?->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'تعذّر إتمام الرفض. حاول مرة أخرى أو راجع سجل الأخطاء.');
        }

        return redirect()
            ->route('admin.instructor-applications.index', ['status' => InstructorProfile::STATUS_PENDING_REVIEW])
            ->with('success', 'تم رفض الطلب وإبلاغ المعلم.');
    }
}
