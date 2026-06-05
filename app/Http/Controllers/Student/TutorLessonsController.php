<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\AcademicSubject;
use App\Models\AcademicYear;
use App\Models\InstructorProfile;
use App\Models\LessonBooking;
use App\Models\LessonBookingRating;
use App\Models\StudentLearningProfile;
use App\Models\TutorAssistedRequest;
use App\Models\User;
use App\Services\LessonBookingService;
use App\Services\TutorLessonQuotaService;
use App\Services\TutorNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TutorLessonsController extends Controller
{
    public function hub()
    {
        $user = Auth::user();
        $profile = TutorLessonQuotaService::syncProfileForUser($user);

        $upcoming = LessonBooking::where('student_id', $user->id)
            ->whereIn('status', [LessonBooking::STATUS_PENDING, LessonBooking::STATUS_CONFIRMED])
            ->where('scheduled_at', '>=', now())
            ->orderBy('scheduled_at')
            ->limit(5)
            ->with('instructor')
            ->get();

        return view('student.tutor-lessons.hub', compact('profile', 'upcoming'));
    }

    public function profile()
    {
        $profile = TutorLessonQuotaService::syncProfileForUser(Auth::user());
        $years = AcademicYear::where('is_active', true)->orderBy('order')->get();
        $subjects = AcademicSubject::where('is_active', true)->orderBy('name')->get();

        return view('student.tutor-lessons.profile', compact('profile', 'years', 'subjects'));
    }

    public function updateProfile(Request $request)
    {
        $data = $request->validate([
            'academic_year_id' => ['nullable', 'exists:academic_years,id'],
            'subject_ids' => ['required', 'array', 'min:1'],
            'subject_ids.*' => ['integer', 'exists:academic_subjects,id'],
            'curriculum_label' => ['nullable', 'string', 'max:120'],
            'grade_stage' => ['nullable', 'string', 'max:80'],
            'matching_mode' => ['required', 'in:assisted,self_schedule,pick_teacher'],
            'preferred_session_type' => ['required', 'in:one_to_one,small_group'],
            'assessment_notes' => ['nullable', 'string', 'max:3000'],
        ]);

        $profile = StudentLearningProfile::firstOrCreate(['user_id' => Auth::id()]);
        $profile->update([
            'academic_year_id' => $data['academic_year_id'] ?? null,
            'subject_ids' => $data['subject_ids'],
            'curriculum_label' => $data['curriculum_label'] ?? null,
            'grade_stage' => $data['grade_stage'] ?? null,
            'matching_mode' => $data['matching_mode'],
            'preferred_session_type' => $data['preferred_session_type'],
            'assessment_notes' => $data['assessment_notes'] ?? null,
            'assessed_at' => $data['assessment_notes'] ? now() : $profile->assessed_at,
        ]);

        return back()->with('success', 'تم حفظ ملفك الدراسي.');
    }

    public function schedule(LessonBookingService $service)
    {
        $profile = TutorLessonQuotaService::syncProfileForUser(Auth::user());
        if ($profile->matching_mode !== StudentLearningProfile::MODE_SELF_SCHEDULE) {
            return redirect()->route('student.tutor-lessons.profile')
                ->withErrors(['matching_mode' => __('tutor.self_schedule_mode_required')]);
        }

        $settings = TutorLessonQuotaService::settings();
        if (empty($settings['self_schedule_enabled'])) {
            return redirect()->route('student.tutor-lessons.hub')
                ->withErrors(['schedule' => __('tutor.self_schedule_disabled')]);
        }

        $subjectId = request()->integer('subject_id') ?: ($profile->subject_ids[0] ?? null);
        $slots = $service->availableSelfScheduleSlots($profile, $subjectId);
        $subjects = AcademicSubject::whereIn('id', $profile->subject_ids ?? [])->get();

        return view('student.tutor-lessons.schedule', compact('profile', 'slots', 'subjects', 'subjectId'));
    }

    public function scheduleBook(Request $request, LessonBookingService $service)
    {
        $profile = TutorLessonQuotaService::syncProfileForUser(Auth::user());

        $data = $request->validate([
            'scheduled_at' => ['required', 'date', 'after:now'],
            'academic_subject_id' => ['nullable', 'exists:academic_subjects,id'],
            'student_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $duration = (int) (TutorLessonQuotaService::settings()['default_duration_minutes'] ?? 60);

        $booking = $service->assignInstructorForSlot([
            'student_id' => Auth::id(),
            'scheduled_at' => $data['scheduled_at'],
            'duration_minutes' => $duration,
            'academic_subject_id' => $data['academic_subject_id'] ?? null,
            'session_type' => $profile->preferred_session_type,
            'student_notes' => $data['student_notes'] ?? null,
        ], Auth::user());

        return redirect()->route('student.tutor-lessons.bookings.show', $booking)
            ->with('success', __('tutor.self_schedule_booked'));
    }

    public function teachers(Request $request)
    {
        $profile = TutorLessonQuotaService::syncProfileForUser(Auth::user());

        if ($profile->matching_mode === StudentLearningProfile::MODE_SELF_SCHEDULE) {
            return redirect()->route('student.tutor-lessons.schedule');
        }
        $subjectId = $request->integer('subject_id') ?: ($profile->subject_ids[0] ?? null);

        $mode = $profile->matching_mode === StudentLearningProfile::MODE_ASSISTED
            ? StudentLearningProfile::MODE_PICK_TEACHER
            : $profile->matching_mode;

        $profiles = LessonBookingService::bookableInstructorsQuery($mode, $subjectId)->get();
        $subjects = AcademicSubject::whereIn('id', $profile->subject_ids ?? [])->get();

        return view('student.tutor-lessons.teachers', compact('profiles', 'profile', 'subjects', 'subjectId'));
    }

    public function bookForm(User $instructor)
    {
        if (! $instructor->isInstructor() && ! $instructor->isTeacher()) {
            abort(404);
        }
        $profile = $instructor->instructorProfile;
        if (! $profile?->isTutorActivated()) {
            return redirect()->route('student.tutor-lessons.teachers')
                ->withErrors(['instructor' => __('tutor.instructor_not_available')]);
        }

        $studentProfile = StudentLearningProfile::firstOrCreate(['user_id' => Auth::id()]);
        $availabilities = $instructor->tutorAvailabilities()->where('is_active', true)->get();
        $subjects = AcademicSubject::whereIn('id', $studentProfile->subject_ids ?? [])->get();

        return view('student.tutor-lessons.book', compact('instructor', 'profile', 'availabilities', 'studentProfile', 'subjects'));
    }

    public function book(Request $request, User $instructor, LessonBookingService $service)
    {
        $studentProfile = StudentLearningProfile::firstOrCreate(['user_id' => Auth::id()]);

        $data = $request->validate([
            'scheduled_at' => ['required', 'date', 'after:now'],
            'academic_subject_id' => ['nullable', 'exists:academic_subjects,id'],
            'session_type' => ['nullable', 'in:one_to_one,small_group'],
            'student_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $booking = $service->createBooking([
            'student_id' => Auth::id(),
            'instructor_id' => $instructor->id,
            'matching_mode' => $studentProfile->matching_mode,
            'session_type' => $data['session_type'] ?? $studentProfile->preferred_session_type,
            'scheduled_at' => $data['scheduled_at'],
            'duration_minutes' => $instructor->instructorProfile?->tutor_default_duration_minutes ?? 60,
            'academic_subject_id' => $data['academic_subject_id'] ?? null,
            'student_notes' => $data['student_notes'] ?? null,
        ], Auth::user());

        return redirect()->route('student.tutor-lessons.bookings.show', $booking)
            ->with('success', 'تم إرسال طلب الحصة.');
    }

    public function bookingsIndex()
    {
        $bookings = LessonBooking::where('student_id', Auth::id())
            ->with(['instructor', 'subject', 'classroomMeeting'])
            ->orderByDesc('scheduled_at')
            ->paginate(15);

        return view('student.tutor-lessons.bookings.index', compact('bookings'));
    }

    public function bookingsShow(LessonBooking $booking)
    {
        $this->authorizeStudent($booking);
        $booking->load(['instructor', 'subject', 'classroomMeeting', 'ratings']);

        return view('student.tutor-lessons.bookings.show', compact('booking'));
    }

    public function cancel(LessonBooking $booking, LessonBookingService $service)
    {
        $this->authorizeStudent($booking);
        $service->cancel($booking, 'student');

        return back()->with('success', 'تم إلغاء الحجز.');
    }

    public function rateForm(LessonBooking $booking)
    {
        $this->authorizeStudent($booking);

        return view('student.tutor-lessons.bookings.rate', compact('booking'));
    }

    public function rate(LessonBooking $booking, Request $request)
    {
        $this->authorizeStudent($booking);
        $data = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:2000'],
        ]);

        LessonBookingRating::updateOrCreate(
            ['lesson_booking_id' => $booking->id, 'rater_id' => Auth::id()],
            [
                'rated_user_id' => $booking->instructor_id,
                'rating' => $data['rating'],
                'comment' => $data['comment'] ?? null,
            ]
        );

        return redirect()->route('student.tutor-lessons.bookings.show', $booking)
            ->with('success', 'شكراً لتقييمك.');
    }

    public function assistedForm()
    {
        $profile = StudentLearningProfile::firstOrCreate(['user_id' => Auth::id()]);
        $subjects = AcademicSubject::where('is_active', true)->orderBy('name')->get();
        $years = AcademicYear::where('is_active', true)->orderBy('order')->get();

        return view('student.tutor-lessons.assisted', compact('profile', 'subjects', 'years'));
    }

    public function assistedStore(Request $request)
    {
        $data = $request->validate([
            'subject_ids' => ['required', 'array', 'min:1'],
            'academic_year_id' => ['nullable', 'exists:academic_years,id'],
            'preferred_session_type' => ['required', 'in:one_to_one,small_group'],
            'message' => ['required', 'string', 'max:3000'],
        ]);

        $req = TutorAssistedRequest::create([
            'student_id' => Auth::id(),
            'requested_by_user_id' => Auth::id(),
            'subject_ids' => $data['subject_ids'],
            'academic_year_id' => $data['academic_year_id'] ?? null,
            'preferred_session_type' => $data['preferred_session_type'],
            'message' => $data['message'],
            'status' => TutorAssistedRequest::STATUS_OPEN,
        ]);

        StudentLearningProfile::updateOrCreate(
            ['user_id' => Auth::id()],
            ['matching_mode' => StudentLearningProfile::MODE_ASSISTED]
        );

        TutorNotificationService::assistedRequestOpened($req);

        return redirect()->route('student.tutor-lessons.assisted.show', $req)
            ->with('success', 'تم إرسال طلب المساعدة.');
    }

    public function assistedShow(TutorAssistedRequest $assisted)
    {
        if ($assisted->student_id !== Auth::id()) {
            abort(403);
        }
        $assisted->load(['assignedInstructor', 'lessonBooking']);

        return view('student.tutor-lessons.assisted-show', compact('assisted'));
    }

    private function authorizeStudent(LessonBooking $booking): void
    {
        if ($booking->student_id !== Auth::id()) {
            abort(403);
        }
    }
}
