<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\AcademicSubject;
use App\Models\AcademicYear;
use App\Models\InstructorProfile;
use App\Models\LessonBooking;
use App\Models\StudentLearningProfile;
use App\Models\User;
use App\Services\LessonBookingService;
use App\Services\TutorInstructorActivationService;
use App\Support\AcademicSubjectCatalog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class TutorSetupController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        $profile = InstructorProfile::firstOrCreate(
            ['user_id' => $user->id],
            ['status' => InstructorProfile::STATUS_DRAFT]
        );

        $subjects = AcademicSubjectCatalog::allActive();
        $years = AcademicYear::where('is_active', true)->orderBy('order')->get();
        $availabilities = $user->tutorAvailabilities()->orderBy('day_of_week')->get();

        $needsTrial = ! $profile->tutor_trial_completed_at;
        $trialBooking = LessonBooking::where('instructor_id', $user->id)
            ->where('is_trial', true)
            ->latest()
            ->first();

        $activationBlockers = TutorInstructorActivationService::activationBlockers($profile, $user);

        return view('instructor.tutor-lessons.setup', compact(
            'profile',
            'subjects',
            'years',
            'availabilities',
            'needsTrial',
            'trialBooking',
            'activationBlockers'
        ));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $profile = InstructorProfile::firstOrCreate(['user_id' => $user->id]);

        $data = $request->validate([
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
            'default_duration' => ['required', 'integer', 'min:30', 'max:180'],
        ]);

        $validSubjectIds = AcademicSubjectCatalog::assertActiveSubjectIds($data['subject_ids']);

        $profile->update([
            'headline' => $data['headline'],
            'bio' => $data['bio'],
            'tutor_years_experience' => $data['years_experience'],
            'tutor_subject_ids' => $validSubjectIds,
            'tutor_academic_year_ids' => array_map('intval', $data['academic_year_ids']),
            'tutor_matching_modes' => $data['matching_modes'],
            'tutor_session_types' => $data['session_types'],
            'tutor_default_duration_minutes' => $data['default_duration'],
            'tutor_onboarding_completed_at' => now(),
            'submitted_at' => $profile->submitted_at ?? now(),
            'status' => $profile->isTutorActivated()
                ? InstructorProfile::STATUS_APPROVED
                : InstructorProfile::STATUS_PENDING_REVIEW,
        ]);

        $activated = TutorInstructorActivationService::attemptAutoActivate($profile->fresh(), $user);

        $message = $activated
            ? 'تم حفظ الملف وتفعيل حسابك — يمكن للطلاب اختيارك الآن.'
            : 'تم حفظ ملف المعلم. أضف جدولاً أسبوعياً (وفعّل نمط اختيار معلم) ليظهر حسابك للطلاب.';

        return back()->with('success', $message);
    }

    public function scheduleTrial(Request $request, LessonBookingService $service)
    {
        $user = Auth::user();
        $profile = $user->instructorProfile;
        if ($profile?->tutor_trial_completed_at) {
            return back()->with('info', 'اكتملت الجلسة التجريبية مسبقاً.');
        }

        $data = $request->validate([
            'scheduled_at' => ['required', 'date', 'after:now'],
        ]);

        $admin = User::whereIn('role', ['admin', 'super_admin'])->where('is_active', true)->first();
        if (! $admin) {
            return back()->withErrors(['scheduled_at' => 'لا يوجد مسؤول للجلسة التجريبية. تواصل مع الدعم.']);
        }

        try {
            $service->createBooking([
                'student_id' => $admin->id,
                'instructor_id' => $user->id,
                'matching_mode' => StudentLearningProfile::MODE_SELF_SCHEDULE,
                'session_type' => StudentLearningProfile::SESSION_ONE_TO_ONE,
                'scheduled_at' => $data['scheduled_at'],
                'duration_minutes' => 30,
                'is_trial' => true,
                'student_notes' => 'جلسة تجريبية لتفعيل حساب المعلم',
            ], $user);
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }

        return back()->with('success', 'تم حجز الجلسة التجريبية. أكّدها وأنهِها من قائمة الحجوزات بعد الموعد لتفعيل الحساب (إن لم يُفعَّل تلقائياً).');
    }
}
