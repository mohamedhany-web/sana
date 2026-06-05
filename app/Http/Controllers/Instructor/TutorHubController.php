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
            ->limit(8)
            ->with(['student', 'subject'])
            ->get();

        $pendingCount = LessonBooking::where('instructor_id', $user->id)
            ->where('status', LessonBooking::STATUS_PENDING)
            ->count();

        $todayMinutes = TutorWorkLogService::minutesToday($user->id);
        $weekMinutes = (int) TutorWorkLog::where('instructor_id', $user->id)
            ->where('work_date', '>=', now()->subDays(7))
            ->sum('minutes');

        $availabilities = $user->tutorAvailabilities()->where('is_active', true)->orderBy('day_of_week')->get();

        return view('instructor.tutor-lessons.hub', compact(
            'profile',
            'upcoming',
            'pendingCount',
            'todayMinutes',
            'weekMinutes',
            'availabilities'
        ));
    }
}
