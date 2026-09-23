@extends('layouts.employee')

@section('title', __('وظيفة جديدة'))
@section('header', __('إضافة وظيفة شاغرة'))

@section('content')
<div class="max-w-3xl space-y-6">
    <a href="{{ route('employee.hr.recruitment.openings.index') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900"><i class="fas fa-arrow-right ml-1"></i> القائمة</a>

    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <form method="POST" action="{{ route('employee.hr.recruitment.openings.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">المسمى الوظيفي *</label>
                <input type="text" name="title" value="{{ old('title') }}" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">القسم</label>
                <input type="text" name="department" value="{{ old('department') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">وصف الوظيفة *</label>
                <textarea name="description" rows="6" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">{{ old('description') }}</textarea>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">المتطلبات</label>
                <textarea name="requirements" rows="4" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">{{ old('requirements') }}</textarea>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">نوع التوظيف *</label>
                    <select name="employment_type" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                        @foreach(\App\Models\HrJobOpening::employmentTypeLabels() as $k => $lbl)
                            <option value="{{ $k }}" {{ old('employment_type', \App\Models\HrJobOpening::EMP_FULL_TIME) === $k ? 'selected' : '' }}>{{ $lbl }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">الحالة *</label>
                    <select name="status" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                        @foreach(\App\Models\HrJobOpening::statusLabels() as $k => $lbl)
                            <option value="{{ $k }}" {{ old('status', \App\Models\HrJobOpening::STATUS_DRAFT) === $k ? 'selected' : '' }}>{{ $lbl }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">إغلاق التقديم (اختياري)</label>
                <input type="date" name="closes_at" value="{{ old('closes_at') }}" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
            </div>
            <div class="flex gap-2 pt-2">
                <button type="submit" class="px-5 py-2.5 rounded-lg bg-violet-600 text-white font-bold text-sm">حفظ</button>
                <a href="{{ route('employee.hr.recruitment.openings.index') }}" class="px-5 py-2.5 rounded-lg bg-gray-200 text-gray-800 font-semibold text-sm">إلغاء</a>
            </div>
        </form>
    </div>
</div>
@endsection
