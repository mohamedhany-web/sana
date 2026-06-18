<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\AcademicSubject;
use App\Models\AcademicYear;
use App\Models\LessonBooking;
use App\Models\StudentLearningProfile;
use App\Models\TutorAssistedRequest;
use App\Models\User;
use App\Services\LessonBookingService;
use App\Services\TutorGroupOfferService;
use App\Services\TutorNotificationService;
use App\Support\AcademicSubjectCatalog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TutorLessonsController extends Controller
{
    public function hub()
    {
        $children = $this->children();
        $bookings = LessonBooking::query()
            ->whereIn('student_id', $children->pluck('id'))
            ->orderByDesc('scheduled_at')
            ->limit(10)
            ->with(['student', 'instructor'])
            ->get();

        return view('parent.tutor-lessons.hub', compact('children', 'bookings'));
    }

    public function assistedForm()
    {
        $children = $this->children();
        $subjects = AcademicSubjectCatalog::allActive();
        $years = AcademicYear::where('is_active', true)->orderBy('order')->get();

        return view('parent.tutor-lessons.assisted', compact('children', 'subjects', 'years'));
    }

    public function assistedStore(Request $request)
    {
        $data = $request->validate([
            'student_id' => ['required', 'integer'],
            'subject_ids' => ['required', 'array', 'min:1'],
            'academic_year_id' => ['nullable', 'exists:academic_years,id'],
            'preferred_session_type' => ['required', 'in:one_to_one,small_group'],
            'message' => ['required', 'string', 'max:3000'],
        ]);

        $childrenIds = $this->children()->pluck('id')->all();
        if (! in_array((int) $data['student_id'], $childrenIds, true)) {
            abort(403);
        }

        $req = TutorAssistedRequest::create([
            'student_id' => $data['student_id'],
            'parent_id' => Auth::id(),
            'requested_by_user_id' => Auth::id(),
            'subject_ids' => $data['subject_ids'],
            'academic_year_id' => $data['academic_year_id'] ?? null,
            'preferred_session_type' => $data['preferred_session_type'],
            'message' => $data['message'],
            'status' => TutorAssistedRequest::STATUS_OPEN,
        ]);

        StudentLearningProfile::updateOrCreate(
            ['user_id' => $data['student_id']],
            ['matching_mode' => StudentLearningProfile::MODE_ASSISTED]
        );

        TutorNotificationService::assistedRequestOpened($req);

        return redirect()->route('parent.tutor-lessons.assisted.show', $req)
            ->with('success', 'تم إرسال الطلب. سنتواصل معكم عبر المنصة.');
    }

    public function assistedShow(TutorAssistedRequest $assisted)
    {
        if ($assisted->parent_id !== Auth::id() && $assisted->requested_by_user_id !== Auth::id()) {
            abort(403);
        }
        $assisted->load(['student', 'assignedInstructor']);

        return view('parent.tutor-lessons.assisted-show', compact('assisted'));
    }

    public function bookingsShow(LessonBooking $booking)
    {
        $childrenIds = $this->children()->pluck('id')->all();
        if (! in_array($booking->student_id, $childrenIds, true)) {
            abort(403);
        }
        $booking->load(['student', 'instructor', 'classroomMeeting']);

        return view('parent.tutor-lessons.booking-show', compact('booking'));
    }

    public function bookForm(User $instructor, Request $request)
    {
        $children = $this->children();
        $studentId = (int) $request->query('student_id');
        if (! $children->contains('id', $studentId)) {
            abort(403);
        }

        $profile = $instructor->instructorProfile;
        $availabilities = $instructor->tutorAvailabilities()->where('is_active', true)->get();
        $student = User::findOrFail($studentId);
        $studentProfile = StudentLearningProfile::firstOrCreate(['user_id' => $studentId]);
        $subjects = AcademicSubject::whereIn('id', $studentProfile->subject_ids ?? [])->get();
        $groupOffers = TutorGroupOfferService::offersForStudentInstructor($student, $instructor);
        $groupLimits = TutorGroupOfferService::groupLimitsForUser($student);

        return view('parent.tutor-lessons.book', compact(
            'instructor',
            'profile',
            'availabilities',
            'student',
            'studentProfile',
            'subjects',
            'groupOffers',
            'groupLimits'
        ));
    }

    public function book(Request $request, User $instructor, LessonBookingService $service)
    {
        $data = $request->validate([
            'student_id' => ['required', 'integer'],
            'scheduled_at' => ['required', 'date', 'after:now'],
            'academic_subject_id' => ['nullable', 'exists:academic_subjects,id'],
            'session_type' => ['nullable', 'in:one_to_one,small_group'],
            'tutor_group_offer_id' => ['nullable', 'integer', 'exists:tutor_group_offers,id'],
            'student_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $childrenIds = $this->children()->pluck('id')->all();
        if (! in_array((int) $data['student_id'], $childrenIds, true)) {
            abort(403);
        }

        $studentProfile = StudentLearningProfile::firstOrCreate(['user_id' => $data['student_id']]);

        $booking = $service->createBooking([
            'student_id' => $data['student_id'],
            'parent_id' => Auth::id(),
            'instructor_id' => $instructor->id,
            'matching_mode' => $studentProfile->matching_mode,
            'session_type' => $data['session_type'] ?? $studentProfile->preferred_session_type,
            'scheduled_at' => $data['scheduled_at'],
            'duration_minutes' => $instructor->instructorProfile?->tutor_default_duration_minutes ?? 60,
            'academic_subject_id' => $data['academic_subject_id'] ?? null,
            'tutor_group_offer_id' => $data['tutor_group_offer_id'] ?? null,
            'student_notes' => $data['student_notes'] ?? null,
        ], Auth::user());

        return redirect()->route('parent.tutor-lessons.bookings.show', $booking)
            ->with('success', 'تم إرسال طلب الحصة لابنك/ابنتك.');
    }

    private function children()
    {
        return Auth::user()->linkedStudents()->where('role', 'student')->get();
    }
}
