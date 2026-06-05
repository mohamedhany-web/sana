<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\AcademicSubject;
use App\Models\AcademicYear;
use App\Models\InstructorProfile;
use App\Models\User;
use App\Services\TutorNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;

class TutorApplyController extends Controller
{
    public function show()
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->isInstructor() || $user->isTeacher()) {
                if (! $user->is_active) {
                    return redirect()->route('tutor.apply.thanks');
                }

                return redirect()->route('instructor.tutor-lessons.setup');
            }
        }

        $subjects = AcademicSubject::where('is_active', true)->orderBy('name')->get();
        $years = AcademicYear::where('is_active', true)->orderBy('order')->get();
        $phoneCountries = config('phone_countries.countries', []);
        $defaultCountry = collect($phoneCountries)->firstWhere('code', config('phone_countries.default_country', 'SA'));

        return view('tutor.apply', compact('subjects', 'years', 'phoneCountries', 'defaultCountry'));
    }

    public function thanks()
    {
        return view('tutor.apply-thanks');
    }

    public function store(Request $request)
    {
        if (Auth::check() && (Auth::user()->isInstructor() || Auth::user()->isTeacher())) {
            return Auth::user()->is_active
                ? redirect()->route('instructor.tutor-lessons.setup')
                : redirect()->route('tutor.apply.thanks');
        }

        $email = strtolower(trim((string) $request->input('email')));
        if ($email !== '') {
            $existing = User::query()
                ->whereRaw('LOWER(email) = ?', [$email])
                ->with('instructorProfile')
                ->first();

            if ($existing && $this->isPendingInstructorApplication($existing)) {
                return redirect()
                    ->route('tutor.apply.thanks')
                    ->with('apply_email', $existing->email)
                    ->with('info', __('tutor.apply_already_submitted'));
            }
        }

        try {
            $data = $request->validate(
                [
                    'name' => ['required', 'string', 'max:120'],
                    'email' => ['required', 'email', 'max:255', 'unique:users,email'],
                    'country_code' => ['nullable', 'string', 'max:10'],
                    'phone' => ['nullable', 'string', 'max:30'],
                    'password' => ['required', 'confirmed', Rules\Password::defaults()],
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
                ],
                $this->applyValidationMessages(),
                $this->applyValidationAttributes()
            );
        } catch (ValidationException $e) {
            throw $e->redirectTo(route('tutor.apply'));
        }

        $phone = isset($data['phone']) ? trim((string) $data['phone']) : '';
        $countryCode = trim((string) ($data['country_code'] ?? ''));
        $fullPhone = ($phone !== '' && $countryCode !== '') ? $countryCode.$phone : ($phone ?: null);

        $user = DB::transaction(function () use ($data, $fullPhone) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $fullPhone,
                'password' => Hash::make($data['password']),
                'role' => 'instructor',
                'is_active' => false,
            ]);

            InstructorProfile::create([
                'user_id' => $user->id,
                'headline' => $data['headline'],
                'bio' => $data['bio'],
                'status' => InstructorProfile::STATUS_PENDING_REVIEW,
                'offers_tutor_booking' => false,
                'tutor_matching_modes' => $data['matching_modes'],
                'tutor_session_types' => $data['session_types'],
                'tutor_subject_ids' => array_map('intval', $data['subject_ids']),
                'tutor_academic_year_ids' => array_map('intval', $data['academic_year_ids']),
                'tutor_years_experience' => $data['years_experience'],
                'tutor_default_duration_minutes' => 60,
                'tutor_onboarding_completed_at' => now(),
                'submitted_at' => now(),
            ]);

            return $user;
        });

        try {
            TutorNotificationService::tutorApplicationSubmitted($user->fresh(['instructorProfile']));
        } catch (\Throwable $e) {
            Log::error('tutor apply notification failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }

        return redirect()
            ->route('tutor.apply.thanks')
            ->with('apply_email', $user->email);
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

    /** @return array<string, string> */
    private function applyValidationMessages(): array
    {
        return [
            'required' => __('tutor.apply_validation.required'),
            'email' => __('tutor.apply_validation.email'),
            'email.unique' => __('tutor.apply_validation.email_unique'),
            'confirmed' => __('tutor.apply_validation.password_confirmed'),
            'password.min' => __('tutor.apply_validation.password_min'),
            'subject_ids.required' => __('tutor.apply_validation.subjects_required'),
            'subject_ids.min' => __('tutor.apply_validation.subjects_required'),
            'academic_year_ids.required' => __('tutor.apply_validation.years_required'),
            'academic_year_ids.min' => __('tutor.apply_validation.years_required'),
            'matching_modes.required' => __('tutor.apply_validation.matching_required'),
            'session_types.required' => __('tutor.apply_validation.sessions_required'),
        ];
    }

    /** @return array<string, string> */
    private function applyValidationAttributes(): array
    {
        return [
            'name' => __('tutor.apply_validation.attr_name'),
            'email' => __('tutor.apply_validation.attr_email'),
            'password' => __('tutor.apply_validation.attr_password'),
            'headline' => __('tutor.apply_validation.attr_headline'),
            'bio' => __('tutor.apply_validation.attr_bio'),
            'years_experience' => __('tutor.apply_validation.attr_years'),
            'subject_ids' => __('tutor.apply_validation.attr_subjects'),
            'academic_year_ids' => __('tutor.apply_validation.attr_years_study'),
        ];
    }
}
