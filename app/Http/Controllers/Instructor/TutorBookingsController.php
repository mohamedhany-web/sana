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
            ->with(['student', 'subject', 'classroomMeeting', 'ratings'])
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->boolean('needs_evaluation'), function ($q) {
                $q->where('status', LessonBooking::STATUS_COMPLETED)
                    ->whereDoesntHave('ratings', fn ($r) => $r->where('rater_id', Auth::id()));
            })
            ->orderByDesc('scheduled_at')
            ->paginate(15)
            ->withQueryString();

        return view('instructor.tutor-lessons.bookings.index', compact('bookings'));
    }

    public function show(LessonBooking $booking)
    {
        $this->authorizeInstructor($booking);

        $booking->load(['student', 'subject', 'classroomMeeting', 'ratings.rater']);

        $needsStudentRating = $booking->status === LessonBooking::STATUS_COMPLETED
            && ! $booking->hasInstructorEvaluation();

        if ($needsStudentRating && ! request()->boolean('skip_rate')) {
            return redirect()
                ->route('instructor.tutor-lessons.bookings.rate', $booking)
                ->with('info', __('tutor.evaluation_required_banner'));
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
            ->with('success', __('tutor.complete_then_rate_required'));
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
                ->with('error', __('tutor.rate_only_after_complete'));
        }

        $existing = $booking->instructorEvaluation();

        return view('instructor.tutor-lessons.bookings.rate', compact('booking', 'existing'));
    }

    public function rate(LessonBooking $booking, Request $request)
    {
        $this->authorizeInstructor($booking);

        if ($booking->status !== LessonBooking::STATUS_COMPLETED) {
            return redirect()
                ->route('instructor.tutor-lessons.bookings.show', $booking)
                ->with('error', __('tutor.rate_only_after_complete'));
        }

        $data = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'lesson_rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['required', 'string', 'min:10', 'max:2000'],
        ], [
            'rating.required' => __('tutor.student_rating_required'),
            'lesson_rating.required' => __('tutor.lesson_rating_required'),
            'comment.required' => __('tutor.evaluation_comment_required'),
            'comment.min' => __('tutor.evaluation_comment_min'),
        ]);

        $wasNew = ! $booking->hasInstructorEvaluation();

        LessonBookingRating::updateOrCreate(
            [
                'lesson_booking_id' => $booking->id,
                'rater_id' => Auth::id(),
            ],
            [
                'rated_user_id' => $booking->student_id,
                'rating' => $data['rating'],
                'lesson_rating' => $data['lesson_rating'],
                'comment' => $data['comment'],
            ]
        );

        $booking->update(['instructor_rated_at' => now()]);

        TutorNotificationService::instructorEvaluationSubmitted(
            $booking->fresh(['student', 'instructor', 'ratings'])
        );

        return redirect()->route('instructor.tutor-lessons.bookings.show', $booking)
            ->with('success', $wasNew
                ? __('tutor.evaluation_sent_to_parent')
                : __('tutor.evaluation_updated'));
    }

    private function authorizeInstructor(LessonBooking $booking): void
    {
        if ($booking->instructor_id !== Auth::id()) {
            abort(403);
        }
    }
}
