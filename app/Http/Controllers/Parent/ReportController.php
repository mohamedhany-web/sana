<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\StudentReport;

class ReportController extends Controller
{
    public function index()
    {
        $studentIds = auth()->user()->linkedStudents()->pluck('users.id');

        $reports = StudentReport::query()
            ->whereIn('student_id', $studentIds)
            ->with('student')
            ->latest()
            ->paginate(15);

        return view('parent.reports.index', compact('reports'));
    }
}
