@extends('layouts.admin')

@section('title', 'إضافة مرحلة دراسية')
@section('header', 'إضافة مرحلة دراسية')

@section('content')
<div class="w-full max-w-3xl mx-auto px-4 py-6 space-y-6">
    <div class="bg-gradient-to-l from-[#1D4EDB] via-indigo-600 to-violet-600 rounded-2xl p-6 text-white shadow-lg">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <nav class="text-sm text-white/80 mb-2">
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-white">لوحة التحكم</a>
                    <span class="mx-2">/</span>
                    <a href="{{ route('admin.academic-years.index') }}" class="hover:text-white">{{ __('admin.academic_years') }}</a>
                    <span class="mx-2">/</span>
                    <span>إضافة</span>
                </nav>
                <h1 class="text-xl font-bold">إضافة مرحلة دراسية</h1>
                <p class="text-sm text-white/90 mt-1">مثال: أول ابتدائي، ثالث متوسط، ثالث ثانوي علمي.</p>
            </div>
            <a href="{{ route('admin.academic-years.index') }}" class="inline-flex items-center gap-2 bg-white/15 hover:bg-white/25 text-white px-4 py-2 rounded-xl text-sm font-medium border border-white/25">
                <i class="fas fa-arrow-right"></i> رجوع
            </a>
        </div>
    </div>

    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100">
            <h2 class="font-bold text-slate-900">بيانات المرحلة</h2>
        </div>
        <form method="POST" action="{{ route('admin.academic-years.store') }}" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="name" class="block text-sm font-semibold text-slate-700 mb-1">اسم المرحلة <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                           class="w-full rounded-xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500/20"
                           placeholder="مثال: الثالث الثانوي">
                    @error('name')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="code" class="block text-sm font-semibold text-slate-700 mb-1">الرمز <span class="text-rose-500">*</span></label>
                    <input type="text" name="code" id="code" value="{{ old('code') }}" required
                           class="w-full rounded-xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500/20"
                           placeholder="مثال: G12">
                    @error('code')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-semibold text-slate-700 mb-1">الوصف</label>
                    <textarea name="description" id="description" rows="3"
                              class="w-full rounded-xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500/20"
                              placeholder="وصف مختصر للمرحلة (اختياري)">{{ old('description') }}</textarea>
                    @error('description')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="icon" class="block text-sm font-semibold text-slate-700 mb-1">الأيقونة</label>
                    <select name="icon" id="icon" class="w-full rounded-xl border-slate-200 text-sm">
                        @foreach([
                            'fas fa-layer-group' => 'مرحلة دراسية',
                            'fas fa-child' => 'ابتدائي',
                            'fas fa-user-graduate' => 'ثانوي',
                            'fas fa-book-open' => 'تعليم عام',
                            'fas fa-school' => 'مدرسة',
                        ] as $value => $label)
                            <option value="{{ $value }}" @selected(old('icon', 'fas fa-layer-group') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="color" class="block text-sm font-semibold text-slate-700 mb-1">اللون</label>
                    <input type="color" name="color" id="color" value="{{ old('color', '#1D4EDB') }}"
                           class="w-full h-11 rounded-xl border border-slate-200 cursor-pointer">
                </div>
                <div>
                    <label for="order" class="block text-sm font-semibold text-slate-700 mb-1">ترتيب الظهور</label>
                    <input type="number" name="order" id="order" value="{{ old('order', 0) }}" min="0"
                           class="w-full rounded-xl border-slate-200 text-sm">
                </div>
                <div>
                    <label for="price" class="block text-sm font-semibold text-slate-700 mb-1">السعر ({{ __('public.currency') }})</label>
                    <input type="number" name="price" id="price" value="{{ old('price', 0) }}" min="0" step="0.01"
                           class="w-full rounded-xl border-slate-200 text-sm">
                    <p class="text-xs text-slate-500 mt-1">0 = مجاني</p>
                </div>
                <div class="md:col-span-2">
                    <label for="thumbnail" class="block text-sm font-semibold text-slate-700 mb-1">صورة (اختياري)</label>
                    <input type="file" name="thumbnail" id="thumbnail" accept="image/*"
                           class="w-full rounded-xl border-slate-200 text-sm">
                </div>
            </div>

            <label class="flex items-start gap-3 p-4 rounded-xl bg-slate-50 border border-slate-200 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" class="mt-1 rounded border-slate-300 text-indigo-600" @checked(old('is_active', true))>
                <span>
                    <span class="block text-sm font-semibold text-slate-800">مرحلة نشطة</span>
                    <span class="block text-xs text-slate-500 mt-0.5">المراحل النشطة فقط تظهر للطلاب ويمكن إضافة مواد تحتها.</span>
                </span>
            </label>

            <div class="flex flex-wrap gap-3 pt-2 border-t border-slate-100">
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-indigo-600 text-white text-sm font-bold hover:bg-indigo-700">
                    <i class="fas fa-save"></i> حفظ المرحلة
                </button>
                <a href="{{ route('admin.academic-years.index') }}" class="inline-flex items-center px-5 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50">إلغاء</a>
            </div>
        </form>
    </div>
</div>
@endsection
