<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\AcademicSubject;
use App\Models\AcademicYear;
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
        $formOptions = config('tutor_application');

        return view('tutor.apply', compact('subjects', 'years', 'phoneCountries', 'defaultCountry', 'formOptions'));
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
                    'tutor_onboarding_completed_at' => now(),
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
}
