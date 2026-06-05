<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\LessonBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CalendarController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:instructor|teacher']);
    }

    public function index()
    {
        $instructorId = Auth::id();
        $bookings = LessonBooking::where('instructor_id', $instructorId)
            ->whereIn('status', [
                LessonBooking::STATUS_PENDING,
                LessonBooking::STATUS_CONFIRMED,
                LessonBooking::STATUS_IN_PROGRESS,
            ])
            ->where('scheduled_at', '>=', now()->startOfDay())
            ->orderBy('scheduled_at')
            ->get();

        $stats = [
            'total' => $bookings->count(),
            'upcoming' => $bookings->where('scheduled_at', '>=', now())->count(),
        ];

        $events = $bookings->map(fn (LessonBooking $b) => (object) [
            'title' => __('tutor.lesson_with', ['student' => $b->student?->name ?? 'طالب']),
            'start_date' => $b->scheduled_at,
            'url' => route('instructor.tutor-lessons.bookings.show', $b),
        ]);

        return view('instructor.calendar.index', compact('events', 'stats'));
    }

    public function getEvents(Request $request)
    {
        $start = $request->date('start') ?? now()->startOfMonth();
        $end = $request->date('end') ?? now()->endOfMonth()->addMonth();

        $bookings = LessonBooking::query()
            ->where('instructor_id', Auth::id())
            ->whereBetween('scheduled_at', [$start, $end])
            ->whereNotIn('status', [LessonBooking::STATUS_CANCELLED])
            ->with('student')
            ->get();

        $statusColors = [
            LessonBooking::STATUS_PENDING => '#f59e0b',
            LessonBooking::STATUS_CONFIRMED => '#10b981',
            LessonBooking::STATUS_IN_PROGRESS => '#3b82f6',
            LessonBooking::STATUS_COMPLETED => '#64748b',
        ];

        $events = $bookings->map(function (LessonBooking $b) use ($statusColors) {
            $end = $b->scheduled_at->copy()->addMinutes($b->duration_minutes);

            return [
                'id' => 'lesson-'.$b->id,
                'title' => __('tutor.lesson_with', ['student' => $b->student?->name ?? 'طالب']),
                'start' => $b->scheduled_at->toIso8601String(),
                'end' => $end->toIso8601String(),
                'url' => route('instructor.tutor-lessons.bookings.show', $b),
                'backgroundColor' => $statusColors[$b->status] ?? '#6366f1',
                'borderColor' => $statusColors[$b->status] ?? '#6366f1',
                'extendedProps' => [
                    'status' => $b->status,
                    'code' => $b->code,
                ],
            ];
        });

        return response()->json($events->values());
    }
}
