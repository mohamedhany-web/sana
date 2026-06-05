@extends('layouts.admin')

@section('title', 'تعديل مسار')
@section('header', __('admin.course_categories'))

@section('content')
<div class="w-full max-w-full px-4 py-6 space-y-6">
    <div class="section-card max-w-2xl">
        <div class="section-card-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <nav class="text-sm text-slate-500 mb-2">
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-sky-600">{{ __('admin.dashboard') }}</a>
                    <span class="mx-2">/</span>
                    <a href="{{ route('admin.course-categories.index') }}" class="hover:text-sky-600">{{ __('admin.course_categories') }}</a>
                    <span class="mx-2">/</span>
                    <span class="text-slate-700">تعديل</span>
                </nav>
                <h1 class="text-xl font-bold text-slate-800">تعديل المسار</h1>
            </div>
            <a href="{{ route('admin.course-categories.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                <i class="fas fa-arrow-right"></i>
                العودة
            </a>
        </div>
        <div class="p-6 sm:p-8 border-t border-slate-100">
            <form method="POST" action="{{ route('admin.course-categories.update', $category) }}" class="space-y-6">
                @csrf
                @method('PUT')
                <div class="space-y-2">
                    <label for="name" class="block text-sm font-semibold text-slate-700">اسم المسار *</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $category->name) }}" required maxlength="255"
                           class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-slate-800 focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                    @error('name')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="space-y-2">
                    <label for="sort_order" class="block text-sm font-semibold text-slate-700">ترتيب العرض</label>
                    <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $category->sort_order) }}" min="0" max="99999"
                           class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-slate-800 focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                    @error('sort_order')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <label class="flex items-center gap-2">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" class="rounded border-slate-300 text-sky-600 focus:ring-sky-500" {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                    <span class="text-sm text-slate-700">مسار نشط (يظهر في قائمة التصفية العامة)</span>
                </label>
                <div class="flex gap-3 pt-2">
                    <button type="submit" class="inline-flex items-center gap-2 bg-sky-600 hover:bg-sky-700 text-white px-6 py-3 rounded-xl font-semibold transition">
                        حفظ التغييرات
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
