<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\EmployeeTaskAssignedMail;
use App\Models\EmployeeJob;
use App\Models\EmployeeTask;
use App\Models\User;
use App\Support\EmployeeTaskTypes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class EmployeeTaskController extends Controller
{
    /**
     * عرض قائمة المهام
     */
    public function index(Request $request)
    {
        $query = EmployeeTask::with(['employee.employeeJob', 'assigner']);

        // البحث
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('employee', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // فلترة حسب الموظف
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        // فلترة حسب الحالة
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // فلترة حسب الأولوية
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('task_type')) {
            $query->where('task_type', $request->task_type);
        }

        if ($request->filled('employee_job_id')) {
            $query->whereHas('employee', function ($q) use ($request) {
                $q->where('employee_job_id', $request->employee_job_id);
            });
        }

        $tasks = $query->latest()->paginate(20);

        $employees = User::employees()->where('is_active', true)->with('employeeJob')->orderBy('name')->get();

        $employeeJobs = EmployeeJob::query()->fixedJobs()->active()->orderBy('name')->get(['id', 'name', 'code']);

        $stats = [
            'total' => EmployeeTask::count(),
            'pending' => EmployeeTask::pending()->count(),
            'in_progress' => EmployeeTask::inProgress()->count(),
            'completed' => EmployeeTask::completed()->count(),
            'overdue' => EmployeeTask::overdue()->count(),
        ];

        $taskTypeDefinitions = EmployeeTaskTypes::definitions();

        return view('admin.employee-tasks.index', compact('tasks', 'employees', 'employeeJobs', 'stats', 'taskTypeDefinitions'));
    }

    /**
     * عرض صفحة إضافة مهمة
     */
    public function create()
    {
        $employees = User::employees()->where('is_active', true)->with('employeeJob')->orderBy('name')->get();
        $taskTypeDefinitions = EmployeeTaskTypes::definitions();

        return view('admin.employee-tasks.create', compact('employees', 'taskTypeDefinitions'));
    }

    /**
     * حفظ مهمة جديدة
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:users,id',
            'task_type' => ['required', Rule::in(EmployeeTaskTypes::codes())],
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'deadline' => 'nullable|date|after:today',
            'notes' => 'nullable|string',
        ]);

        $employee = User::query()->with('employeeJob')->findOrFail($validated['employee_id']);
        if (! $employee->is_employee) {
            throw ValidationException::withMessages(['employee_id' => 'المستخدم المختار ليس موظفاً.']);
        }
        $jobCode = $employee->employeeJob?->code;
        if (! EmployeeTaskTypes::taskTypeAllowedForJob($validated['task_type'], $jobCode)) {
            throw ValidationException::withMessages([
                'task_type' => 'نوع المهمة غير متاح لوظيفة هذا الموظف. اختر نوعاً يطابق الوظيفة أو «مهمة عامة».',
            ]);
        }

        $validated['assigned_by'] = auth()->id();
        $validated['status'] = 'pending';

        $task = EmployeeTask::create($validated);

        try {
            $employee = $task->employee;
            if ($employee && $employee->email) {
                Mail::to($employee->email)->send(new EmployeeTaskAssignedMail($task));
            }
        } catch (\Throwable $e) {
            Log::warning('Failed to send employee task assigned email: ' . $e->getMessage(), [
                'task_id' => $task->id,
                'employee_id' => $task->employee_id,
            ]);
        }

        return redirect()->route('admin.employee-tasks.show', $task)
                        ->with('success', __('تم إضافة المهمة بنجاح'));
    }

    /**
     * عرض تفاصيل مهمة
     */
    public function show(Request $request, EmployeeTask $employeeTask)
    {
        $employeeTask->load(['employee.employeeJob', 'assigner']);
        $totalDeliverables = $employeeTask->deliverables()->count();

        $deliverables = $employeeTask->deliverables()
            ->with('reviewer')
            ->when($request->filled('search'), function ($q) use ($request) {
                $s = $request->search;
                $q->where(function ($q) use ($s) {
                    $q->where('title', 'like', "%{$s}%")
                        ->orWhere('description', 'like', "%{$s}%")
                        ->orWhere('received_from', 'like', "%{$s}%")
                        ->orWhere('link_url', 'like', "%{$s}%")
                        ->orWhere('file_name', 'like', "%{$s}%")
                        ->orWhere('duration_before', 'like', "%{$s}%")
                        ->orWhere('duration_after', 'like', "%{$s}%");
                });
            })
            ->orderByDesc('created_at')
            ->paginate(25)
            ->withQueryString();

        return view('admin.employee-tasks.show', compact('employeeTask', 'deliverables', 'totalDeliverables'));
    }

    /**
     * عرض صفحة تعديل مهمة
     */
    public function edit(EmployeeTask $employeeTask)
    {
        $employees = User::employees()->where('is_active', true)->with('employeeJob')->orderBy('name')->get();
        $taskTypeDefinitions = EmployeeTaskTypes::definitions();

        return view('admin.employee-tasks.edit', compact('employeeTask', 'employees', 'taskTypeDefinitions'));
    }

    /**
     * تحديث مهمة
     */
    public function update(Request $request, EmployeeTask $employeeTask)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:users,id',
            'task_type' => ['required', Rule::in(EmployeeTaskTypes::codes())],
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'status' => 'required|in:pending,in_progress,completed,cancelled,on_hold',
            'deadline' => 'nullable|date',
            'progress' => 'nullable|integer|min:0|max:100',
            'notes' => 'nullable|string',
        ]);

        $employee = User::query()->with('employeeJob')->findOrFail($validated['employee_id']);
        if (! $employee->is_employee) {
            throw ValidationException::withMessages(['employee_id' => 'المستخدم المختار ليس موظفاً.']);
        }
        $jobCode = $employee->employeeJob?->code;
        if (! EmployeeTaskTypes::taskTypeAllowedForJob($validated['task_type'], $jobCode)) {
            throw ValidationException::withMessages([
                'task_type' => 'نوع المهمة غير متاح لوظيفة هذا الموظف.',
            ]);
        }

        // تحديث التواريخ بناءً على الحالة
        if ($validated['status'] === 'in_progress' && !$employeeTask->started_at) {
            $validated['started_at'] = now();
        }

        if ($validated['status'] === 'completed') {
            $validated['completed_at'] = now();
            $validated['progress'] = 100;
        }

        $employeeTask->update($validated);

        return redirect()->route('admin.employee-tasks.show', $employeeTask)
                        ->with('success', __('تم تحديث المهمة بنجاح'));
    }

    /**
     * حذف مهمة
     */
    public function destroy(EmployeeTask $employeeTask)
    {
        $employeeTask->delete();
        return redirect()->route('admin.employee-tasks.index')
                        ->with('success', __('تم حذف المهمة بنجاح'));
    }
}
