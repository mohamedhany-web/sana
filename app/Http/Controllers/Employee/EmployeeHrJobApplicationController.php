<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\HrJobApplication;
use App\Models\HrJobOpening;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EmployeeHrJobApplicationController extends Controller
{
    private function gate(): void
    {
        $u = auth()->user();
        abort_unless($u && $u->isEmployee() && $u->employeeCan('hr_desk'), 403);
    }

    public function show(HrJobApplication $hr_job_application)
    {
        $this->gate();

        $hr_job_application->load([
            'opening',
            'candidate',
            'interviews.interviewer:id,name',
            'interviews.creator:id,name',
        ]);

        $interviewers = \App\Models\User::query()
            ->where('is_employee', true)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('employee.hr.recruitment.applications.show', [
            'application' => $hr_job_application,
            'interviewers' => $interviewers,
        ]);
    }

    public function store(Request $request)
    {
        $this->gate();

        $validated = $request->validate([
            'hr_job_opening_id' => 'required|exists:hr_job_openings,id',
            'hr_candidate_id' => 'required|exists:hr_candidates,id',
            'cover_letter' => 'nullable|string|max:10000',
        ]);

        $opening = HrJobOpening::findOrFail($validated['hr_job_opening_id']);
        abort_unless($opening->isAcceptingApplications(), 422, 'الوظيفة غير مفتوحة للتقديم.');

        $exists = HrJobApplication::where('hr_job_opening_id', $validated['hr_job_opening_id'])
            ->where('hr_candidate_id', $validated['hr_candidate_id'])
            ->exists();

        abort_if($exists, 422, 'هذا المرشح مقدّم بالفعل على هذه الوظيفة.');

        $application = HrJobApplication::create([
            'hr_job_opening_id' => $validated['hr_job_opening_id'],
            'hr_candidate_id' => $validated['hr_candidate_id'],
            'status' => HrJobApplication::STATUS_APPLIED,
            'cover_letter' => $validated['cover_letter'] ?? null,
            'applied_at' => now(),
        ]);

        return redirect()->route('employee.hr.recruitment.applications.show', $application)
            ->with('success', __('تم تسجيل طلب التوظيف.'));
    }

    public function update(Request $request, HrJobApplication $hr_job_application)
    {
        $this->gate();

        $validated = $request->validate([
            'status' => ['required', Rule::in(array_keys(HrJobApplication::statusLabels()))],
            'internal_notes' => 'nullable|string|max:10000',
        ]);

        $hr_job_application->update($validated);

        return redirect()->route('employee.hr.recruitment.applications.show', $hr_job_application)
            ->with('success', __('تم تحديث حالة الطلب.'));
    }
}
