<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Parent\ParentDashboardService;
use Illuminate\Http\Request;

class ChildController extends Controller
{
    public function __construct(private ParentDashboardService $dashboard) {}

    public function index()
    {
        $children = $this->dashboard->linkedStudents(auth()->user());

        return view('parent.children.index', compact('children'));
    }

    public function show(User $student)
    {
        $this->authorizeStudent($student);
        $snapshot = $this->dashboard->studentSnapshot($student);

        return view('parent.children.show', array_merge(['student' => $student], $snapshot));
    }

    private function authorizeStudent(User $student): void
    {
        $linked = auth()->user()->linkedStudents()->where('users.id', $student->id)->exists();
        abort_unless($linked && $student->role === 'student', 403);
    }
}
