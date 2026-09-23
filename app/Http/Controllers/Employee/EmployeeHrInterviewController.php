<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\HrInterview;
use App\Models\HrJobApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class EmployeeHrInterviewController extends Controller
{
    private function gate(): void
    {
        $u = Auth::user();
        abort_unless($u && $u->isEmployee() && $u->employeeCan('hr_desk'), 403);
    }

    private function assertInterviewBelongs(HrJobApplication $application, HrInterview $interview): void
    {
        abort_unless((int) $interview->hr_job_application_id === (int) $application->id, 404);
    }

    public function store(Request $request, HrJobApplication $hr_job_application)
    {
        $this->gate();

        $validated = $request->validate([
            'round_key' => ['required', Rule::in(array_keys(HrInterview::roundKeyLabels()))],
            'round_label' => [
                'nullable',
                'string',
                'max:255',
                Rule::requiredIf($request->input('round_key') === HrInterview::ROUND_OTHER),
            ],
            'scheduled_at' => 'required|date',
            'duration_minutes' => 'nullable|integer|min:15|max:480',
            'meeting_details' => 'nullable|string|max:2000',
            'interviewer_id' => 'nullable|exists:users,id',
        ]);

        if ($validated['round_key'] !== HrInterview::ROUND_OTHER) {
            $validated['round_label'] = null;
        }

        HrInterview::create([
            'hr_job_application_id' => $hr_job_application->id,
            'round_key' => $validated['round_key'],
            'round_label' => $validated['round_label'] ?? null,
            'scheduled_at' => $validated['scheduled_at'],
            'duration_minutes' => $validated['duration_minutes'] ?? null,
            'meeting_details' => $validated['meeting_details'] ?? null,
            'interviewer_id' => $validated['interviewer_id'] ?? null,
            'status' => HrInterview::STATUS_SCHEDULED,
            'result' => HrInterview::RESULT_PENDING,
            'created_by' => Auth::id(),
        ]);

        if ($hr_job_application->status === HrJobApplication::STATUS_APPLIED
            || $hr_job_application->status === HrJobApplication::STATUS_SCREENING) {
            $hr_job_application->update(['status' => HrJobApplication::STATUS_INTERVIEW]);
        }

        return redirect()->route('employee.hr.recruitment.applications.show', $hr_job_application)
            ->with('success', __('تم جدولة المقابلة.'));
    }

    public function update(Request $request, HrJobApplication $hr_job_application, HrInterview $hr_interview)
    {
        $this->gate();
        $this->assertInterviewBelongs($hr_job_application, $hr_interview);

        $validated = $request->validate([
            'round_key' => ['required', Rule::in(array_keys(HrInterview::roundKeyLabels()))],
            'round_label' => [
                'nullable',
                'string',
                'max:255',
                Rule::requiredIf($request->input('round_key') === HrInterview::ROUND_OTHER),
            ],
            'scheduled_at' => 'required|date',
            'duration_minutes' => 'nullable|integer|min:15|max:480',
            'meeting_details' => 'nullable|string|max:2000',
            'interviewer_id' => 'nullable|exists:users,id',
            'status' => ['required', Rule::in(array_keys(HrInterview::statusLabels()))],
            'result' => ['required', Rule::in(array_keys(HrInterview::resultLabels()))],
            'notes' => 'nullable|string|max:10000',
        ]);

        if (($validated['round_key'] ?? '') !== HrInterview::ROUND_OTHER) {
            $validated['round_label'] = null;
        }

        $hr_interview->update($validated);

        return redirect()->route('employee.hr.recruitment.applications.show', $hr_job_application)
            ->with('success', __('تم تحديث المقابلة.'));
    }

    public function destroy(HrJobApplication $hr_job_application, HrInterview $hr_interview)
    {
        $this->gate();
        $this->assertInterviewBelongs($hr_job_application, $hr_interview);

        $hr_interview->delete();

        return redirect()->route('employee.hr.recruitment.applications.show', $hr_job_application)
            ->with('success', __('تم حذف سجل المقابلة.'));
    }
}
