<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\LessonBooking;
use App\Models\LessonBookingRating;
use App\Services\LessonBookingService;
use App\Services\TutorNotificationService;
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

        $needsStudentRating = $booking->status === LessonBooking::STATUS_COMPLETED
            && ! $booking->instructor_rated_at
            && ! $booking->ratings->firstWhere('rater_id', Auth::id());

        if ($needsStudentRating && ! request()->boolean('skip_rate')) {
            return redirect()
                ->route('instructor.tutor-lessons.bookings.rate', $booking)
                ->with('info', 'يجب تقييم الطالب بعد انتهاء الحصة. سيصل التقييم لولي الأمر كإشعار.');
        }

        return view('instructor.tutor-lessons.bookings.show', compact('booking', 'needsStudentRating'));
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

        return redirect()
            ->route('instructor.tutor-lessons.bookings.rate', $booking)
            ->with('success', 'تم إنهاء الحصة وخصم الساعات. يرجى تقييم الطالب الآن — سيصل التقييم لولي الأمر.');
    }

    public function sendReminder(LessonBooking $booking)
    {
        $this->authorizeInstructor($booking);

        if (! in_array($booking->status, [
            LessonBooking::STATUS_CONFIRMED,
            LessonBooking::STATUS_IN_PROGRESS,
        ], true)) {
            return back()->with('error', __('tutor.reminder_not_allowed'));
        }

        TutorNotificationService::bookingReminder($booking);
        if (! $booking->reminder_sent_at) {
            $booking->update(['reminder_sent_at' => now()]);
        }

        return back()->with('success', __('tutor.reminder_sent'));
    }

    public function rateForm(LessonBooking $booking)
    {
        $this->authorizeInstructor($booking);

        if ($booking->status !== LessonBooking::STATUS_COMPLETED) {
            return redirect()
                ->route('instructor.tutor-lessons.bookings.show', $booking)
                ->with('error', 'يمكن تقييم الطالب بعد إنهاء الحصة فقط.');
        }

        return view('instructor.tutor-lessons.bookings.rate', compact('booking'));
    }

    public function rate(LessonBooking $booking, Request $request)
    {
        $this->authorizeInstructor($booking);

        if ($booking->status !== LessonBooking::STATUS_COMPLETED) {
            return back()->with('error', 'يمكن تقييم الطالب بعد إنهاء الحصة فقط.');
        }

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

        $booking->update(['instructor_rated_at' => now()]);
        TutorNotificationService::studentRatedByInstructor(
            $booking->fresh(),
            (int) $data['rating'],
            $data['comment'] ?? null
        );

        return redirect()->route('instructor.tutor-lessons.bookings.show', $booking)
            ->with('success', 'تم حفظ تقييم الطالب وإرسال إشعار لولي الأمر.');
    }

    private function authorizeInstructor(LessonBooking $booking): void
    {
        if ($booking->instructor_id !== Auth::id()) {
            abort(403);
        }
    }
}
