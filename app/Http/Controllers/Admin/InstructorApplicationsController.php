<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicSubject;
use App\Models\AcademicYear;
use App\Models\InstructorProfile;
use App\Services\InstructorApplicationService;
use App\Services\TutorFormSchemaService;
use App\Support\CloudStorage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

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
        $formPreviewUrl = route('admin.instructor-applications.form-preview');

        return view('admin.instructor-applications.index', compact('applications', 'stats', 'publicApplyUrl', 'formPreviewUrl'));
    }

    /**
     * معاينة نموذج التقديم كما يراه المتقدم — بدون إرسال طلب حقيقي.
     */
    public function formPreview()
    {
        $subjects = \App\Support\AcademicSubjectCatalog::allActive();
        $years = AcademicYear::where('is_active', true)->orderBy('order')->get();
        $phoneCountries = config('phone_countries.countries', []);
        $defaultCountry = collect($phoneCountries)->firstWhere('code', config('phone_countries.default_country', 'SA'));
        $formOptions = config('tutor_application');
        $formSteps = TutorFormSchemaService::completionSteps();
        $useDynamicForm = false;
        $totalSteps = max(1, $formSteps->count());
        $formPreview = true;
        $completeMode = true;
        $prefill = [
            'name' => 'معاينة نموذج',
            'email' => 'preview@sanaedu.com',
        ];

        return view('tutor.apply', compact(
            'subjects',
            'years',
            'phoneCountries',
            'defaultCountry',
            'formOptions',
            'useDynamicForm',
            'formSteps',
            'totalSteps',
            'formPreview',
            'completeMode',
            'prefill'
        ));
    }

    public function show(InstructorProfile $application)
    {
        $application = $this->resolveSubmittedApplication($application);

        $application->load(['user', 'reviewedByUser']);
        $subjects = AcademicSubject::whereIn('id', $application->tutor_subject_ids ?? [])->get();
        $years = AcademicYear::whereIn('id', $application->tutor_academic_year_ids ?? [])->get();

        return view('admin.instructor-applications.show', compact('application', 'subjects', 'years'));
    }

    /**
     * عرض/تحميل مرفق الطلب عبر مسار إداري مصادق — يعتمد على R2 مباشرة ويتجنب 404 مسار /media العام.
     */
    public function attachment(InstructorProfile $application, string $key): Response
    {
        $application = $this->resolveSubmittedApplication($application);
        $app = $application->application_data ?? [];

        $path = match ($key) {
            'demo_video' => $app['video']['file_path'] ?? null,
            default => $app['documents'][$key] ?? null,
        };

        if (! is_string($path) || $path === '') {
            abort(404, 'المرفق غير موجود');
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return redirect()->away($path);
        }

        $remote = CloudStorage::readFileContents($path, ['r2', 's3', 'public']);
        if ($remote === null) {
            Log::warning('instructor application attachment missing', [
                'application_id' => $application->id,
                'key' => $key,
                'path' => $path,
            ]);
            abort(404, 'تعذّر العثور على الملف في التخزين السحابي');
        }

        $headers = [
            'Content-Type' => $remote['mime'],
            'Cache-Control' => 'private, max-age=300',
            'Content-Disposition' => 'inline; filename="'.basename($path).'"',
        ];

        return response($remote['content'], 200, $headers);
    }

    public function edit(InstructorProfile $application)
    {
        $application = $this->resolveSubmittedApplication($application);

        $application->load('user');
        $subjects = AcademicSubject::where('is_active', true)->orderBy('name')->get();
        $years = AcademicYear::where('is_active', true)->orderBy('order')->get();

        return view('admin.instructor-applications.edit', compact('application', 'subjects', 'years'));
    }

    public function update(Request $request, InstructorProfile $application)
    {
        $application = $this->resolveSubmittedApplication($application);

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
            'instructor_portal_mode' => [
                Rule::requiredIf($application->status === InstructorProfile::STATUS_APPROVED),
                Rule::in(InstructorProfile::PORTAL_MODES),
            ],
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
        $application = $this->resolveSubmittedApplication($application);

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

        if ($isActive) {
            $email = $application->fresh()->user?->email;
            $mailNote = $email ? ' وتم إرسال رسالة تأكيد إلى '.$email.'.' : '';

            return back()->with('success', 'تم تفعيل حساب المعلم — يمكنه تسجيل الدخول.'.$mailNote);
        }

        return back()->with('success', 'تم إيقاف حساب المعلم — لن يتمكن من تسجيل الدخول.');
    }

    public function activateAccount(Request $request, InstructorProfile $application)
    {
        try {
            InstructorApplicationService::setAccountActive($application, $request->user(), true);
        } catch (\Throwable $e) {
            Log::error('instructor application activate account failed', [
                'application_id' => $application->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'تعذّر تفعيل الحساب.');
        }

        $email = $application->user?->email;
        $mailNote = $email
            ? ' وتم إرسال رسالة تأكيد إلى '.$email.'.'
            : '';

        return back()->with('success', 'تم تفعيل حساب المعلم.'.$mailNote);
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
            'instructor_portal_mode' => ['required', Rule::in(InstructorProfile::PORTAL_MODES)],
        ]);

        if ($application->status === InstructorProfile::STATUS_APPROVED) {
            return back()->with('info', 'هذا الطلب مقبول مسبقاً.');
        }

        try {
            InstructorApplicationService::approve(
                $application,
                $request->user(),
                $data['admin_note'] ?? null,
                $data['instructor_portal_mode']
            );
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
            ->with('success', 'تم قبول المعلم وتفعيل حسابه — سيظهر له: '.\App\Support\InstructorPortalAccess::modeLabel($data['instructor_portal_mode']).'.'.(
                $application->user?->email
                    ? ' وتم إرسال رسالة تأكيد إلى '.$application->user->email.'.'
                    : ''
            ));
    }

    public function saveEvaluation(Request $request, InstructorProfile $application)
    {
        $application = $this->resolveSubmittedApplication($application);

        $criteriaKeys = array_keys(config('tutor_application.evaluation_criteria', []));
        $decisionKeys = array_keys(config('tutor_application.evaluation_decisions', []));

        $data = $request->validate([
            'scores' => ['nullable', 'array'],
            'scores.*' => ['integer', 'min:1', 'max:4'],
            'decision' => ['nullable', 'string', Rule::in($decisionKeys)],
            'notes' => ['nullable', 'string', 'max:5000'],
            'reviewer_name' => ['nullable', 'string', 'max:120'],
        ]);

        $scores = [];
        foreach ($criteriaKeys as $key) {
            if (isset($data['scores'][$key])) {
                $scores[$key] = (int) $data['scores'][$key];
            }
        }

        $application->update([
            'application_evaluation' => [
                'scores' => $scores,
                'decision' => $data['decision'] ?? null,
                'notes' => $data['notes'] ?? null,
                'reviewer_name' => $data['reviewer_name'] ?? $request->user()?->name,
                'reviewed_at' => now()->toIso8601String(),
                'reviewer_id' => $request->user()?->id,
            ],
        ]);

        return back()->with('success', 'تم حفظ تقييم فريق التوظيف.');
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

    /**
     * طلبات التوظيف المعروضة للإدارة يجب أن تكون مقدَّمة فعلياً.
     * إن نقص submitted_at وكانت حالة الطلب قيد المراجعة/مقبولة/مرفوضة نرمّم التاريخ بدل 404.
     */
    private function resolveSubmittedApplication(InstructorProfile $application): InstructorProfile
    {
        if ($application->submitted_at) {
            return $application;
        }

        if (in_array($application->status, [
            InstructorProfile::STATUS_PENDING_REVIEW,
            InstructorProfile::STATUS_APPROVED,
            InstructorProfile::STATUS_REJECTED,
        ], true)) {
            $application->forceFill([
                'submitted_at' => $application->created_at ?? now(),
            ])->save();

            return $application->fresh() ?? $application;
        }

        abort(404);
    }
}
