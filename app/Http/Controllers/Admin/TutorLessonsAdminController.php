<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InstructorProfile;
use App\Models\LessonBooking;
use App\Models\TutorAssistedRequest;
use App\Models\User;
use App\Services\LessonBookingService;
use App\Services\TutorNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        $bookings = $q->paginate(25)->withQueryString();

        return view('admin.tutor-lessons.bookings', compact('bookings'));
    }

    public function bookingShow(LessonBooking $booking)
    {
        $booking->load(['student', 'instructor', 'subject', 'classroomMeeting', 'ratings']);

        return view('admin.tutor-lessons.booking-show', compact('booking'));
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
