@extends('layouts.employee')

@section('title', __('طلب توظيف'))
@section('header', __('طلب توظيف — مقابلات'))

@section('content')
@php
    $app = $application;
@endphp
<div class="max-w-5xl space-y-8">
    @if(session('success'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif

    <div class="flex flex-wrap gap-2">
        <a href="{{ route('employee.hr.recruitment.openings.show', $app->opening) }}" class="text-sm font-semibold text-violet-700 hover:underline">{{ $app->opening->title }}</a>
        <span class="text-gray-400">/</span>
        <span class="text-sm font-semibold text-gray-800">{{ $app->candidate->full_name }}</span>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-6 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
        <div>
            <p class="text-gray-500 text-xs font-bold">المرشح</p>
            <p class="font-bold text-gray-900">{{ $app->candidate->full_name }}</p>
            <p class="text-gray-600">{{ $app->candidate->email }}</p>
            <a href="{{ route('employee.hr.recruitment.candidates.show', $app->candidate) }}" class="text-violet-700 text-xs font-bold mt-1 inline-block">ملف المرشح</a>
        </div>
        <div>
            <p class="text-gray-500 text-xs font-bold">الوظيفة</p>
            <p class="font-bold">{{ $app->opening->title }}</p>
            <p class="text-gray-500 text-xs mt-2">تاريخ التقديم: {{ $app->applied_at?->format('Y-m-d H:i') }}</p>
        </div>
        @if($app->cover_letter)
            <div class="md:col-span-2">
                <p class="text-gray-500 text-xs font-bold mb-1">رسالة / تغطية</p>
                <div class="bg-gray-50 rounded-lg p-3 whitespace-pre-wrap">{{ $app->cover_letter }}</div>
            </div>
        @endif
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-4">حالة الخط</h2>
        <form method="POST" action="{{ route('employee.hr.recruitment.applications.update', $app) }}" class="space-y-3 max-w-xl">
            @csrf
            @method('PATCH')
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">الحالة</label>
                <select name="status" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    @foreach(\App\Models\HrJobApplication::statusLabels() as $k => $lbl)
                        <option value="{{ $k }}" {{ old('status', $app->status) === $k ? 'selected' : '' }}>{{ $lbl }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">ملاحظات داخلية</label>
                <textarea name="internal_notes" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">{{ old('internal_notes', $app->internal_notes) }}</textarea>
            </div>
            <button type="submit" class="px-4 py-2 rounded-lg bg-gray-900 text-white text-sm font-bold">حفظ الحالة</button>
        </form>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-6" x-data="{ roundKey: '{{ old('round_key', \App\Models\HrInterview::ROUND_PHONE) }}' }">
        <h2 class="text-lg font-bold text-gray-900 mb-4">جدولة مقابلة جديدة</h2>
        <form method="POST" action="{{ route('employee.hr.recruitment.applications.interviews.store', $app) }}" class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            @csrf
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-gray-600 mb-1">نوع الجولة</label>
                <select name="round_key" x-model="roundKey" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    @foreach(\App\Models\HrInterview::roundKeyLabels() as $k => $lbl)
                        <option value="{{ $k }}">{{ $lbl }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-2" x-show="roundKey === '{{ \App\Models\HrInterview::ROUND_OTHER }}'" x-cloak>
                <label class="block text-xs font-semibold text-gray-600 mb-1">عنوان الجولة *</label>
                <input type="text" name="round_label" value="{{ old('round_label') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" placeholder="مثال: مقابلة مع المدير المباشر">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">التاريخ والوقت *</label>
                <input type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at') }}" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">المدة (دقائق)</label>
                <input type="number" name="duration_minutes" value="{{ old('duration_minutes', 45) }}" min="15" max="480" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-gray-600 mb-1">المقابل / منسق الاجتماع</label>
                <select name="interviewer_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    <option value="">—</option>
                    @foreach($interviewers as $u)
                        <option value="{{ $u->id }}" {{ (string) old('interviewer_id') === (string) $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-gray-600 mb-1">مكان أو رابط الاجتماع</label>
                <textarea name="meeting_details" rows="2" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" placeholder="قاعة، رابط Teams/Zoom…">{{ old('meeting_details') }}</textarea>
            </div>
            <div class="md:col-span-2">
                <button type="submit" class="px-4 py-2 rounded-lg bg-teal-600 text-white font-bold text-sm">إضافة للجدول</button>
            </div>
        </form>
    </div>

    <div class="space-y-6">
        <h2 class="text-lg font-bold text-gray-900">المقابلات المجدولة</h2>
        @forelse($app->interviews as $int)
            <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-4">
                <div class="flex flex-wrap justify-between gap-2">
                    <h3 class="font-bold text-gray-900">{{ $int->round_title }}</h3>
                    <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-slate-100">{{ $int->status_label }} · {{ $int->result_label }}</span>
                </div>
                <p class="text-sm text-gray-600">{{ $int->scheduled_at->format('Y-m-d H:i') }}
                    @if($int->duration_minutes)· {{ $int->duration_minutes }} دقيقة@endif
                    @if($int->interviewer)· — {{ $int->interviewer->name }}@endif
                </p>
                @if($int->meeting_details)
                    <p class="text-sm bg-gray-50 rounded p-2 whitespace-pre-wrap">{{ $int->meeting_details }}</p>
                @endif
                @if($int->notes)
                    <p class="text-xs text-gray-500">ملاحظات: {{ $int->notes }}</p>
                @endif

                <form method="POST" action="{{ route('employee.hr.recruitment.applications.interviews.update', [$app, $int]) }}" class="space-y-3 border-t border-gray-100 pt-4 text-sm">
                    @csrf
                    @method('PUT')
                    <p class="text-xs font-bold text-gray-500">تحديث المقابلة</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs text-gray-600">الجولة</label>
                            <select name="round_key" class="w-full rounded border border-gray-300 px-2 py-1.5 text-sm">
                                @foreach(\App\Models\HrInterview::roundKeyLabels() as $k => $lbl)
                                    <option value="{{ $k }}" {{ $int->round_key === $k ? 'selected' : '' }}>{{ $lbl }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-600">عنوان مخصص (إن وجد)</label>
                            <input type="text" name="round_label" value="{{ $int->round_label }}" class="w-full rounded border border-gray-300 px-2 py-1.5 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-600">الموعد</label>
                            <input type="datetime-local" name="scheduled_at" value="{{ $int->scheduled_at->format('Y-m-d\TH:i') }}" required class="w-full rounded border border-gray-300 px-2 py-1.5 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-600">المدة</label>
                            <input type="number" name="duration_minutes" value="{{ $int->duration_minutes }}" min="15" max="480" class="w-full rounded border border-gray-300 px-2 py-1.5 text-sm">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs text-gray-600">المقابل</label>
                            <select name="interviewer_id" class="w-full rounded border border-gray-300 px-2 py-1.5 text-sm">
                                <option value="">—</option>
                                @foreach($interviewers as $u)
                                    <option value="{{ $u->id }}" {{ (int) $int->interviewer_id === (int) $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs text-gray-600">مكان / رابط</label>
                            <textarea name="meeting_details" rows="2" class="w-full rounded border border-gray-300 px-2 py-1.5 text-sm">{{ $int->meeting_details }}</textarea>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-600">حالة الجلسة</label>
                            <select name="status" class="w-full rounded border border-gray-300 px-2 py-1.5 text-sm">
                                @foreach(\App\Models\HrInterview::statusLabels() as $k => $lbl)
                                    <option value="{{ $k }}" {{ $int->status === $k ? 'selected' : '' }}>{{ $lbl }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-600">النتيجة</label>
                            <select name="result" class="w-full rounded border border-gray-300 px-2 py-1.5 text-sm">
                                @foreach(\App\Models\HrInterview::resultLabels() as $k => $lbl)
                                    <option value="{{ $k }}" {{ $int->result === $k ? 'selected' : '' }}>{{ $lbl }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs text-gray-600">ملاحظات بعد المقابلة</label>
                            <textarea name="notes" rows="2" class="w-full rounded border border-gray-300 px-2 py-1.5 text-sm">{{ $int->notes }}</textarea>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <button type="submit" class="px-3 py-1.5 rounded-lg bg-gray-800 text-white text-xs font-bold">حفظ</button>
                    </div>
                </form>
                <form method="POST" action="{{ route('employee.hr.recruitment.applications.interviews.destroy', [$app, $int]) }}" onsubmit="return confirm('حذف سجل المقابلة؟');" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-xs text-rose-600 font-bold">حذف السجل</button>
                </form>
            </div>
        @empty
            <p class="text-gray-500 text-sm">لا مقابلات بعد.</p>
        @endforelse
    </div>
</div>
@endsection
