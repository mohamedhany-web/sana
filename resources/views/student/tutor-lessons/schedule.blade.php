@extends('layouts.app')
@section('title', __('tutor.self_schedule_title'))
@section('header', __('tutor.self_schedule_title'))
@include('student.tutor-lessons.partials.dashboard-styles')
@section('content')
<div class="sd-page w-full pb-8 max-w-3xl">
    <a href="{{ route('student.tutor-lessons.hub') }}" class="text-sm sd-link mb-4 inline-block">← {{ __('tutor.student_hub_title') }}</a>
    <div class="sd-panel mb-4">
        <div class="sd-panel-body">
            <p class="text-sm text-slate-600">{{ __('tutor.self_schedule_hint') }}</p>
            <p class="text-xs text-slate-500 mt-2">المتبقي من باقتك: <strong>{{ max(0, (int) $profile->lesson_hours_quota - (int) $profile->lesson_hours_used) }}</strong> ساعة</p>
        </div>
    </div>
    @if($subjects->isNotEmpty())
    <form method="get" class="mb-4 flex gap-2 items-end">
        <div class="flex-1">
            <label class="text-xs font-bold">المادة</label>
            <select name="subject_id" class="w-full border rounded-lg px-3 py-2" onchange="this.form.submit()">
                @foreach($subjects as $s)
                    <option value="{{ $s->id }}" @selected($subjectId == $s->id)>{{ $s->name }}</option>
                @endforeach
            </select>
        </div>
    </form>
    @endif
    @if($errors->any())
        <div class="sd-panel mb-4 border-rose-200 bg-rose-50 text-rose-700 text-sm p-4">
            @foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach
        </div>
    @endif
    @if(empty($slots))
        <div class="sd-panel sd-panel-body text-center text-slate-500 py-10">{{ __('tutor.no_slots') }}</div>
    @else
        <p class="text-sm font-bold text-slate-700 mb-3">{{ __('tutor.pick_slot') }}</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            @foreach($slots as $slot)
            <form method="post" action="{{ route('student.tutor-lessons.schedule.store') }}" class="sd-panel hover:border-violet-400 transition-colors">
                @csrf
                <input type="hidden" name="scheduled_at" value="{{ \Carbon\Carbon::parse($slot['scheduled_at'])->format('Y-m-d H:i:s') }}">
                @if($subjectId)<input type="hidden" name="academic_subject_id" value="{{ $subjectId }}">@endif
                <button type="submit" class="w-full text-right p-4">
                    <span class="font-bold text-slate-800 block">{{ $slot['label'] }}</span>
                    <span class="text-xs text-violet-600 mt-1">حجز ← تعيين معلم تلقائياً</span>
                </button>
            </form>
            @endforeach
        </div>
    @endif
</div>
@endsection
