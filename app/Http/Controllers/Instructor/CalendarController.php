<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
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
        $events = collect();
        $stats = [
            'total' => 0,
            'upcoming' => 0,
        ];

        return view('instructor.calendar.index', compact('events', 'stats'));
    }

    public function getEvents(Request $request)
    {
        return response()->json([]);
    }
}
