@extends('layouts.admin')

@section('title', __('admin.course_categories'))
@section('header', __('admin.course_categories'))

@section('content')
<div class="w-full max-w-full px-4 py-6 space-y-6">
    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            {{ session('success') }}
        </div>
    @endif

    <div class="section-card">
        <div class="section-card-header">
            <h1 class="text-xl font-bold text-slate-800">{{ __('admin.course_categories') }}</h1>
            <p class="text-sm text-slate-600 mt-1">
                المسارات التي تظهر في قائمة التصفية بصفحة الكورسات العامة. أضف اسماً لكل مسار ثم اختره عند إنشاء أو تعديل كورس.
            </p>
        </div>
        <div class="p-6 sm:p-8 border-t border-slate-100">
            <h2 class="text-sm font-semibold text-slate-700 mb-4">إضافة مسار جديد</h2>
            <form method="POST" action="{{ route('admin.course-categories.store') }}" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                @csrf
                <div class="md:col-span-5 space-y-2">
                    <label for="name" class="block text-sm font-medium text-slate-700">اسم المسار *</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required maxlength="255"
                           class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-slate-800 focus:ring-2 focus:ring-sky-500 focus:border-sky-500"
                           placeholder="مثال: التدريس التفاعلي">
                    @error('name')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="md:col-span-3 space-y-2">
                    <label for="sort_order" class="block text-sm font-medium text-slate-700">ترتيب العرض</label>
                    <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order') }}" min="0" max="99999"
                           class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-slate-800 focus:ring-2 focus:ring-sky-500 focus:border-sky-500"
                           placeholder="تلقائي إن وُجد فارغاً">
                    @error('sort_order')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="md:col-span-2 flex items-center gap-2 pb-3">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" id="is_active" value="1" class="rounded border-slate-300 text-sky-600 focus:ring-sky-500" {{ old('is_active', true) ? 'checked' : '' }}>
                    <label for="is_active" class="text-sm text-slate-700">نشط</label>
                </div>
                <div class="md:col-span-2">
                    <button type="submit" class="w-full md:w-auto inline-flex items-center justify-center gap-2 bg-sky-600 hover:bg-sky-700 text-white px-5 py-3 rounded-xl font-semibold transition">
                        <i class="fas fa-plus"></i>
                        إضافة
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="section-card overflow-hidden">
        <div class="section-card-header">
            <h2 class="text-base font-semibold text-slate-800">المسارات الحالية</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="text-right px-4 py-3 font-semibold">الاسم</th>
                        <th class="text-right px-4 py-3 font-semibold w-28">الترتيب</th>
                        <th class="text-right px-4 py-3 font-semibold w-28">الحالة</th>
                        <th class="text-right px-4 py-3 font-semibold w-40">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($categories as $row)
                        <tr class="hover:bg-slate-50/80">
                            <td class="px-4 py-3 text-slate-800 font-medium">{{ $row->name }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $row->sort_order }}</td>
                            <td class="px-4 py-3">
                                @if($row->is_active)
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">نشط</span>
                                @else
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-600">معطّل</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-2">
                                    <a href="{{ route('admin.course-categories.edit', $row) }}" class="inline-flex items-center gap-1 text-sky-600 hover:text-sky-800 text-xs font-semibold">
                                        <i class="fas fa-pen"></i> تعديل
                                    </a>
                                    <form method="POST" action="{{ route('admin.course-categories.destroy', $row) }}" class="inline" onsubmit="return confirm('حذف هذا المسار؟ الكورسات المرتبطة ستُزال ربطها بالمسار.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-1 text-rose-600 hover:text-rose-800 text-xs font-semibold">
                                            <i class="fas fa-trash"></i> حذف
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-10 text-center text-slate-500">لا توجد مسارات بعد. أضف مساراً بالأعلى ليظهر في صفحة الكورسات.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
