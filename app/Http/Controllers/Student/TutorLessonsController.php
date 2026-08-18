<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\AcademicSubject;
use App\Models\AcademicYear;
use App\Models\InstructorProfile;
use App\Models\LessonBooking;
use App\Models\LessonBookingRating;
use App\Models\LessonSessionRecording;
use App\Models\StudentLearningProfile;
use App\Models\TutorAssistedRequest;
use App\Models\User;
use App\Services\InstructorApplicationService;
use App\Services\LessonBookingService;
use App\Services\TutorGroupOfferService;
use App\Services\TutorLessonQuotaService;
use App\Services\TutorNotificationService;
use App\Support\AcademicSubjectCatalog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TutorLessonsController extends Controller
{
    public function hub()
    {
        $user = Auth::user();
        $profile = TutorLessonQuotaService::syncProfileForUser($user);

        $upcoming = LessonBooking::where('student_id', $user->id)
            ->where(function ($q) {
                $q->where(function ($upcoming) {
                    $upcoming->whereIn('status', [
                        LessonBooking::STATUS_PENDING,
                        LessonBooking::STATUS_CONFIRMED,
                    ])->where('scheduled_at', '>=', now());
                })->orWhere('status', LessonBooking::STATUS_IN_PROGRESS);
            })
            ->orderBy('scheduled_at')
            ->limit(5)
            ->with(['instructor', 'classroomMeeting'])
            ->get();

        return view('student.tutor-lessons.hub', compact('profile', 'upcoming'));
    }

    public function profile()
    {
        return redirect()->route('student.tutor-lessons.teachers');
    }

    public function updateProfile(Request $request)
    {
        return redirect()->route('student.tutor-lessons.teachers');
    }

    public function schedule(Request $request, LessonBookingService $service)
    {
        $profile = TutorLessonQuotaService::syncProfileForUser(Auth::user());
        if ($profile->matching_mode !== StudentLearningProfile::MODE_SELF_SCHEDULE) {
            return redirect()->route('student.tutor-lessons.teachers');
        }

        $settings = TutorLessonQuotaService::settings();
        if (empty($settings['self_schedule_enabled'])) {
            return redirect()->route('student.tutor-lessons.hub')
                ->withErrors(['schedule' => __('tutor.self_schedule_disabled')]);
        }

        $subjectId = $request->filled('subject_id') ? $request->integer('subject_id') : null;
        $slots = $service->availableSelfScheduleSlots($profile, $subjectId);
        $subjects = AcademicSubjectCatalog::allActive();

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
        $subjectId = $request->filled('subject_id') ? $request->integer('subject_id') : null;
        $search = trim((string) $request->input('q', ''));

        $query = LessonBookingService::studentVisibleInstructorsQuery($subjectId);
        if ($search !== '') {
            $like = '%'.addcslashes($search, '%_\\').'%';
            $query->where(function ($inner) use ($like) {
                $inner->whereHas('user', fn ($userQuery) => $userQuery->where('name', 'like', $like))
                    ->orWhere('headline', 'like', $like);
            });
        }

        $profiles = $query->get();
        foreach ($profiles as $instructorProfile) {
            InstructorApplicationService::enableStudentBooking($instructorProfile);
        }

        $filterSubjects = AcademicSubjectCatalog::allActive();
        $subjectIds = $profiles
            ->flatMap(fn ($p) => is_array($p->tutor_subject_ids) ? $p->tutor_subject_ids : [])
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->values()
            ->all();
        $subjects = $subjectIds === []
            ? collect()
            : AcademicSubject::query()->whereIn('id', $subjectIds)->get();

        return view('student.tutor-lessons.teachers', compact(
            'profiles',
            'profile',
            'subjects',
            'filterSubjects',
            'subjectId',
            'search'
        ));
    }

    public function bookForm(User $instructor, LessonBookingService $bookingService)
    {
        if (! $instructor->isInstructor() && ! $instructor->isTeacher()) {
            abort(404);
        }
        $profile = $instructor->instructorProfile;
        if ($profile && $profile->status === InstructorProfile::STATUS_APPROVED) {
            InstructorApplicationService::enableStudentBooking($profile);
            $profile->refresh();
        }
        if (! $profile || $profile->status !== InstructorProfile::STATUS_APPROVED || ! $profile->hasTutorLessonsPortal()) {
            return redirect()->route('student.tutor-lessons.teachers')
                ->withErrors(['instructor' => __('tutor.instructor_not_available')]);
        }

        $student = Auth::user();
        $studentProfile = StudentLearningProfile::firstOrCreate(['user_id' => $student->id]);
        $availabilities = $instructor->tutorAvailabilities()->where('is_active', true)->get();
        $catalogSubjects = AcademicSubjectCatalog::allActive();
        $teacherSubjectIds = collect($profile->tutor_subject_ids ?? [])
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->all();
        $subjects = $teacherSubjectIds === []
            ? $catalogSubjects
            : $catalogSubjects->sortBy(fn ($s) => in_array((int) $s->id, $teacherSubjectIds, true) ? 0 : 1)->values();
        $groupOffers = TutorGroupOfferService::offersForStudentInstructor($student, $instructor);
        $groupLimits = TutorGroupOfferService::groupLimitsForUser($student);

        $duration = (int) ($profile->tutor_default_duration_minutes ?? 60);
        $sessionType = $profile->resolveSessionType(
            (string) ($studentProfile->preferred_session_type ?? StudentLearningProfile::SESSION_ONE_TO_ONE)
        );
        if ($sessionType === StudentLearningProfile::SESSION_SMALL_GROUP && $groupOffers->isEmpty()) {
            $sessionType = StudentLearningProfile::SESSION_ONE_TO_ONE;
        }

        $availableSlots = $bookingService->availableSlotsForInstructor(
            (int) $instructor->id,
            $duration,
            $sessionType,
            1,
            null,
            14
        );

        return view('student.tutor-lessons.book', compact(
            'instructor',
            'profile',
            'availabilities',
            'studentProfile',
            'subjects',
            'groupOffers',
            'groupLimits',
            'availableSlots',
            'duration'
        ));
    }

    public function book(Request $request, User $instructor, LessonBookingService $service)
    {
        $studentProfile = StudentLearningProfile::firstOrCreate(['user_id' => Auth::id()]);
        if ($instructor->instructorProfile) {
            InstructorApplicationService::enableStudentBooking($instructor->instructorProfile);
            $instructor->load('instructorProfile');
        }

        $data = $request->validate([
            'scheduled_at' => ['required', 'date', 'after:now'],
            'academic_subject_id' => ['nullable', 'exists:academic_subjects,id'],
            'session_type' => ['nullable', 'in:one_to_one,small_group'],
            'tutor_group_offer_id' => ['nullable', 'integer', 'exists:tutor_group_offers,id'],
            'student_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $duration = (int) ($instructor->instructorProfile?->tutor_default_duration_minutes ?? 60);
        $sessionType = $data['session_type'] ?? $studentProfile->preferred_session_type ?? StudentLearningProfile::SESSION_ONE_TO_ONE;
        if ($instructor->instructorProfile) {
            $sessionType = $instructor->instructorProfile->resolveSessionType((string) $sessionType);
        }
        $scheduledAt = \Carbon\Carbon::parse($data['scheduled_at']);

        if (! $service->isSlotAvailable(
            (int) $instructor->id,
            $scheduledAt,
            $duration,
            $sessionType,
            1,
            null,
            true
        )) {
            return back()
                ->withInput()
                ->withErrors(['scheduled_at' => __('tutor.slot_not_available')]);
        }

        $booking = $service->createBooking([
            'student_id' => Auth::id(),
            'instructor_id' => $instructor->id,
            'matching_mode' => StudentLearningProfile::MODE_PICK_TEACHER,
            'session_type' => $sessionType,
            'scheduled_at' => $scheduledAt,
            'duration_minutes' => $duration,
            'academic_subject_id' => $data['academic_subject_id'] ?? null,
            'tutor_group_offer_id' => $data['tutor_group_offer_id'] ?? null,
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
        $booking->load(['instructor.instructorProfile', 'subject', 'classroomMeeting', 'ratings']);

        $lessonRecording = null;
        if (\Illuminate\Support\Facades\Schema::hasTable('lesson_session_recordings')) {
            $lessonRecording = LessonSessionRecording::query()
                ->where(function ($q) use ($booking) {
                    $q->where('lesson_booking_id', $booking->id);
                    if ($booking->classroom_meeting_id) {
                        $q->orWhere(function ($inner) use ($booking) {
                            $inner->where('classroom_meeting_id', $booking->classroom_meeting_id)
                                ->where('student_id', $booking->student_id);
                        });
                    }
                })
                ->latest('id')
                ->first();
        }

        return view('student.tutor-lessons.bookings.show', compact('booking', 'lessonRecording'));
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
        $subjects = AcademicSubjectCatalog::allActive();
        $years = AcademicYear::where('is_active', true)->orderBy('order')->get();

        return view('student.tutor-lessons.assisted', compact('profile', 'subjects', 'years'));
    }

    public function assistedStore(Request $request)
    {
        $data = $request->validate([
            'subject_ids' => ['required', 'array', 'min:1'],
            'subject_ids.*' => ['integer', 'exists:academic_subjects,id'],
            'academic_year_id' => ['nullable', 'exists:academic_years,id'],
            'preferred_session_type' => ['required', 'in:one_to_one,small_group'],
            'message' => ['required', 'string', 'max:3000'],
        ]);

        $validSubjectIds = AcademicSubjectCatalog::assertActiveSubjectIds(
            $data['subject_ids'],
            isset($data['academic_year_id']) ? (int) $data['academic_year_id'] : null
        );

        $req = TutorAssistedRequest::create([
            'student_id' => Auth::id(),
            'requested_by_user_id' => Auth::id(),
            'subject_ids' => $validSubjectIds,
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
