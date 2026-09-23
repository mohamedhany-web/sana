@extends('layouts.employee')

@section('title', __('مرشح جديد'))
@section('header', __('إضافة مرشح'))

@section('content')
<div class="max-w-2xl space-y-6">
    <a href="{{ route('employee.hr.recruitment.candidates.index') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900"><i class="fas fa-arrow-right ml-1"></i> {{ __('القائمة') }}</a>

    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <form method="POST" action="{{ route('employee.hr.recruitment.candidates.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">{{ __('الاسم الكامل *') }}</label>
                <input type="text" name="full_name" value="{{ old('full_name') }}" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">{{ __('البريد *') }}</label>
                    <input type="email" name="email" value="{{ old('email') }}" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">{{ __('الهاتف') }}</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">{{ __('رابط معرض أعمال (اختياري)') }}</label>
                <input type="url" name="portfolio_url" value="{{ old('portfolio_url') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">{{ __('مصدر التواصل *') }}</label>
                <select name="source" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    @foreach(\App\Models\HrCandidate::sourceLabels() as $k => $lbl)
                        <option value="{{ $k }}" {{ old('source', \App\Models\HrCandidate::SOURCE_OTHER) === $k ? 'selected' : '' }}>{{ $lbl }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">{{ __('السيرة (PDF / Word — حتى 40 ميجابايت)') }}</label>
                <input type="file" name="cv" accept=".pdf,.doc,.docx" class="w-full text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">{{ __('ملاحظات') }}</label>
                <textarea name="notes" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">{{ old('notes') }}</textarea>
            </div>
            <button type="submit" class="px-5 py-2.5 rounded-lg bg-slate-800 text-white font-bold text-sm">{{ __('حفظ') }}</button>
        </form>
    </div>
</div>
@endsection
