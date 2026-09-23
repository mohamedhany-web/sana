<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\HrJobOpening;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class EmployeeHrJobOpeningController extends Controller
{
    private function gate(): void
    {
        $u = Auth::user();
        abort_unless($u && $u->isEmployee() && $u->employeeCan('hr_desk'), 403);
    }

    public function index(Request $request)
    {
        $this->gate();

        $query = HrJobOpening::query()->with('creator:id,name')->withCount('applications')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $s = trim((string) $request->input('search'));
            if ($s !== '') {
                $like = '%'.$s.'%';
                $query->where(function ($q) use ($like) {
                    $q->where('title', 'like', $like)->orWhere('department', 'like', $like);
                });
            }
        }

        $openings = $query->paginate(15)->withQueryString();

        return view('employee.hr.recruitment.openings.index', compact('openings'));
    }

    public function create()
    {
        $this->gate();

        return view('employee.hr.recruitment.openings.create');
    }

    public function store(Request $request)
    {
        $this->gate();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            'description' => 'required|string|max:20000',
            'requirements' => 'nullable|string|max:20000',
            'employment_type' => ['required', Rule::in(array_keys(HrJobOpening::employmentTypeLabels()))],
            'status' => ['required', Rule::in(array_keys(HrJobOpening::statusLabels()))],
            'closes_at' => 'nullable|date',
        ]);

        $validated['created_by'] = Auth::id();

        $opening = HrJobOpening::create($validated);

        return redirect()->route('employee.hr.recruitment.openings.show', $opening)
            ->with('success', __('تم إنشاء الوظيفة.'));
    }

    public function show(HrJobOpening $opening)
    {
        $this->gate();

        $opening->load([
            'creator:id,name',
            'applications.candidate:id,full_name,email,phone',
            'applications' => fn ($q) => $q->latest('applied_at'),
        ]);

        $candidateIds = $opening->applications->pluck('hr_candidate_id');

        $candidatesForSelect = \App\Models\HrCandidate::query()
            ->when($candidateIds->isNotEmpty(), fn ($q) => $q->whereNotIn('id', $candidateIds))
            ->orderBy('full_name')
            ->get(['id', 'full_name', 'email']);

        return view('employee.hr.recruitment.openings.show', compact('opening', 'candidatesForSelect'));
    }

    public function edit(HrJobOpening $opening)
    {
        $this->gate();

        return view('employee.hr.recruitment.openings.edit', compact('opening'));
    }

    public function update(Request $request, HrJobOpening $opening)
    {
        $this->gate();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            'description' => 'required|string|max:20000',
            'requirements' => 'nullable|string|max:20000',
            'employment_type' => ['required', Rule::in(array_keys(HrJobOpening::employmentTypeLabels()))],
            'status' => ['required', Rule::in(array_keys(HrJobOpening::statusLabels()))],
            'closes_at' => 'nullable|date',
        ]);

        $opening->update($validated);

        return redirect()->route('employee.hr.recruitment.openings.show', $opening)
            ->with('success', __('تم حفظ التعديلات.'));
    }

    public function destroy(HrJobOpening $opening)
    {
        $this->gate();

        $opening->delete();

        return redirect()->route('employee.hr.recruitment.openings.index')
            ->with('success', __('تم حذف الوظيفة والطلبات المرتبطة.'));
    }
}
