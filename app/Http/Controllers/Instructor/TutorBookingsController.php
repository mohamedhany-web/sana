<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\LessonBooking;
use App\Models\LessonBookingRating;
use App\Services\LessonBookingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TutorBookingsController extends Controller
{
    public function index(Request $request)
    {
        $bookings = LessonBooking::query()
            ->where('instructor_id', Auth::id())
            ->with(['student', 'subject', 'classroomMeeting'])
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->orderByDesc('scheduled_at')
            ->paginate(15);

        return view('instructor.tutor-lessons.bookings.index', compact('bookings'));
    }

    public function show(LessonBooking $booking)
    {
        $this->authorizeInstructor($booking);

        $booking->load(['student', 'subject', 'classroomMeeting', 'ratings.rater']);

        return view('instructor.tutor-lessons.bookings.show', compact('booking'));
    }

    public function confirm(LessonBooking $booking, Request $request, LessonBookingService $service)
    {
        $this->authorizeInstructor($booking);
        $service->confirm($booking, $request->input('instructor_notes'));

        return back()->with('success', 'تم تأكيد الحصة وإنشاء غرفة الاجتماع.');
    }

    public function cancel(LessonBooking $booking, LessonBookingService $service)
    {
        $this->authorizeInstructor($booking);
        $service->cancel($booking, 'instructor');

        return back()->with('success', 'تم إلغاء الحجز.');
    }

    public function complete(LessonBooking $booking, LessonBookingService $service)
    {
        $this->authorizeInstructor($booking);
        $service->complete($booking);

        return back()->with('success', 'تم إنهاء الحصة وتسجيل الدقائق.');
    }

    public function rateForm(LessonBooking $booking)
    {
        $this->authorizeInstructor($booking);

        return view('instructor.tutor-lessons.bookings.rate', compact('booking'));
    }

    public function rate(LessonBooking $booking, Request $request)
    {
        $this->authorizeInstructor($booking);
        $data = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:2000'],
        ]);

        LessonBookingRating::updateOrCreate(
            [
                'lesson_booking_id' => $booking->id,
                'rater_id' => Auth::id(),
            ],
            [
                'rated_user_id' => $booking->student_id,
                'rating' => $data['rating'],
                'comment' => $data['comment'] ?? null,
            ]
        );

        return redirect()->route('instructor.tutor-lessons.bookings.show', $booking)
            ->with('success', 'شكراً لتقييمك.');
    }

    private function authorizeInstructor(LessonBooking $booking): void
    {
        if ($booking->instructor_id !== Auth::id()) {
            abort(403);
        }
    }
}
