@extends('layouts.employee')

@section('title', $candidate->full_name)
@section('header', __('ملف مرشح'))

@section('content')
<div class="max-w-4xl space-y-6">
    @if(session('success'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif

    <div class="flex flex-wrap justify-between gap-3">
        <a href="{{ route('employee.hr.recruitment.candidates.index') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900"><i class="fas fa-arrow-right ml-1"></i> {{ __('القائمة') }}</a>
        <div class="flex gap-2">
            <a href="{{ route('employee.hr.recruitment.candidates.edit', $candidate) }}" class="px-3 py-2 rounded-lg bg-gray-800 text-white text-sm font-bold">{{ __('تعديل') }}</a>
            @if($candidate->applications->count() === 0)
                <form method="POST" action="{{ route('employee.hr.recruitment.candidates.destroy', $candidate) }}" onsubmit="return confirm(@json(__('حذف المرشح؟')));">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-3 py-2 rounded-lg bg-rose-100 text-rose-800 text-sm font-bold">{{ __('حذف') }}</button>
                </form>
            @endif
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-2">
        <h1 class="text-xl font-black text-gray-900">{{ $candidate->full_name }}</h1>
        <p class="text-sm text-gray-600">{{ $candidate->email }} @if($candidate->phone)· {{ $candidate->phone }}@endif</p>
        <p class="text-xs text-gray-500">{{ __('المصدر:') }} {{ $candidate->source_label }}</p>
        @if($candidate->portfolio_url)
            <p class="text-sm"><a href="{{ $candidate->portfolio_url }}" target="_blank" class="text-violet-700 font-bold underline">{{ __('رابط الأعمال') }}</a></p>
        @endif
        @if($candidate->cv_path)
            <p class="text-sm"><a href="{{ $candidate->cvUrl() }}" target="_blank" class="text-violet-700 font-bold underline">{{ __('السيرة الذاتية') }}</a></p>
        @endif
        @if($candidate->notes)
            <div class="mt-4 text-sm bg-gray-50 rounded-lg p-3 whitespace-pre-wrap">{{ $candidate->notes }}</div>
        @endif
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="text-lg font-bold mb-4">{{ __('التقديم على وظيفة') }}</h2>
        @if($openingsForSelect->isNotEmpty())
            <form method="POST" action="{{ route('employee.hr.recruitment.applications.store') }}" class="flex flex-col sm:flex-row gap-3 items-end">
                @csrf
                <input type="hidden" name="hr_candidate_id" value="{{ $candidate->id }}">
                <div class="flex-1 w-full">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">{{ __('الوظيفة') }}</label>
                    <select name="hr_job_opening_id" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                        @foreach($openingsForSelect as $o)
                            <option value="{{ $o->id }}">{{ $o->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex-1 w-full">
                    <input type="text" name="cover_letter" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" placeholder="{{ __('ملاحظة مع الطلب (اختياري)') }}">
                </div>
                <button type="submit" class="px-4 py-2 rounded-lg bg-violet-600 text-white font-bold text-sm">{{ __('تسجيل') }}</button>
            </form>
        @else
            <p class="text-sm text-gray-500">{{ __('لا توجد وظائف مفتوحة متاحة لهذا المرشح (أو سبق التقديم على كل المفتوحة).') }}</p>
        @endif
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-3 border-b font-bold">{{ __('طلباته') }}</div>
        <ul class="divide-y divide-gray-100 text-sm">
            @forelse($candidate->applications as $app)
                <li class="px-5 py-3 flex justify-between">
                    <span>{{ $app->opening?->title }}</span>
                    <a href="{{ route('employee.hr.recruitment.applications.show', $app) }}" class="text-violet-700 font-bold">{{ $app->status_label }}</a>
                </li>
            @empty
                <li class="px-5 py-8 text-center text-gray-500">{{ __('لا طلبات.') }}</li>
            @endforelse
        </ul>
    </div>
</div>
@endsection
