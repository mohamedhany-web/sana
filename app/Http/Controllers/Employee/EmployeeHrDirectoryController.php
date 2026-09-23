<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\EmployeeTask;
use App\Models\HrEmployeeEvent;
use App\Models\LeaveRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class EmployeeHrDirectoryController extends Controller
{
    private function gate(): void
    {
        $u = Auth::user();
        abort_unless($u && $u->isEmployee() && $u->employeeCan('hr_desk'), 403);
    }

    public function index(Request $request)
    {
        $this->gate();

        $query = User::query()
            ->employees()
            ->with('employeeJob:id,name,code')
            ->orderBy('name');

        if (! $request->boolean('all')) {
            $query->where('is_active', true);
        }

        if ($request->filled('job_id')) {
            $query->where('employee_job_id', $request->integer('job_id'));
        }

        if ($request->filled('search')) {
            $s = trim((string) $request->input('search'));
            if ($s !== '') {
                $like = '%'.$s.'%';
                $query->where(function ($q) use ($like) {
                    $q->where('name', 'like', $like)
                        ->orWhere('email', 'like', $like)
                        ->orWhere('phone', 'like', $like)
                        ->orWhere('employee_code', 'like', $like);
                });
            }
        }

        $employees = $query->paginate(24)->withQueryString();

        $jobs = \App\Models\EmployeeJob::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return view('employee.hr.employees.index', compact('employees', 'jobs'));
    }

    public function show(User $employee)
    {
        $this->gate();
        abort_unless($employee->is_employee, 404);

        $employee->load(['employeeJob']);

        $leaveStats = [
            'pending' => LeaveRequest::where('employee_id', $employee->id)->where('status', 'pending')->count(),
            'approved_year' => LeaveRequest::where('employee_id', $employee->id)
                ->where('status', 'approved')
                ->whereYear('start_date', now()->year)
                ->sum('days'),
        ];

        $recentLeaves = LeaveRequest::query()
            ->where('employee_id', $employee->id)
            ->latest()
            ->take(8)
            ->get();

        $pendingTasks = EmployeeTask::query()
            ->where('employee_id', $employee->id)
            ->whereIn('status', ['pending', 'in_progress'])
            ->count();

        $agreementsCount = $employee->employeeAgreements()->count();

        $hrEvents = HrEmployeeEvent::query()
            ->where('employee_id', $employee->id)
            ->with('author:id,name')
            ->latest('event_date')
            ->latest('id')
            ->take(50)
            ->get();

        return view('employee.hr.employees.show', compact(
            'employee',
            'leaveStats',
            'recentLeaves',
            'pendingTasks',
            'agreementsCount',
            'hrEvents'
        ));
    }

    public function storeEvent(Request $request, User $employee)
    {
        $this->gate();
        abort_unless($employee->is_employee, 404);

        $validated = $request->validate([
            'event_type' => ['required', Rule::in(array_keys(HrEmployeeEvent::typeLabels()))],
            'title' => 'nullable|string|max:255',
            'body' => 'required|string|max:10000',
            'event_date' => 'required|date',
        ]);

        HrEmployeeEvent::create([
            'employee_id' => $employee->id,
            'created_by' => Auth::id(),
            'event_type' => $validated['event_type'],
            'title' => $validated['title'] ?? null,
            'body' => $validated['body'],
            'event_date' => $validated['event_date'],
        ]);

        return redirect()->route('employee.hr.employees.show', $employee)
            ->with('success', __('تم تسجيل الحدث في سجل الموارد البشرية.'));
    }
}
