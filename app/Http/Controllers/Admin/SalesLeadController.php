<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdvancedCourse;
use App\Models\EmployeeJob;
use App\Models\SalesLead;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SalesLeadController extends Controller
{
    /** @return array<int, string> */
    private static function sourceValues(): array
    {
        return [
            SalesLead::SOURCE_WEBSITE,
            SalesLead::SOURCE_PHONE,
            SalesLead::SOURCE_SOCIAL,
            SalesLead::SOURCE_REFERRAL,
            SalesLead::SOURCE_EVENT,
            SalesLead::SOURCE_WALK_IN,
            SalesLead::SOURCE_OTHER,
        ];
    }

    /** @return \Illuminate\Database\Eloquent\Collection<int, User> */
    private function salesReps()
    {
        $salesJobIds = EmployeeJob::query()->where('code', 'sales')->pluck('id');

        $query = User::query()
            ->where('is_employee', true)
            ->orderBy('name');

        if ($salesJobIds->isNotEmpty()) {
            $query->whereIn('employee_job_id', $salesJobIds);
        }

        return $query->get(['id', 'name']);
    }

    public function index(Request $request)
    {
        $query = SalesLead::query()
            ->with(['assignedTo:id,name', 'creator:id,name', 'interestedCourse:id,title']);

        if ($request->filled('status')) {
            $s = (string) $request->input('status');
            $allowed = [
                SalesLead::STATUS_NEW,
                SalesLead::STATUS_CONTACTED,
                SalesLead::STATUS_QUALIFIED,
                SalesLead::STATUS_CONVERTED,
                SalesLead::STATUS_LOST,
            ];
            if (in_array($s, $allowed, true)) {
                $query->where('status', $s);
            }
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            if ($search !== '') {
                $like = '%'.$search.'%';
                $query->where(function ($q) use ($like, $search) {
                    $q->where('name', 'like', $like)
                        ->orWhere('email', 'like', $like)
                        ->orWhere('phone', 'like', $like);
                    if (ctype_digit($search)) {
                        $q->orWhere('id', (int) $search);
                    }
                });
            }
        }

        $leads = $query->latest()->paginate(25)->withQueryString();

        return view('admin.sales.leads.index', compact('leads'));
    }

    public function create()
    {
        $courses = AdvancedCourse::query()->orderBy('title')->get(['id', 'title']);
        $salesReps = $this->salesReps();

        return view('admin.sales.leads.create', compact('courses', 'salesReps'));
    }

    public function store(Request $request)
    {
        $salesRepIds = $this->salesReps()->pluck('id')->all();

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:64',
            'company' => 'nullable|string|max:255',
            'source' => ['required', Rule::in(self::sourceValues())],
            'status' => ['nullable', Rule::in([
                SalesLead::STATUS_NEW,
                SalesLead::STATUS_CONTACTED,
                SalesLead::STATUS_QUALIFIED,
            ])],
            'notes' => 'nullable|string|max:10000',
            'interested_advanced_course_id' => 'nullable|exists:advanced_courses,id',
            'assigned_to' => $salesRepIds !== []
                ? ['nullable', Rule::in($salesRepIds)]
                : 'nullable',
        ];

        $validated = $request->validate($rules);

        $validated['created_by'] = auth()->id();
        $validated['status'] = $validated['status'] ?? SalesLead::STATUS_NEW;

        if (empty($validated['assigned_to'])) {
            unset($validated['assigned_to']);
        }

        $lead = SalesLead::create($validated);

        return redirect()
            ->route('admin.sales.leads.show', $lead)
            ->with('success', 'تم إضافة العميل المحتمل بنجاح.');
    }

    public function show(SalesLead $salesLead)
    {
        $salesLead->load([
            'assignedTo:id,name,email',
            'creator:id,name',
            'linkedUser:id,name,email',
            'convertedOrder:id,user_id,amount,status,advanced_course_id',
            'convertedOrder.course:id,title',
            'convertedOrder.user:id,name,email',
            'interestedCourse:id,title',
        ]);

        return view('admin.sales.leads.show', compact('salesLead'));
    }
}
