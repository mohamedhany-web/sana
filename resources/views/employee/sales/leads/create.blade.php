@extends('layouts.employee')

@section('title', __('إضافة Lead'))
@section('header', __('إضافة عميل محتمل'))

@section('content')
<div class="space-y-6 max-w-3xl">
    <a href="{{ route('employee.sales.leads.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-gray-600 hover:text-gray-900">
        <i class="fas fa-arrow-right"></i> القائمة
    </a>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <form method="POST" action="{{ route('employee.sales.leads.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">{{ __('الاسم') }} <span class="text-rose-600">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500">
                @error('name')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">{{ __('البريد') }}</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500">
                    @error('email')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">{{ __('الهاتف') }}</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500">
                    @error('phone')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">{{ __('الشركة') }}</label>
                <input type="text" name="company" value="{{ old('company') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500">
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">{{ __('المصدر') }} <span class="text-rose-600">*</span></label>
                    <select name="source" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500">
                        @foreach(\App\Models\SalesLead::sourceLabels() as $val => $label)
                            <option value="{{ $val }}" {{ old('source', \App\Models\SalesLead::SOURCE_OTHER) === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">{{ __('الحالة الأولية') }}</label>
                    <select name="status" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500">
                        <option value="{{ \App\Models\SalesLead::STATUS_NEW }}" {{ old('status', \App\Models\SalesLead::STATUS_NEW) === \App\Models\SalesLead::STATUS_NEW ? 'selected' : '' }}>{{ __('جديد') }}</option>
                        <option value="{{ \App\Models\SalesLead::STATUS_CONTACTED }}" {{ old('status') === \App\Models\SalesLead::STATUS_CONTACTED ? 'selected' : '' }}>{{ __('تم التواصل') }}</option>
                        <option value="{{ \App\Models\SalesLead::STATUS_QUALIFIED }}" {{ old('status') === \App\Models\SalesLead::STATUS_QUALIFIED ? 'selected' : '' }}>{{ __('مؤهل') }}</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">{{ __('كورس مهتم به') }}</label>
                <select name="interested_advanced_course_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500">
                    <option value="">—</option>
                    @foreach($courses as $c)
                        <option value="{{ $c->id }}" {{ (string) old('interested_advanced_course_id') === (string) $c->id ? 'selected' : '' }}>{{ $c->title }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">{{ __('ملاحظات') }}</label>
                <textarea name="notes" rows="4" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500">{{ old('notes') }}</textarea>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="px-5 py-2.5 rounded-lg bg-teal-600 hover:bg-teal-700 text-white text-sm font-bold">{{ __('حفظ') }}</button>
                <a href="{{ route('employee.sales.leads.index') }}" class="px-5 py-2.5 rounded-lg bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm font-semibold">{{ __('إلغاء') }}</a>
            </div>
        </form>
    </div>
</div>
@endsection
