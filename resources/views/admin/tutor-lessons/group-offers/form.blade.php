@extends('layouts.admin')

@php
    $offer = $offer ?? null;
    $isEdit = $offer !== null;
    $selectedPlans = old('subscription_plan_keys', $offer?->subscription_plan_keys ?? []);
@endphp

@section('title', $isEdit ? 'تعديل عرض مجموعة' : 'عرض مجموعة جديد')

@section('content')
<div class="max-w-3xl mx-auto">
    @include('admin.tutor-lessons._nav')

    <h1 class="text-2xl font-black text-slate-900 mb-6">{{ $isEdit ? 'تعديل عرض المجموعة' : 'عرض حجز مجموعة جديد' }}</h1>

    <form method="post" action="{{ $isEdit ? route('admin.tutor-lessons.group-offers.update', $offer) : route('admin.tutor-lessons.group-offers.store') }}" class="bg-white rounded-2xl border border-slate-200 p-6 space-y-5">
        @csrf
        @if($isEdit) @method('PUT') @endif

        @if($errors->any())
            <div class="p-4 rounded-xl bg-rose-50 text-rose-800 text-sm space-y-1">
                @foreach($errors->all() as $e)<p class="m-0">{{ $e }}</p>@endforeach
            </div>
        @endif

        <div>
            <label class="block text-sm font-bold text-slate-700 mb-1">عنوان العرض *</label>
            <input type="text" name="title" required maxlength="160" value="{{ old('title', $offer?->title) }}" class="w-full px-3 py-2 rounded-lg border border-slate-200">
        </div>

        <div>
            <label class="block text-sm font-bold text-slate-700 mb-1">المعلّم *</label>
            <select name="instructor_id" required class="w-full px-3 py-2 rounded-lg border border-slate-200">
                <option value="">— اختر —</option>
                @foreach($instructors as $p)
                    <option value="{{ $p->user_id }}" @selected((int) old('instructor_id', $offer?->instructor_id) === (int) $p->user_id)>{{ $p->user?->name }}</option>
                @endforeach
            </select>
            <p class="text-xs text-slate-500 mt-1">يجب أن يدعم المعلّم نوع «مجموعة صغيرة» في إعداداته.</p>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">أقل عدد طلاب *</label>
                <input type="number" name="min_group_size" min="2" max="30" required value="{{ old('min_group_size', $offer?->min_group_size ?? 2) }}" class="w-full px-3 py-2 rounded-lg border border-slate-200">
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">أقصى عدد طلاب *</label>
                <input type="number" name="max_group_size" min="2" max="30" required value="{{ old('max_group_size', $offer?->max_group_size ?? 6) }}" class="w-full px-3 py-2 rounded-lg border border-slate-200">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">مدة الحصة (دقيقة) *</label>
                <input type="number" name="duration_minutes" min="30" max="180" required value="{{ old('duration_minutes', $offer?->duration_minutes ?? 60) }}" class="w-full px-3 py-2 rounded-lg border border-slate-200">
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">سعر عرض (اختياري)</label>
                <input type="number" name="display_price" min="0" step="0.01" value="{{ old('display_price', $offer?->display_price) }}" class="w-full px-3 py-2 rounded-lg border border-slate-200">
            </div>
        </div>

        <div>
            <label class="block text-sm font-bold text-slate-700 mb-1">باقة دورات مرتبطة (اختياري)</label>
            <select name="package_id" class="w-full px-3 py-2 rounded-lg border border-slate-200">
                <option value="">— بدون —</option>
                @foreach($packages as $pkg)
                    <option value="{{ $pkg->id }}" @selected((int) old('package_id', $offer?->package_id) === (int) $pkg->id)>{{ $pkg->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-bold text-slate-700 mb-1">المادة (اختياري)</label>
            <select name="academic_subject_id" class="w-full px-3 py-2 rounded-lg border border-slate-200">
                <option value="">— كل المواد —</option>
                @foreach($subjects as $s)
                    <option value="{{ $s->id }}" @selected((int) old('academic_subject_id', $offer?->academic_subject_id) === (int) $s->id)>{{ $s->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-bold text-slate-700 mb-2">اشتراكات الطلاب المسموح لها</label>
            <p class="text-xs text-slate-500 mb-2">اترك الكل غير محدّد = أي باقة تفعّل فيها «حجز المجموعات».</p>
            <div class="flex flex-wrap gap-3">
                @foreach($planKeys as $key)
                    <label class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border border-slate-200 text-sm">
                        <input type="checkbox" name="subscription_plan_keys[]" value="{{ $key }}" @checked(in_array($key, $selectedPlans, true))>
                        {{ $studentPlans[$key]['label'] ?? $key }}
                    </label>
                @endforeach
            </div>
        </div>

        <div>
            <label class="block text-sm font-bold text-slate-700 mb-1">الوصف</label>
            <textarea name="description" rows="3" class="w-full px-3 py-2 rounded-lg border border-slate-200">{{ old('description', $offer?->description) }}</textarea>
        </div>

        <div class="grid grid-cols-2 gap-4 items-end">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">ترتيب العرض</label>
                <input type="number" name="sort_order" min="0" value="{{ old('sort_order', $offer?->sort_order ?? 0) }}" class="w-full px-3 py-2 rounded-lg border border-slate-200">
            </div>
            <label class="inline-flex items-center gap-2 text-sm font-bold text-slate-700">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" @checked(filter_var(old('is_active', $offer?->is_active ?? true), FILTER_VALIDATE_BOOLEAN))>
                نشط
            </label>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-violet-600 text-white font-bold text-sm">حفظ</button>
            <a href="{{ route('admin.tutor-lessons.group-offers.index') }}" class="px-6 py-2.5 rounded-xl border border-slate-200 text-slate-700 font-bold text-sm">إلغاء</a>
        </div>
    </form>
</div>
@endsection
