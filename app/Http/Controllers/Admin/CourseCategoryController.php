<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourseCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CourseCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:manage.courses');
    }

    public function index(): View
    {
        $categories = CourseCategory::query()->orderBy('sort_order')->orderBy('name')->get();

        return view('admin.course-categories.index', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:course_categories,name',
            'sort_order' => 'nullable|integer|min:0|max:99999',
            'is_active' => 'boolean',
        ], [
            'name.required' => 'اسم المسار مطلوب',
            'name.unique' => 'هذا الاسم مستخدم بالفعل',
        ]);

        CourseCategory::create([
            'name' => trim($validated['name']),
            'sort_order' => $validated['sort_order'] ?? (int) ((CourseCategory::max('sort_order') ?? 0) + 1),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.course-categories.index')
            ->with('success', __('تم إضافة المسار بنجاح.'));
    }

    public function edit(CourseCategory $course_category): View
    {
        return view('admin.course-categories.edit', ['category' => $course_category]);
    }

    public function update(Request $request, CourseCategory $course_category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('course_categories', 'name')->ignore($course_category->id),
            ],
            'sort_order' => 'nullable|integer|min:0|max:99999',
            'is_active' => 'boolean',
        ], [
            'name.required' => 'اسم المسار مطلوب',
            'name.unique' => 'هذا الاسم مستخدم بالفعل',
        ]);

        $course_category->update([
            'name' => trim($validated['name']),
            'sort_order' => $validated['sort_order'] ?? $course_category->sort_order,
            'is_active' => $request->boolean('is_active'),
        ]);

        // مزامنة حقل category النصي في الكورسات المرتبطة
        if ($course_category->wasChanged('name')) {
            $course_category->advancedCourses()->update(['category' => $course_category->name]);
        }

        return redirect()->route('admin.course-categories.index')
            ->with('success', __('تم تحديث المسار بنجاح.'));
    }

    public function destroy(CourseCategory $course_category): RedirectResponse
    {
        $course_category->advancedCourses()->update(['category' => null]);
        $course_category->delete();

        return redirect()->route('admin.course-categories.index')
            ->with('success', __('تم حذف المسار. الكورسات المرتبطة أصبحت بدون مسار.'));
    }
}
