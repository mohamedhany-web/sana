<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InstructorProfile;
use App\Models\LessonBooking;
use App\Models\TutorAssistedRequest;
use App\Models\TutorGroupOffer;
use App\Models\User;
use App\Services\LessonBookingService;
use App\Services\TutorNotificationService;
use App\Support\AcademicSubjectCatalog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class TutorLessonsAdminController extends Controller
{
    public function index()
    {
        $stats = [
            'active_tutors' => InstructorProfile::offersTutorBooking()->count(),
            'pending_bookings' => LessonBooking::where('status', LessonBooking::STATUS_PENDING)->count(),
            'upcoming' => LessonBooking::whereIn('status', [LessonBooking::STATUS_PENDING, LessonBooking::STATUS_CONFIRMED])
                ->where('scheduled_at', '>=', now())
                ->count(),
            'open_assisted' => TutorAssistedRequest::where('status', TutorAssistedRequest::STATUS_OPEN)->count(),
        ];

        $recentBookings = LessonBooking::with(['student', 'instructor'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return view('admin.tutor-lessons.index', compact('stats', 'recentBookings'));
    }

    public function bookings(Request $request)
    {
        $q = LessonBooking::with(['student', 'instructor', 'subject'])
            ->orderByDesc('scheduled_at');

        if ($status = $request->string('status')->toString()) {
            $q->where('status', $status);
        }

        if ($request->boolean('admin_only')) {
            $q->whereHas('requestedBy', function ($rq) {
                $rq->where(function ($inner) {
                    $inner->whereIn('role', ['admin', 'super_admin'])
                        ->orWhere('is_employee', true);
                });
            });
        }

        if ($groupKey = $request->string('group')->toString()) {
            $q->where('group_session_key', $groupKey);
        }

        $bookings = $q->paginate(25)->withQueryString();

        return view('admin.tutor-lessons.bookings', compact('bookings'));
    }

    public function bookCreate()
    {
        $instructors = InstructorProfile::offersTutorBooking()
            ->with('user')
            ->orderByDesc('tutor_activated_at')
            ->get();

        $students = User::query()
            ->where('role', 'student')
            ->where('is_active', true)
            ->orderBy('name')
            ->limit(500)
            ->get(['id', 'name', 'email', 'phone']);

        $subjects = AcademicSubjectCatalog::allActive();
        $groupOffers = TutorGroupOffer::query()
            ->active()
            ->with(['instructor', 'subject'])
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get();

        return view('admin.tutor-lessons.book-create', compact(
            'instructors',
            'students',
            'subjects',
            'groupOffers'
        ));
    }

    public function bookStore(Request $request, LessonBookingService $bookingService)
    {
        $data = $request->validate([
            'session_type' => ['required', 'in:one_to_one,small_group'],
            'instructor_id' => ['required', 'exists:users,id'],
            'student_ids' => ['required', 'array', 'min:1'],
            'student_ids.*' => ['integer', 'exists:users,id'],
            'academic_subject_id' => ['nullable', 'exists:academic_subjects,id'],
            'tutor_group_offer_id' => ['nullable', 'exists:tutor_group_offers,id'],
            'max_group_size' => ['nullable', 'integer', 'min:2', 'max:30'],
            'scheduled_at' => ['required', 'date', 'after:now'],
            'duration_minutes' => ['required', 'integer', 'min:15', 'max:240'],
            'confirmation_mode' => ['required', 'in:await_instructor,confirm_now'],
            'enforce_quota' => ['nullable', 'boolean'],
            'ignore_availability_window' => ['nullable', 'boolean'],
            'student_notes' => ['nullable', 'string', 'max:2000'],
            'instructor_notes' => ['nullable', 'string', 'max:2000'],
        ], [
            'student_ids.required' => 'اختر طالباً واحداً على الأقل.',
            'scheduled_at.after' => 'الموعد يجب أن يكون في المستقبل.',
        ]);

        $data['enforce_quota'] = $request->boolean('enforce_quota', true);
        $data['ignore_availability_window'] = $request->boolean('ignore_availability_window');

        try {
            $bookings = $bookingService->createAdminBookings($data, $request->user());
        } catch (ValidationException $e) {
            throw $e->redirectTo(route('admin.tutor-lessons.book.create'));
        }

        $count = $bookings->count();
        $first = $bookings->first();
        $modeLabel = ($data['confirmation_mode'] ?? '') === 'confirm_now'
            ? 'مع التأكيد وإنشاء Classroom'
            : 'بانتظار تأكيد المعلم';

        $message = $count === 1
            ? 'تم إنشاء الحجز '.$first->code.' '.$modeLabel.'.'
            : 'تم إنشاء '.$count.' حجوزات مجموعة '.$modeLabel.'.';

        if ($first?->group_session_key) {
            return redirect()
                ->route('admin.tutor-lessons.bookings', ['group' => $first->group_session_key])
                ->with('success', $message);
        }

        return redirect()
            ->route('admin.tutor-lessons.bookings.show', $first)
            ->with('success', $message);
    }

    public function bookSearchStudents(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        if (mb_strlen($q) < 2) {
            return response()->json([]);
        }

        $students = User::query()
            ->where('role', 'student')
            ->where('is_active', true)
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', '%'.$q.'%')
                    ->orWhere('email', 'like', '%'.$q.'%')
                    ->orWhere('phone', 'like', '%'.$q.'%');
            })
            ->orderBy('name')
            ->limit(25)
            ->get(['id', 'name', 'email', 'phone']);

        return response()->json($students);
    }

    public function bookingShow(LessonBooking $booking)
    {
        $booking->load(['student', 'instructor', 'subject', 'classroomMeeting', 'ratings']);

        $groupBookings = collect();
        if ($booking->group_session_key) {
            $groupBookings = LessonBooking::with(['student'])
                ->where('group_session_key', $booking->group_session_key)
                ->orderBy('id')
                ->get();
        }

        return view('admin.tutor-lessons.booking-show', compact('booking', 'groupBookings'));
    }

    public function instructors()
    {
        $profiles = InstructorProfile::query()
            ->with('user')
            ->where(function ($q) {
                $q->whereNotNull('tutor_onboarding_completed_at')
                    ->orWhere('offers_tutor_booking', true)
                    ->orWhereNotNull('submitted_at');
            })
            ->orderByDesc('updated_at')
            ->paginate(20);

        return view('admin.tutor-lessons.instructors', compact('profiles'));
    }

    public function activate(InstructorProfile $profile)
    {
        $profile->update([
            'offers_tutor_booking' => true,
            'tutor_activated_at' => now(),
            'status' => InstructorProfile::STATUS_APPROVED,
        ]);
        $profile->user?->update(['is_active' => true]);

        TutorNotificationService::notify(
            $profile->user_id,
            'تم تفعيل حساب المعلم',
            'يمكنك الآن استقبال حجوزات الطلاب على المنصة.',
            route('instructor.tutor-lessons.hub'),
            __('tutor.open_hub'),
            'instructor'
        );

        return back()->with('success', 'تم تفعيل المعلم.');
    }

    public function assistedIndex()
    {
        $requests = TutorAssistedRequest::with(['student', 'parent', 'assignedInstructor'])
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.tutor-lessons.assisted-index', compact('requests'));
    }

    public function assistedShow(TutorAssistedRequest $assisted)
    {
        $assisted->load(['student', 'parent', 'assignedInstructor', 'lessonBooking']);
        $instructors = InstructorProfile::offersTutorBooking()->with('user')->get();

        return view('admin.tutor-lessons.assisted-show', compact('assisted', 'instructors'));
    }

    public function assistedAssign(Request $request, TutorAssistedRequest $assisted, LessonBookingService $bookingService)
    {
        $data = $request->validate([
            'assigned_instructor_id' => ['required', 'exists:users,id'],
            'scheduled_at' => ['required', 'date', 'after:now'],
        ]);

        $booking = $bookingService->createBooking([
            'student_id' => $assisted->student_id,
            'parent_id' => $assisted->parent_id,
            'instructor_id' => $data['assigned_instructor_id'],
            'matching_mode' => 'assisted',
            'session_type' => $assisted->preferred_session_type,
            'scheduled_at' => $data['scheduled_at'],
            'duration_minutes' => 60,
            'tutor_assisted_request_id' => $assisted->id,
            'student_notes' => $assisted->message,
        ], Auth::user());

        $assisted->update([
            'status' => TutorAssistedRequest::STATUS_ASSIGNED,
            'assigned_instructor_id' => $data['assigned_instructor_id'],
            'lesson_booking_id' => $booking->id,
            'assigned_at' => now(),
        ]);

        TutorNotificationService::assistedRequestAssigned($assisted->fresh());

        return back()->with('success', 'تم تعيين المعلم وإنشاء حجز.');
    }
}
