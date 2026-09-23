<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeHrLeaveController extends Controller
{
    private function gate(): void
    {
        $u = Auth::user();
        abort_unless($u && $u->isEmployee() && $u->employeeCan('hr_desk'), 403);
    }

    public function index(Request $request)
    {
        $this->gate();

        $query = LeaveRequest::with(['employee.employeeJob', 'reviewer'])->latest();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('employee', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('employee_code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->integer('employee_id'));
        }

        $leaveRequests = $query->paginate(20)->withQueryString();

        $employees = User::query()->employees()->where('is_active', true)->orderBy('name')->get(['id', 'name']);

        $stats = [
            'total' => LeaveRequest::count(),
            'pending' => LeaveRequest::where('status', 'pending')->count(),
            'approved' => LeaveRequest::where('status', 'approved')->count(),
            'rejected' => LeaveRequest::where('status', 'rejected')->count(),
        ];

        return view('employee.hr.leaves.index', compact('leaveRequests', 'employees', 'stats'));
    }

    public function show(LeaveRequest $leave)
    {
        $this->gate();

        $leave->load(['employee.employeeJob', 'reviewer']);

        return view('employee.hr.leaves.show', compact('leave'));
    }

    public function approve(Request $request, LeaveRequest $leave)
    {
        $this->gate();

        $validated = $request->validate([
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        if ($leave->status !== 'pending') {
            return redirect()->back()
                ->with('error', __('لا يمكن الموافقة على طلب تمت مراجعته بالفعل.'));
        }

        $leave->update([
            'status' => 'approved',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
            'admin_notes' => $validated['admin_notes'] ?? null,
        ]);

        return redirect()->route('employee.hr.leaves.index')
            ->with('success', __('تم الموافقة على طلب الإجازة.'));
    }

    public function reject(Request $request, LeaveRequest $leave)
    {
        $this->gate();

        $validated = $request->validate([
            'admin_notes' => 'required|string|max:1000',
        ]);

        if ($leave->status !== 'pending') {
            return redirect()->back()
                ->with('error', __('لا يمكن رفض طلب تمت مراجعته بالفعل.'));
        }

        $leave->update([
            'status' => 'rejected',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
            'admin_notes' => $validated['admin_notes'],
        ]);

        return redirect()->route('employee.hr.leaves.index')
            ->with('success', __('تم رفض طلب الإجازة.'));
    }
}
