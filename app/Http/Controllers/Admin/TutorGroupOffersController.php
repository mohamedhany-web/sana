<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicSubject;
use App\Models\InstructorProfile;
use App\Models\Package;
use App\Models\TutorGroupOffer;
use App\Services\StudentSubscriptionPlansService;
use Illuminate\Http\Request;

class TutorGroupOffersController extends Controller
{
    public function index()
    {
        $offers = TutorGroupOffer::query()
            ->with(['instructor', 'package', 'subject'])
            ->orderBy('sort_order')
            ->orderByDesc('updated_at')
            ->paginate(20);

        return view('admin.tutor-lessons.group-offers.index', compact('offers'));
    }

    public function create()
    {
        return view('admin.tutor-lessons.group-offers.form', $this->formData());
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        TutorGroupOffer::create($data);

        return redirect()->route('admin.tutor-lessons.group-offers.index')
            ->with('success', __('tutor.group_offer_saved'));
    }

    public function edit(TutorGroupOffer $groupOffer)
    {
        return view('admin.tutor-lessons.group-offers.form', array_merge(
            $this->formData(),
            ['offer' => $groupOffer]
        ));
    }

    public function update(Request $request, TutorGroupOffer $groupOffer)
    {
        $groupOffer->update($this->validated($request));

        return redirect()->route('admin.tutor-lessons.group-offers.index')
            ->with('success', __('tutor.group_offer_saved'));
    }

    public function destroy(TutorGroupOffer $groupOffer)
    {
        $groupOffer->delete();

        return back()->with('success', __('tutor.group_offer_deleted'));
    }

    protected function formData(): array
    {
        $instructors = InstructorProfile::offersTutorBooking()
            ->with('user')
            ->get()
            ->sortBy(fn ($p) => $p->user?->name ?? '');

        return [
            'instructors' => $instructors,
            'packages' => Package::where('is_active', true)->orderBy('name')->get(),
            'subjects' => AcademicSubject::orderBy('name')->get(),
            'planKeys' => StudentSubscriptionPlansService::planKeys(),
            'studentPlans' => StudentSubscriptionPlansService::getPlans(),
        ];
    }

    protected function validated(Request $request): array
    {
        $data = $request->validate([
            'instructor_id' => ['required', 'exists:users,id'],
            'package_id' => ['nullable', 'exists:packages,id'],
            'academic_subject_id' => ['nullable', 'exists:academic_subjects,id'],
            'title' => ['required', 'string', 'max:160'],
            'description' => ['nullable', 'string', 'max:5000'],
            'max_group_size' => ['required', 'integer', 'min:2', 'max:30'],
            'min_group_size' => ['required', 'integer', 'min:2', 'max:30'],
            'duration_minutes' => ['required', 'integer', 'min:30', 'max:180'],
            'display_price' => ['nullable', 'numeric', 'min:0'],
            'subscription_plan_keys' => ['nullable', 'array'],
            'subscription_plan_keys.*' => ['string', 'in:'.implode(',', StudentSubscriptionPlansService::planKeys())],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ]);

        if ((int) $data['min_group_size'] > (int) $data['max_group_size']) {
            $data['min_group_size'] = $data['max_group_size'];
        }

        $planKeys = array_values(array_filter($data['subscription_plan_keys'] ?? []));

        return [
            'instructor_id' => (int) $data['instructor_id'],
            'package_id' => $data['package_id'] ?: null,
            'academic_subject_id' => $data['academic_subject_id'] ?: null,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'max_group_size' => (int) $data['max_group_size'],
            'min_group_size' => (int) $data['min_group_size'],
            'duration_minutes' => (int) $data['duration_minutes'],
            'display_price' => isset($data['display_price']) && $data['display_price'] !== '' ? $data['display_price'] : null,
            'subscription_plan_keys' => $planKeys,
            'is_active' => $request->boolean('is_active'),
            'sort_order' => (int) ($data['sort_order'] ?? 0),
        ];
    }
}
