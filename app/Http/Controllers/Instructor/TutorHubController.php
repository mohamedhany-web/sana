<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\LessonBooking;
use App\Models\TutorWorkLog;
use App\Services\TutorWorkLogService;
use Illuminate\Support\Facades\Auth;

class TutorHubController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $profile = $user->instructorProfile;

        $upcoming = LessonBooking::query()
            ->where('instructor_id', $user->id)
            ->whereIn('status', [LessonBooking::STATUS_PENDING, LessonBooking::STATUS_CONFIRMED])
            ->where('scheduled_at', '>=', now())
            ->orderBy('scheduled_at')
            ->limit(6)
            ->with(['student', 'subject'])
            ->get();

        $pendingCount = LessonBooking::where('instructor_id', $user->id)
            ->where('status', LessonBooking::STATUS_PENDING)
            ->count();

        $confirmedUpcoming = LessonBooking::where('instructor_id', $user->id)
            ->where('status', LessonBooking::STATUS_CONFIRMED)
            ->where('scheduled_at', '>=', now())
            ->count();

        $todayMinutes = TutorWorkLogService::minutesToday($user->id);
        $weekMinutes = (int) TutorWorkLog::where('instructor_id', $user->id)
            ->where('work_date', '>=', now()->subDays(7))
            ->sum('minutes');

        $availabilityDays = $user->tutorAvailabilities()
            ->where('is_active', true)
            ->distinct('day_of_week')
            ->count('day_of_week');

        $stats = [
            'pending_bookings' => $pendingCount,
            'confirmed_upcoming' => $confirmedUpcoming,
            'today_minutes' => $todayMinutes,
            'week_minutes' => $weekMinutes,
            'availability_days' => $availabilityDays,
            'is_activated' => (bool) $profile?->isTutorActivated(),
        ];

        return view('dashboard.instructor-tutor', compact('profile', 'upcoming', 'stats'));
    }
}
