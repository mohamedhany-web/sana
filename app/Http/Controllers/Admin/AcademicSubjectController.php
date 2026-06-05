<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicSubject;
use App\Models\AcademicYear;
use App\Models\AdvancedCourse;
use App\Models\InstructorProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AcademicSubjectController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = $request->user();
            if ($user && ($user->isSuperAdmin()
                || $user->hasPermission('manage.courses')
                || $user->hasPermission('manage.academic-subjects')
                || $user->hasPermission('manage.academic-years'))) {
                return $next($request);
            }

            abort(403);
        });
    }

    public function index(Request $request): View
    {
        $trackId = $request->query('track');

        $subjects = AcademicSubject::query()
            ->with(['academicYear:id,name'])
            ->when($trackId, fn ($query) => $query->where('academic_year_id', $trackId))
            ->withCount(['advancedCourses as courses_count' => fn ($q) => $q->where('is_active', true)])
            ->orderBy('order')
            ->orderBy('name')
            ->get()
            ->map(function (AcademicSubject $subject) {
                $subject->setAttribute('instructors_count', $this->countInstructorsForSubject($subject->id));

                return $subject;
            });

        $summary = [
            'total' => $subjects->count(),
            'active' => $subjects->where('is_active', true)->count(),
            'courses' => $subjects->sum('courses_count'),
            'instructors' => $subjects->sum('instructors_count'),
        ];

        $currentTrack = $trackId ? AcademicYear::find($trackId) : null;
        $tracks = AcademicYear::orderBy('order')->orderBy('name')->pluck('name', 'id');

        return view('admin.academic-subjects.index', compact('subjects', 'summary', 'currentTrack', 'tracks'));
    }

    public function create(Request $request)
    {
        $academicYears = AcademicYear::orderBy('order')->orderBy('name')->get();
        $selectedTrack = $request->query('track');

        $skills = AdvancedCourse::where('is_active', true)
            ->pluck('skills')
            ->filter()
            ->flatMap(function ($skills) {
                return collect(is_array($skills) ? $skills : json_decode($skills, true) ?? []);
            })
            ->filter()
            ->unique()
            ->values();

        $languages = AdvancedCourse::whereNotNull('programming_language')
            ->distinct()
            ->orderBy('programming_language')
            ->pluck('programming_language');

        $frameworks = AdvancedCourse::whereNotNull('framework')
            ->distinct()
            ->orderBy('framework')
            ->pluck('framework');

        return view('admin.academic-subjects.create', compact('academicYears', 'skills', 'languages', 'frameworks', 'selectedTrack'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'name' => 'required|string|max:255',
            'code' => ['nullable', 'string', 'max:100', Rule::unique('academic_subjects', 'code')],
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:100',
            'color' => 'nullable|string|max:7',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ], [
            'academic_year_id.required' => 'المرحلة الدراسية مطلوبة',
            'academic_year_id.exists' => 'المرحلة المحددة غير موجودة',
            'name.required' => 'اسم المادة مطلوب',
            'name.max' => 'اسم المادة لا يجب أن يتجاوز 255 حرف',
            'code.unique' => 'رمز المادة مستخدم مسبقاً',
            'code.max' => 'رمز المادة لا يجب أن يتجاوز 100 حرف',
        ]);

        $code = trim((string) ($validated['code'] ?? ''));
        if ($code === '') {
            $code = $this->generateUniqueCode($validated['name']);
        }

        AcademicSubject::create([
            'academic_year_id' => $validated['academic_year_id'],
            'name' => trim($validated['name']),
            'code' => $code,
            'description' => $validated['description'] ?? null,
            'icon' => $validated['icon'] ?? 'fas fa-book',
            'color' => $validated['color'] ?? '#1D4EDB',
            'order' => $validated['order'] ?? ((int) (AcademicSubject::max('order') ?? 0) + 1),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.academic-subjects.index', ['track' => $validated['academic_year_id']])
            ->with('success', 'تم إضافة المادة بنجاح');
    }

    public function show(AcademicSubject $academicSubject)
    {
        $academicSubject->load(['academicYear']);
        
        // إضافة advancedCourses كـ collection فارغة لأن العلاقة لم تعد موجودة
        $academicSubject->setRelation('advancedCourses', collect());

        return view('admin.academic-subjects.show', compact('academicSubject'));
    }

    public function edit(AcademicSubject $academicSubject)
    {
        $academicYears = AcademicYear::where('is_active', true)->orderBy('order')->get();
        return view('admin.academic-subjects.edit', compact('academicSubject', 'academicYears'));
    }

    public function update(Request $request, AcademicSubject $academicSubject)
    {
        $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'name' => 'required|string|max:255',
            'code' => [
                'required',
                'string',
                'max:100',
                Rule::unique('academic_subjects', 'code')->ignore($academicSubject->id),
            ],
            'description' => 'nullable|string',
            'icon' => 'required|string|max:100',
            'color' => 'required|string|max:7',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ], [
            'academic_year_id.required' => 'المرحلة الدراسية مطلوبة',
            'academic_year_id.exists' => 'المرحلة المحددة غير موجودة',
            'name.required' => 'اسم المادة مطلوب',
            'name.max' => 'اسم المادة لا يجب أن يتجاوز 255 حرف',
            'code.required' => 'رمز المادة مطلوب',
            'code.unique' => 'رمز المادة مستخدم مسبقاً',
            'code.max' => 'رمز المادة لا يجب أن يتجاوز 100 حرف',
            'icon.required' => 'أيقونة المادة مطلوبة',
            'color.required' => 'لون المادة مطلوب',
        ]);

        $academicSubject->update([
            'academic_year_id' => $request->integer('academic_year_id'),
            'name' => trim($request->string('name')->toString()),
            'code' => trim($request->string('code')->toString()),
            'description' => $request->input('description'),
            'icon' => $request->input('icon'),
            'color' => $request->input('color'),
            'order' => (int) ($request->input('order') ?? 0),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.academic-subjects.index')
            ->with('success', 'تم تحديث المادة بنجاح');
    }

    public function destroy(AcademicSubject $academicSubject): RedirectResponse
    {
        if ($academicSubject->advancedCourses()->exists()) {
            return redirect()->route('admin.academic-subjects.index')
                ->with('error', 'لا يمكن حذف المادة لأنها مرتبطة بكورسات.');
        }

        if ($this->countInstructorsForSubject($academicSubject->id) > 0) {
            return redirect()->route('admin.academic-subjects.index')
                ->with('error', 'لا يمكن حذف المادة لأنها مرتبطة بمعلّمين.');
        }

        $academicSubject->delete();

        return redirect()->route('admin.academic-subjects.index')
            ->with('success', 'تم حذف المادة بنجاح');
    }

    public function toggleStatus(AcademicSubject $academicSubject): RedirectResponse
    {
        $academicSubject->update([
            'is_active' => ! $academicSubject->is_active,
        ]);

        $status = $academicSubject->is_active ? 'تم تفعيل' : 'تم إيقاف';

        return redirect()->route('admin.academic-subjects.index')
            ->with('success', $status.' المادة بنجاح');
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:academic_subjects,id',
            'items.*.order' => 'required|integer|min:0',
        ]);

        foreach ($request->items as $item) {
            AcademicSubject::where('id', $item['id'])->update(['order' => $item['order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم إعادة ترتيب المجموعات المهارية بنجاح'
        ]);
    }

    private function hydrateCluster(AcademicSubject $subject, Collection $courses): AcademicSubject
    {
        $track = $subject->academicYear;

        $matchedCourses = $this->filterCourses($courses, [
            optional($track)->code,
            optional($track)->name,
            $subject->code,
            $subject->name,
            $subject->description,
        ]);

        if ($matchedCourses->isEmpty()) {
            $matchedCourses = $courses;
        }

        $languages = $matchedCourses->pluck('programming_language')->filter()->unique()->values();
        $frameworks = $matchedCourses->pluck('framework')->filter()->unique()->values();
        $levels = $matchedCourses->pluck('level')->filter()->unique()->values();
        $minutes = $matchedCourses->sum(function ($course) {
            return ((int) ($course->duration_hours ?? 0) * 60) + (int) ($course->duration_minutes ?? 0);
        });
        $avgMinutes = $matchedCourses->count() > 0 ? (int) round($minutes / $matchedCourses->count()) : 0;

        $subject->setAttribute('cluster_metrics', [
            'courses_count' => $matchedCourses->count(),
            'languages' => $languages->take(8),
            'frameworks' => $frameworks->take(8),
            'levels' => $levels,
            'avg_duration' => $this->formatDurationMinutes($avgMinutes),
            'avg_rating' => $matchedCourses->count() > 0 ? round((float) ($matchedCourses->avg('rating') ?? 0), 1) : null,
        ]);

        $subject->setRelation('preview_courses', $matchedCourses->sortByDesc('created_at')->take(3));

        return $subject;
    }

    private function filterCourses(Collection $courses, array $identifiers): Collection
    {
        $needles = collect($identifiers)
            ->filter()
            ->map(fn ($value) => Str::of($value)->lower()->replace(['-', '_'], ' ')->squish())
            ->filter(fn ($value) => $value->isNotEmpty());

        if ($needles->isEmpty()) {
            return collect();
        }

        return $courses->filter(function (AdvancedCourse $course) use ($needles) {
            $fields = collect([
                $course->category,
                $course->programming_language,
                $course->framework,
                $course->level,
                $course->title,
                $course->description,
            ])->merge((array) ($course->skills ?? []));

            return $fields->contains(function ($field) use ($needles) {
                if (empty($field)) {
                    return false;
                }

                $value = Str::of($field)->lower()->replace(['-', '_'], ' ')->squish();

                foreach ($needles as $needle) {
                    if ($needle->isNotEmpty() && Str::contains($value, $needle)) {
                        return true;
                    }
                }

                return false;
            });
        })->values();
    }

    private function formatDurationMinutes(int $minutes): ?string
    {
        if ($minutes <= 0) {
            return null;
        }

        $hours = intdiv($minutes, 60);
        $remaining = $minutes % 60;

        if ($hours === 0) {
            return $remaining . ' د';
        }

        if ($remaining === 0) {
            return $hours . ' س';
        }

        return $hours . ' س ' . $remaining . ' د';
    }

    private function countInstructorsForSubject(int $subjectId): int
    {
        return InstructorProfile::query()
            ->where(function ($query) use ($subjectId) {
                $query->whereJsonContains('tutor_subject_ids', $subjectId)
                    ->orWhereJsonContains('tutor_subject_ids', (string) $subjectId);
            })
            ->count();
    }

    private function generateUniqueCode(string $name): string
    {
        $base = Str::upper(Str::slug($name, '_'));
        if ($base === '') {
            $base = 'SUBJECT';
        }
        $base = Str::limit($base, 80, '');
        $code = $base;
        $i = 1;
        while (AcademicSubject::where('code', $code)->exists()) {
            $code = $base.'_'.$i;
            $i++;
        }

        return $code;
    }
}