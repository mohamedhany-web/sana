<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\TutorWorkLog;
use App\Services\TutorWorkLogService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TutorWorkLogController extends Controller
{
    public function index()
    {
        $logs = TutorWorkLog::where('instructor_id', Auth::id())
            ->orderByDesc('work_date')
            ->limit(60)
            ->get();

        $todayMinutes = TutorWorkLogService::minutesToday(Auth::id());

        return view('instructor.tutor-lessons.work-log', compact('logs', 'todayMinutes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'work_date' => ['required', 'date'],
            'minutes' => ['required', 'integer', 'min:1', 'max:720'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        TutorWorkLogService::recordManual(
            Auth::id(),
            Carbon::parse($data['work_date']),
            (int) $data['minutes'],
            $data['notes'] ?? null
        );

        TutorWorkLogService::minutesToday(Auth::id());

        return back()->with('success', 'تم تسجيل ساعات العمل.');
    }
}
