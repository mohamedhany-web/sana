<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Mail\TutorApplicationReceivedMail;
use App\Models\AcademicYear;
use App\Models\InstructorProfile;
use App\Models\User;
use App\Services\TutorApplicationFormService;
use App\Services\TutorFormSchemaService;
use App\Services\TutorNotificationService;
use App\Support\AcademicSubjectCatalog;
use App\Support\InstructorPortalAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class TutorApplyController extends Controller
{
    public function show()
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->isInstructor() || $user->isTeacher()) {
                return redirect(TutorApplicationFormService::postApplyRedirect($user));
            }
        }

        $phoneCountries = config('phone_countries.countries', []);
        $defaultCountry = collect($phoneCountries)->firstWhere('code', config('phone_countries.default_country', 'SA'));

        return view('tutor.apply-register', compact('phoneCountries', 'defaultCountry'));
    }

    public function policy()
    {
        $user = Auth::user();
        if (! $user || (! $user->isInstructor() && ! $user->isTeacher())) {
            return redirect()->route('tutor.apply');
        }

        $profile = $user->instructorProfile;
        if (! $profile || ! in_array($profile->status, [
            InstructorProfile::STATUS_DRAFT,
            InstructorProfile::STATUS_PENDING_REVIEW,
        ], true)) {
            return $user->is_active
                ? redirect()->route(InstructorPortalAccess::homeRoute($user))
                : redirect()->route('tutor.apply.thanks');
        }

        if (TutorApplicationFormService::hasAcceptedPolicy($profile)) {
            return redirect(TutorApplicationFormService::postApplyRedirect($user));
        }

        $sections = __('teacher_policy.sections');

        return view('tutor.apply-policy', compact('user', 'sections'));
    }

    public function acceptPolicy(Request $request)
    {
        $user = Auth::user();
        if (! $user || (! $user->isInstructor() && ! $user->isTeacher())) {
            return redirect()->route('tutor.apply');
        }

        $profile = $user->instructorProfile;
        if (! $profile || ! in_array($profile->status, [
            InstructorProfile::STATUS_DRAFT,
            InstructorProfile::STATUS_PENDING_REVIEW,
        ], true)) {
            return redirect()->route('tutor.apply.thanks');
        }

        $request->validate([
            'policy_agreed' => ['accepted'],
        ], [
            'policy_agreed.accepted' => 'يجب الموافقة على سياسة انضمام المعلمين للمتابعة.',
        ]);

        TutorApplicationFormService::markPolicyAccepted($profile->fresh());

        return redirect()
            ->route('instructor.tutor-lessons.hub')
            ->with('success', 'تمت الموافقة على السياسة. أكمل باقي ملفك من لوحة التحكم ثم أرسله للإدارة.');
    }

    public function thanks()
    {
        $user = Auth::user();
        if ($user && ($user->isInstructor() || $user->isTeacher())) {
            $profile = $user->instructorProfile;
            if ($profile && ! TutorApplicationFormService::hasAcceptedPolicy($profile)
                && in_array($profile->status, [InstructorProfile::STATUS_DRAFT, InstructorProfile::STATUS_PENDING_REVIEW], true)) {
                return redirect()->route('tutor.apply.policy');
            }
            if ($profile?->needsApplicationCompletion()) {
                return redirect()
                    ->route('instructor.tutor-lessons.hub')
                    ->with('info', __('tutor.complete_application_banner'));
            }
        }

        return view('tutor.apply-thanks', [
            'email' => session('apply_email', $user?->email),
        ]);
    }

    public function completeForm()
    {
        if (! Auth::check()) {
            return view('tutor.apply-complete-gate');
        }

        $user = Auth::user();
        if (! $user->isInstructor() && ! $user->isTeacher()) {
            return redirect()
                ->route('tutor.apply')
                ->with('info', 'إكمال ملف التقديم مخصص لحسابات المعلمين. أنشئ حساب معلّم أولاً.');
        }

        $profile = $user->instructorProfile;
        if (! $profile) {
            return redirect()->route('tutor.apply');
        }

        if ($profile->isAwaitingAdminReview()) {
            return redirect()->route('tutor.apply.thanks')
                ->with('info', 'تم إرسال ملفك مسبقاً وهو قيد مراجعة الإدارة.');
        }

        if ($profile->status === InstructorProfile::STATUS_APPROVED) {
            return redirect()->route(InstructorPortalAccess::homeRoute($user));
        }

        if (! $profile->needsApplicationCompletion() && $profile->status !== InstructorProfile::STATUS_REJECTED) {
            return redirect(TutorApplicationFormService::postApplyRedirect($user));
        }

        if (! TutorApplicationFormService::hasAcceptedPolicy($profile)) {
            return redirect()->route('tutor.apply.policy');
        }

        return $this->renderCompleteFormView($user, $profile);
    }

    public function completeStore(Request $request)
    {
        $user = Auth::user();
        if (! $user || (! $user->isInstructor() && ! $user->isTeacher())) {
            return redirect()->route('tutor.apply');
        }

        $profile = $user->instructorProfile;
        if (! $profile || (! $profile->needsApplicationCompletion() && $profile->status !== InstructorProfile::STATUS_REJECTED)) {
            return redirect(TutorApplicationFormService::postApplyRedirect($user));
        }

        if (! TutorApplicationFormService::hasAcceptedPolicy($profile)) {
            return redirect()->route('tutor.apply.policy');
        }

        try {
            $data = TutorApplicationFormService::validateCompletion($request);
        } catch (ValidationException $e) {
            throw $e->redirectTo(route('tutor.apply.complete'));
        }

        try {
            $profile = DB::transaction(function () use ($data, $request, $user, $profile) {
                $user->update([
                    'name' => $data['name'],
                ]);

                $files = TutorApplicationFormService::storeUploadedFiles($request, $user->id);
                $existingPersonal = $profile->application_data['personal'] ?? [];
                $applicationData = TutorApplicationFormService::buildApplicationData($data, $files);
                $applicationData['personal'] = array_merge($existingPersonal, $applicationData['personal'] ?? []);
                if (! empty($profile->application_data['policy'])) {
                    $applicationData['policy'] = $profile->application_data['policy'];
                }
                $applicationData['completed_in_portal_at'] = now()->toIso8601String();

                $profile->update([
                    'headline' => $data['headline'] ?? $profile->headline ?? 'معلم',
                    'bio' => $data['bio'] ?? $profile->bio ?? '',
                    'status' => InstructorProfile::STATUS_PENDING_REVIEW,
                    'offers_tutor_booking' => false,
                    'tutor_matching_modes' => $data['matching_modes'] ?? ['pick_teacher'],
                    'tutor_session_types' => TutorApplicationFormService::sessionTypesFromFormats($data['lesson_formats'] ?? []),
                    'tutor_subject_ids' => array_map('intval', $data['subject_ids'] ?? []),
                    'tutor_academic_year_ids' => array_map('intval', $data['academic_year_ids'] ?? []),
                    'tutor_years_experience' => (int) ($data['years_experience'] ?? 0),
                    'tutor_default_duration_minutes' => 60,
                    'submitted_at' => now(),
                    'rejection_reason' => null,
                    'application_data' => $applicationData,
                ]);

                return $profile->fresh();
            });
        } catch (ValidationException $e) {
            throw $e->redirectTo(route('tutor.apply.complete'));
        } catch (\RuntimeException $e) {
            throw ValidationException::withMessages([
                'demo_video' => $e->getMessage(),
            ])->redirectTo(route('tutor.apply.complete'));
        } catch (\Throwable $e) {
            Log::error('tutor apply complete failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            throw ValidationException::withMessages([
                'name' => 'تعذّر إرسال الملف حالياً. تأكد من اتصال التخزين السحابي ثم حاول مرة أخرى.',
            ])->redirectTo(route('tutor.apply.complete'));
        }

        try {
            TutorNotificationService::tutorApplicationSubmitted($user->fresh(), $profile);
        } catch (\Throwable $e) {
            Log::error('tutor apply complete notification failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }

        try {
            Mail::to($user->email)->send(new TutorApplicationReceivedMail($user->fresh()));
        } catch (\Throwable $e) {
            Log::error('tutor application received mail failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }

        return redirect()
            ->route('tutor.apply.thanks')
            ->with('apply_email', $user->email)
            ->with('success', 'تم استلام ملفك وإرساله للإدارة بنجاح.');
    }

    public function store(Request $request)
    {
        if (Auth::check() && (Auth::user()->isInstructor() || Auth::user()->isTeacher())) {
            return redirect(TutorApplicationFormService::postApplyRedirect(Auth::user()));
        }

        $request->merge([
            'linkedin_url' => trim((string) $request->input('linkedin_url')) ?: null,
        ]);

        $email = strtolower(trim((string) $request->input('email')));
        if ($email !== '') {
            $existing = User::query()
                ->whereRaw('LOWER(email) = ?', [$email])
                ->with('instructorProfile')
                ->first();

            if ($existing) {
                if (TutorApplicationFormService::isOpenInstructorAccount($existing)) {
                    Auth::login($existing);
                    $request->session()->regenerate();

                    return redirect(TutorApplicationFormService::postApplyRedirect($existing))
                        ->with('apply_email', $existing->email)
                        ->with('info', __('tutor.apply_already_submitted'));
                }

                throw ValidationException::withMessages([
                    'email' => __('tutor.apply_validation.email_unique'),
                ])->redirectTo(route('tutor.apply'));
            }
        }

        try {
            $data = TutorApplicationFormService::validateRegistration($request);
        } catch (ValidationException $e) {
            throw $e->redirectTo(route('tutor.apply'));
        }

        $phone = trim((string) $data['phone']);
        $countryCode = trim((string) ($data['country_code'] ?? ''));
        $fullPhone = ($phone !== '' && $countryCode !== '') ? $countryCode.$phone : $phone;

        if ($fullPhone !== '') {
            $existingByPhone = User::query()
                ->where('phone', $fullPhone)
                ->with('instructorProfile')
                ->first();

            if ($existingByPhone) {
                if (TutorApplicationFormService::isOpenInstructorAccount($existingByPhone)) {
                    Auth::login($existingByPhone);
                    $request->session()->regenerate();

                    return redirect(TutorApplicationFormService::postApplyRedirect($existingByPhone))
                        ->with('apply_email', $existingByPhone->email)
                        ->with('info', __('tutor.apply_already_submitted'));
                }

                throw ValidationException::withMessages([
                    'phone' => __('tutor.apply_validation.phone_unique'),
                ])->redirectTo(route('tutor.apply'));
            }
        }

        try {
            [$user] = DB::transaction(function () use ($data, $fullPhone) {
                $user = User::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'phone' => $fullPhone,
                    'password' => Hash::make($data['password']),
                    'role' => 'instructor',
                    'is_active' => true,
                ]);

                InstructorProfile::create([
                    'user_id' => $user->id,
                    'headline' => 'معلم',
                    'bio' => '',
                    'status' => InstructorProfile::STATUS_DRAFT,
                    'offers_tutor_booking' => false,
                    'tutor_matching_modes' => ['pick_teacher'],
                    'tutor_session_types' => ['one_to_one'],
                    'tutor_subject_ids' => [],
                    'tutor_academic_year_ids' => [],
                    'tutor_years_experience' => 0,
                    'tutor_default_duration_minutes' => 60,
                    'tutor_onboarding_completed_at' => null,
                    'submitted_at' => null,
                    'application_data' => [
                        'personal' => [
                            'nationality' => $data['nationality'] ?? null,
                            'country_city' => $data['country_city'] ?? null,
                            'linkedin_url' => $data['linkedin_url'] ?? null,
                        ],
                        'registration_only' => true,
                        'account_created_at' => now()->toIso8601String(),
                        'form_version' => '2026-07-signup-first',
                    ],
                ]);

                return [$user];
            });
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('tutor apply register query failed', [
                'email' => $data['email'] ?? null,
                'phone' => $fullPhone,
                'error' => $e->getMessage(),
            ]);

            $msg = $e->getMessage();
            if (str_contains($msg, 'users_phone_unique') || (str_contains($msg, 'Duplicate entry') && str_contains($msg, 'phone'))) {
                throw ValidationException::withMessages([
                    'phone' => __('tutor.apply_validation.phone_unique'),
                ])->redirectTo(route('tutor.apply'));
            }
            if (str_contains($msg, 'users_email_unique') || (str_contains($msg, 'Duplicate entry') && str_contains($msg, 'email'))) {
                throw ValidationException::withMessages([
                    'email' => __('tutor.apply_validation.email_unique'),
                ])->redirectTo(route('tutor.apply'));
            }

            throw ValidationException::withMessages([
                'email' => 'تعذّر إكمال التسجيل حالياً. حاول مرة أخرى أو تواصل مع الدعم.',
            ])->redirectTo(route('tutor.apply'));
        } catch (\Throwable $e) {
            Log::error('tutor apply register failed', [
                'email' => $data['email'] ?? null,
                'error' => $e->getMessage(),
            ]);

            throw ValidationException::withMessages([
                'email' => 'تعذّر إكمال التسجيل حالياً. حاول مرة أخرى.',
            ])->redirectTo(route('tutor.apply'));
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()
            ->route('tutor.apply.policy')
            ->with('apply_email', $user->email)
            ->with('success', 'تم إنشاء حسابك. اقرأ السياسة ثم أكمل باقي ملفك من لوحة التحكم.');
    }

    private function renderCompleteFormView(User $user, InstructorProfile $profile)
    {
        $subjects = AcademicSubjectCatalog::allActive();
        $years = AcademicYear::where('is_active', true)->orderBy('order')->get();
        $phoneCountries = config('phone_countries.countries', []);
        $defaultCountry = collect($phoneCountries)->firstWhere('code', config('phone_countries.default_country', 'SA'));
        $formOptions = config('tutor_application');
        // مراحل الإكمال من منشئ النماذج (إلزامي/اختياري/إخفاء) — العرض بـ JS عادي بدون Alpine
        $formSteps = TutorFormSchemaService::completionSteps();
        $useDynamicForm = false;
        $totalSteps = max(1, $formSteps->count());
        $completeMode = true;
        $formPreview = false;
        $prefill = [
            'name' => $user->name,
            'email' => $user->email,
            'nationality' => $profile->application_data['personal']['nationality'] ?? '',
            'country_city' => $profile->application_data['personal']['country_city'] ?? '',
            'linkedin_url' => $profile->application_data['personal']['linkedin_url'] ?? '',
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
            'completeMode',
            'formPreview',
            'prefill',
            'user',
            'profile'
        ));
    }
}
