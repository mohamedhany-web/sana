<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicSubject;
use App\Models\AcademicYear;
use App\Models\InstructorProfile;
use App\Services\InstructorApplicationService;
use App\Services\TutorFormSchemaService;
use App\Support\AcademicSubjectCatalog;
use App\Support\CloudStorage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class InstructorApplicationsController extends Controller
{
    /** مفاتيح فلاتر الصفحة — تُستخدم لمسح/تمييز الفلاتر النشطة */
    public const INDEX_FILTER_KEYS = [
        'search',
        'status',
        'account',
        'portal_mode',
        'booking',
        'homepage',
        'subject_id',
        'academic_year_id',
        'specialization',
        'curriculum',
        'stage',
        'lesson_format',
        'matching_mode',
        'session_type',
        'experience_min',
        'experience_max',
        'nationality',
        'country_city',
        'form',
        'has_video',
        'eval_decision',
        'submitted_from',
        'submitted_to',
        'sort',
    ];

    public function index(Request $request)
    {
        // كل من قدّم طلباً أو تم تفعيله/اعتماده (حتى لو نُشّط من مسار إداري/تجريبي بدون submitted_at)
        $query = InstructorProfile::query()
            ->with('user')
            ->where(function ($q) {
                $q->whereNotNull('submitted_at')
                    ->orWhere('status', InstructorProfile::STATUS_APPROVED)
                    ->orWhere(function ($inner) {
                        $inner->where('offers_tutor_booking', true)
                            ->whereNotNull('tutor_activated_at');
                    });
            });

        $this->applyIndexFilters($query, $request);
        $this->applyIndexSort($query, $request);

        $applications = $query->paginate(20)->withQueryString();

        $base = InstructorProfile::query()->where(function ($q) {
            $q->whereNotNull('submitted_at')
                ->orWhere('status', InstructorProfile::STATUS_APPROVED)
                ->orWhere(function ($inner) {
                    $inner->where('offers_tutor_booking', true)
                        ->whereNotNull('tutor_activated_at');
                });
        });

        $stats = [
            'pending' => (clone $base)->where('status', InstructorProfile::STATUS_PENDING_REVIEW)->count(),
            'approved' => (clone $base)->where('status', InstructorProfile::STATUS_APPROVED)->count(),
            'rejected' => (clone $base)->where('status', InstructorProfile::STATUS_REJECTED)->count(),
            'total' => (clone $base)->count(),
            'active_accounts' => (clone $base)->whereHas('user', fn ($q) => $q->where('is_active', true))->count(),
            'inactive_accounts' => (clone $base)->whereHas('user', fn ($q) => $q->where('is_active', false))->count(),
        ];

        $filterOptions = [
            'subjects' => AcademicSubjectCatalog::allActive(),
            'years' => AcademicYear::where('is_active', true)->orderBy('order')->get(['id', 'name']),
            'specializations' => config('tutor_application.specializations', []),
            'curricula' => config('tutor_application.curricula', []),
            'stages' => config('tutor_application.stages', []),
            'lesson_formats' => config('tutor_application.lesson_formats', []),
            'evaluation_decisions' => config('tutor_application.evaluation_decisions', []),
            'matching_modes' => [
                'pick_teacher' => __('tutor.matching_pick_teacher'),
                'self_schedule' => __('tutor.matching_self_schedule'),
                'assisted' => __('tutor.matching_assisted'),
            ],
            'session_types' => [
                'one_to_one' => __('tutor.session_one_to_one'),
                'small_group' => __('tutor.session_small_group'),
            ],
            'portal_modes' => [
                InstructorProfile::PORTAL_BOTH => 'حصص وكورسات',
                InstructorProfile::PORTAL_TUTOR_LESSONS => 'حصص خاصة فقط',
                InstructorProfile::PORTAL_COURSES => 'كورسات فقط',
            ],
        ];

        $activeFilters = collect(self::INDEX_FILTER_KEYS)
            ->filter(fn (string $key) => filled($request->input($key)))
            ->values()
            ->all();

        $publicApplyUrl = route('tutor.apply');
        $formPreviewUrl = route('admin.instructor-applications.form-preview');

        return view('admin.instructor-applications.index', compact(
            'applications',
            'stats',
            'publicApplyUrl',
            'formPreviewUrl',
            'filterOptions',
            'activeFilters'
        ));
    }

    /**
     * تطبيق فلاتر قائمة انضمام المعلمين (بيانات أساسية + بيانات النموذج).
     */
    public function applyIndexFilters(Builder $query, Request $request): void
    {
        if ($status = $request->string('status')->toString()) {
            $query->where('status', $status);
        }

        if ($request->string('account')->toString() === 'active') {
            $query->whereHas('user', fn ($q) => $q->where('is_active', true));
        } elseif ($request->string('account')->toString() === 'inactive') {
            $query->whereHas('user', fn ($q) => $q->where('is_active', false));
        }

        if ($portalMode = $request->string('portal_mode')->toString()) {
            if (in_array($portalMode, InstructorProfile::PORTAL_MODES, true)) {
                $query->where('instructor_portal_mode', $portalMode);
            }
        }

        $booking = $request->string('booking')->toString();
        if ($booking === 'activated') {
            $query->where('offers_tutor_booking', true)->whereNotNull('tutor_activated_at');
        } elseif ($booking === 'not_activated') {
            $query->where(function ($q) {
                $q->where('offers_tutor_booking', false)
                    ->orWhereNull('tutor_activated_at');
            });
        } elseif ($booking === 'offering') {
            $query->where('offers_tutor_booking', true);
        }

        $homepage = $request->string('homepage')->toString();
        if ($homepage === 'yes') {
            $query->where('show_on_homepage', true);
        } elseif ($homepage === 'no') {
            $query->where('show_on_homepage', false);
        }

        if ($subjectId = $request->integer('subject_id')) {
            $query->where(function ($q) use ($subjectId) {
                $q->whereJsonContains('tutor_subject_ids', $subjectId)
                    ->orWhereJsonContains('tutor_subject_ids', (string) $subjectId);
            });
        }

        if ($yearId = $request->integer('academic_year_id')) {
            $query->where(function ($q) use ($yearId) {
                $q->whereJsonContains('tutor_academic_year_ids', $yearId)
                    ->orWhereJsonContains('tutor_academic_year_ids', (string) $yearId);
            });
        }

        if ($specialization = $request->string('specialization')->toString()) {
            $query->whereJsonContains('application_data->teaching->specializations', $specialization);
        }

        if ($curriculum = $request->string('curriculum')->toString()) {
            $query->whereJsonContains('application_data->teaching->curricula', $curriculum);
        }

        if ($stage = $request->string('stage')->toString()) {
            $query->whereJsonContains('application_data->teaching->stages', $stage);
        }

        if ($lessonFormat = $request->string('lesson_format')->toString()) {
            $query->whereJsonContains('application_data->teaching->lesson_formats', $lessonFormat);
        }

        if ($matchingMode = $request->string('matching_mode')->toString()) {
            $query->whereJsonContains('tutor_matching_modes', $matchingMode);
        }

        if ($sessionType = $request->string('session_type')->toString()) {
            $query->whereJsonContains('tutor_session_types', $sessionType);
        }

        if ($request->filled('experience_min')) {
            $query->where('tutor_years_experience', '>=', max(0, $request->integer('experience_min')));
        }

        if ($request->filled('experience_max')) {
            $query->where('tutor_years_experience', '<=', max(0, $request->integer('experience_max')));
        }

        if ($nationality = $request->string('nationality')->trim()->toString()) {
            $query->where('application_data->personal->nationality', 'like', '%'.$nationality.'%');
        }

        if ($countryCity = $request->string('country_city')->trim()->toString()) {
            $query->where('application_data->personal->country_city', 'like', '%'.$countryCity.'%');
        }

        $form = $request->string('form')->toString();
        if ($form === 'full') {
            $query->whereNotNull('application_data');
        } elseif ($form === 'basic') {
            $query->whereNull('application_data');
        }

        $hasVideo = $request->string('has_video')->toString();
        if ($hasVideo === 'yes') {
            $query->where(function ($q) {
                $q->where(function ($inner) {
                    $inner->whereNotNull('application_data->video->link')
                        ->where('application_data->video->link', '!=', '');
                })->orWhere(function ($inner) {
                    $inner->whereNotNull('application_data->video->file_path')
                        ->where('application_data->video->file_path', '!=', '');
                });
            });
        } elseif ($hasVideo === 'no') {
            $query->where(function ($q) {
                $q->whereNull('application_data')
                    ->orWhere(function ($inner) {
                        $inner->where(function ($v) {
                            $v->whereNull('application_data->video->link')
                                ->orWhere('application_data->video->link', '');
                        })->where(function ($v) {
                            $v->whereNull('application_data->video->file_path')
                                ->orWhere('application_data->video->file_path', '');
                        });
                    });
            });
        }

        if ($evalDecision = $request->string('eval_decision')->toString()) {
            $query->where('application_evaluation->decision', $evalDecision);
        }

        if ($from = $request->string('submitted_from')->toString()) {
            $query->whereDate('submitted_at', '>=', $from);
        }

        if ($to = $request->string('submitted_to')->toString()) {
            $query->whereDate('submitted_at', '<=', $to);
        }

        if ($search = $request->string('search')->trim()->toString()) {
            $like = '%'.$search.'%';
            $query->where(function ($q) use ($like, $search) {
                $q->where('headline', 'like', $like)
                    ->orWhere('bio', 'like', $like)
                    ->orWhere('application_data->personal->nationality', 'like', $like)
                    ->orWhere('application_data->personal->country_city', 'like', $like)
                    ->orWhere('application_data->qualification->specialization', 'like', $like)
                    ->orWhere('application_data->qualification->degree_qualification', 'like', $like)
                    ->orWhereHas('user', function ($userQuery) use ($like) {
                        $userQuery->where('name', 'like', $like)
                            ->orWhere('email', 'like', $like)
                            ->orWhere('phone', 'like', $like);
                    });

                if (ctype_digit($search)) {
                    $q->orWhere('id', (int) $search)
                        ->orWhere('user_id', (int) $search);
                }
            });
        }
    }

    private function applyIndexSort(Builder $query, Request $request): void
    {
        $sort = $request->string('sort')->toString();

        match ($sort) {
            'oldest' => $query->orderBy('submitted_at')->orderBy('id'),
            'name' => $query->join('users', 'users.id', '=', 'instructor_profiles.user_id')
                ->select('instructor_profiles.*')
                ->orderBy('users.name')
                ->orderByDesc('instructor_profiles.id'),
            'experience_desc' => $query->orderByDesc('tutor_years_experience')->orderByDesc('id'),
            'experience_asc' => $query->orderBy('tutor_years_experience')->orderByDesc('id'),
            'updated' => $query->orderByDesc('updated_at')->orderByDesc('id'),
            default => $query->orderByDesc('submitted_at')
                ->orderByDesc('tutor_activated_at')
                ->orderByDesc('updated_at'),
        };
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

    /**
     * إيقاف/تفعيل الظهور العام فقط — لا يمس تسجيل الدخول أو الحساب.
     */
    public function toggleHomepage(Request $request, InstructorProfile $application)
    {
        try {
            $visible = InstructorApplicationService::toggleHomepageVisibility($application, $request->user());
        } catch (\Throwable $e) {
            Log::error('instructor application toggle homepage failed', [
                'application_id' => $application->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'تعذّر تغيير ظهور المعلم.');
        }

        if ($visible) {
            return back()->with('success', 'تم إظهار المعلم على الرئيسية وقائمة المعلمين. الحساب والحجز لم يتأثرا.');
        }

        return back()->with('success', 'تم إيقاف ظهور المعلم للعامة. الحساب ما زال يعمل ويمكنه تسجيل الدخول.');
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
            ->with('success', 'تم قبول المعلم وتفعيل حسابه وإتاحته للطلاب في صفحة اختيار المعلم'.
                ' — سيظهر له: '.\App\Support\InstructorPortalAccess::modeLabel($data['instructor_portal_mode']).'.'.(
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
     * عرض ملف المعلم للإدارة: إن وُجد السجل نعرضه.
     * إن نقص submitted_at نرمّمه عند الاعتماد/التفعيل/المراجعة بدل إرجاع 404
     * (كان يحدث مع معلمين مفعّلين من مسار إداري/تجريبي بحالة draft).
     */
    private function resolveSubmittedApplication(InstructorProfile $application): InstructorProfile
    {
        if ($application->submitted_at) {
            return $application;
        }

        $application->loadMissing('user');

        $shouldBackfill = in_array($application->status, [
            InstructorProfile::STATUS_PENDING_REVIEW,
            InstructorProfile::STATUS_APPROVED,
            InstructorProfile::STATUS_REJECTED,
        ], true)
            || ((bool) $application->offers_tutor_booking && $application->tutor_activated_at !== null)
            || ($application->user?->is_active && in_array((string) ($application->user->role ?? ''), ['instructor', 'teacher'], true));

        if ($shouldBackfill) {
            $application->forceFill([
                'submitted_at' => $application->created_at ?? now(),
            ])->save();

            return $application->fresh() ?? $application;
        }

        // مسودة غير مفعّلة: ما زال يُسمح بعرضها للأدمن بدل 404
        return $application;
    }
}
