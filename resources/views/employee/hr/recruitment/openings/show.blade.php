@extends('layouts.employee')

@section('title', $opening->title)
@section('header', __('وظيفة شاغرة'))

@section('content')
<div class="max-w-5xl space-y-6">
    @if(session('success'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif

    <div class="flex flex-wrap justify-between gap-3">
        <a href="{{ route('employee.hr.recruitment.openings.index') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900"><i class="fas fa-arrow-right ml-1"></i> {{ __('القائمة') }}</a>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('employee.hr.recruitment.openings.edit', $opening) }}" class="px-3 py-2 rounded-lg bg-gray-800 text-white text-sm font-bold">{{ __('تعديل') }}</a>
            <form method="POST" action="{{ route('employee.hr.recruitment.openings.destroy', $opening) }}" onsubmit="return confirm(@json(__('حذف الوظيفة وجميع الطلبات المرتبطة؟')));">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-3 py-2 rounded-lg bg-rose-100 text-rose-800 text-sm font-bold">{{ __('حذف') }}</button>
            </form>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-3">
        <h1 class="text-2xl font-black text-gray-900">{{ $opening->title }}</h1>
        <div class="flex flex-wrap gap-2 text-sm">
            <span class="px-2 py-0.5 rounded-full bg-violet-100 text-violet-900 font-bold">{{ $opening->status_label }}</span>
            <span class="text-gray-600">{{ $opening->employment_type_label }}</span>
            @if($opening->department)<span class="text-gray-500">· {{ $opening->department }}</span>@endif
            @if($opening->closes_at)<span class="text-gray-500">{{ __('· إغلاق:') }} {{ $opening->closes_at->format('Y-m-d') }}</span>@endif
        </div>
        <div class="prose prose-sm max-w-none text-gray-800 whitespace-pre-wrap border-t border-gray-100 pt-4">{{ $opening->description }}</div>
        @if($opening->requirements)
            <div>
                <p class="text-xs font-bold text-gray-500 mb-1">{{ __('المتطلبات') }}</p>
                <div class="text-sm text-gray-700 whitespace-pre-wrap bg-gray-50 rounded-lg p-3">{{ $opening->requirements }}</div>
            </div>
        @endif
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-4">{{ __('ربط مرشح بطلب جديد') }}</h2>
        @if($opening->isAcceptingApplications() && $candidatesForSelect->isNotEmpty())
            <form method="POST" action="{{ route('employee.hr.recruitment.applications.store') }}" class="flex flex-col sm:flex-row gap-3 items-end">
                @csrf
                <input type="hidden" name="hr_job_opening_id" value="{{ $opening->id }}">
                <div class="flex-1 w-full">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">{{ __('المرشح') }}</label>
                    <select name="hr_candidate_id" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                        @foreach($candidatesForSelect as $c)
                            <option value="{{ $c->id }}">{{ $c->full_name }} — {{ $c->email }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex-1 w-full">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">{{ __('رسالة / تغطية (اختياري)') }}</label>
                    <textarea name="cover_letter" rows="2" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" placeholder="{{ __('اختياري') }}"></textarea>
                </div>
                <button type="submit" class="px-4 py-2 rounded-lg bg-violet-600 text-white font-bold text-sm">{{ __('تسجيل الطلب') }}</button>
            </form>
        @elseif(!$opening->isAcceptingApplications())
            <p class="text-sm text-amber-800">{{ __('الوظيفة غير مفتوحة للتقديم (الحالة أو تاريخ الإغلاق).') }}</p>
        @else
            <p class="text-sm text-gray-500">{{ __('جميع المرشحين مرتبطون بالفعل أو لا يوجد مرشحون في النظام. أضف مرشحاً من قائمة المرشحين.') }}</p>
        @endif
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-100 font-bold">{{ __('طلبات التوظيف') }} ({{ $opening->applications->count() }})</div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 font-semibold">
                    <tr>
                        <th class="text-right px-4 py-2">{{ __('المرشح') }}</th>
                        <th class="text-right px-4 py-2">{{ __('الحالة') }}</th>
                        <th class="text-right px-4 py-2">{{ __('التقديم') }}</th>
                        <th class="text-right px-4 py-2"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($opening->applications as $app)
                    <tr>
                        <td class="px-4 py-2">{{ $app->candidate?->full_name }}</td>
                        <td class="px-4 py-2">{{ $app->status_label }}</td>
                        <td class="px-4 py-2 text-gray-500 whitespace-nowrap">{{ $app->applied_at?->format('Y-m-d') }}</td>
                        <td class="px-4 py-2"><a href="{{ route('employee.hr.recruitment.applications.show', $app) }}" class="text-violet-700 font-bold">{{ __('إدارة') }}</a></td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-4 py-8 text-center text-gray-500">{{ __('لا طلبات بعد.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
