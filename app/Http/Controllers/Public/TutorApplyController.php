<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\AcademicSubject;
use App\Models\AcademicYear;
use App\Support\AcademicSubjectCatalog;
use App\Models\InstructorProfile;
use App\Models\User;
use App\Services\TutorApplicationFormService;
use App\Services\TutorNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class TutorApplyController extends Controller
{
    public function show()
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->isInstructor() || $user->isTeacher()) {
                if ($user->is_active) {
                    return redirect()->route('instructor.tutor-lessons.setup');
                }

                return redirect(TutorApplicationFormService::postApplyRedirect($user));
            }
        }

        $subjects = AcademicSubjectCatalog::allActive();
        $years = AcademicYear::where('is_active', true)->orderBy('order')->get();
        $phoneCountries = config('phone_countries.countries', []);
        $defaultCountry = collect($phoneCountries)->firstWhere('code', config('phone_countries.default_country', 'SA'));
        $formOptions = config('tutor_application');

        return view('tutor.apply', compact('subjects', 'years', 'phoneCountries', 'defaultCountry', 'formOptions'));
    }

    public function policy()
    {
        $user = Auth::user();
        if (! $user || (! $user->isInstructor() && ! $user->isTeacher())) {
            return redirect()->route('tutor.apply');
        }

        $profile = $user->instructorProfile;
        if (! $profile || $profile->status !== InstructorProfile::STATUS_PENDING_REVIEW) {
            return $user->is_active
                ? redirect()->route('instructor.tutor-lessons.hub')
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
        if (! $profile || $profile->status !== InstructorProfile::STATUS_PENDING_REVIEW) {
            return redirect()->route('tutor.apply.thanks');
        }

        $request->validate([
            'policy_agreed' => ['accepted'],
        ], [
            'policy_agreed.accepted' => 'يجب الموافقة على سياسة انضمام المعلمين للمتابعة.',
        ]);

        TutorApplicationFormService::markPolicyAccepted($profile->fresh());

        return redirect()
            ->route('instructor.tutor-lessons.setup')
            ->with('success', 'تمت الموافقة على السياسة. أكمل إعداد ملفك الآن.');
    }

    public function thanks()
    {
        $user = Auth::user();
        if ($user && ($user->isInstructor() || $user->isTeacher()) && ! $user->is_active) {
            $profile = $user->instructorProfile;
            if ($profile && ! TutorApplicationFormService::hasAcceptedPolicy($profile)) {
                return redirect()->route('tutor.apply.policy');
            }
            if ($profile && ! $profile->tutor_onboarding_completed_at) {
                return redirect()->route('instructor.tutor-lessons.setup');
            }
        }

        return view('tutor.apply-thanks', [
            'email' => session('apply_email', $user?->email),
        ]);
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
                if ($this->isPendingInstructorApplication($existing)) {
                    Auth::login($existing);

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
            $data = TutorApplicationFormService::validate($request);
        } catch (ValidationException $e) {
            throw $e->redirectTo(route('tutor.apply'));
        }

        $phone = trim((string) $data['phone']);
        $countryCode = trim((string) ($data['country_code'] ?? ''));
        $fullPhone = ($phone !== '' && $countryCode !== '') ? $countryCode.$phone : $phone;

        try {
            $user = DB::transaction(function () use ($data, $fullPhone, $request) {
                $user = User::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'phone' => $fullPhone,
                    'password' => Hash::make($data['password']),
                    'role' => 'instructor',
                    'is_active' => false,
                ]);

                $files = TutorApplicationFormService::storeUploadedFiles($request, $user->id);
                $applicationData = TutorApplicationFormService::buildApplicationData($data, $files);

                InstructorProfile::create([
                    'user_id' => $user->id,
                    'headline' => $data['headline'],
                    'bio' => $data['bio'],
                    'status' => InstructorProfile::STATUS_PENDING_REVIEW,
                    'offers_tutor_booking' => false,
                    'tutor_matching_modes' => $data['matching_modes'],
                    'tutor_session_types' => TutorApplicationFormService::sessionTypesFromFormats($data['lesson_formats']),
                    'tutor_subject_ids' => array_map('intval', $data['subject_ids']),
                    'tutor_academic_year_ids' => array_map('intval', $data['academic_year_ids']),
                    'tutor_years_experience' => $data['years_experience'],
                    'tutor_default_duration_minutes' => 60,
                    'tutor_onboarding_completed_at' => null,
                    'submitted_at' => now(),
                    'application_data' => $applicationData,
                ]);

                return $user;
            });
        } catch (\RuntimeException $e) {
            throw ValidationException::withMessages([
                'demo_video' => $e->getMessage(),
            ])->redirectTo(route('tutor.apply'));
        }

        try {
            TutorNotificationService::tutorApplicationSubmitted($user->fresh(['instructorProfile']));
        } catch (\Throwable $e) {
            Log::error('tutor apply notification failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()
            ->route('tutor.apply.policy')
            ->with('apply_email', $user->email)
            ->with('success', 'تم استلام طلبك. اقرأ السياسة ثم أكمل إعداد حسابك.');
    }

    private function isPendingInstructorApplication(User $user): bool
    {
        if (! in_array($user->role, ['instructor', 'teacher'], true) || $user->is_active) {
            return false;
        }

        $profile = $user->instructorProfile;

        return $profile !== null
            && $profile->status === InstructorProfile::STATUS_PENDING_REVIEW;
    }
}
