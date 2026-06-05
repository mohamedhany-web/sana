@extends('layouts.app')
@section('title', 'حصتي')
@section('header', 'تفاصيل الحصة')
@include('student.tutor-lessons.partials.dashboard-styles')
@section('content')
<div class="sd-page w-full pb-8 max-w-2xl">
    <a href="{{ route('student.tutor-lessons.bookings.index') }}" class="text-sm sd-link mb-4 inline-block">← كل الحصص</a>
    <div class="sd-panel">
        <div class="sd-panel-head"><h2 class="font-bold text-slate-800">حجز #{{ $booking->code }}</h2></div>
        <div class="sd-panel-body space-y-3">
            <div class="flex justify-between gap-4 text-sm"><span class="text-slate-500">المعلم</span><strong>{{ $booking->instructor?->name }}</strong></div>
            <div class="flex justify-between gap-4 text-sm"><span class="text-slate-500">الموعد</span><strong>{{ $booking->scheduled_at?->format('Y-m-d H:i') }}</strong></div>
            <div class="flex justify-between gap-4 text-sm"><span class="text-slate-500">الحالة</span><strong>{{ $booking->statusLabel() }}</strong></div>
            @if($booking->billable_minutes > 0)
            <div class="flex justify-between gap-4 text-sm"><span class="text-slate-500">دقائق مشتركة</span><strong>{{ $booking->billable_minutes }}</strong></div>
            @endif
            <div class="flex flex-wrap gap-2 pt-3">
                @if($booking->classroomMeeting && in_array($booking->status,['confirmed','in_progress']))
                    <a href="{{ url('classroom/join/'.$booking->classroomMeeting->code) }}" class="sd-btn-primary">{{ __('tutor.enter_lesson') }}</a>
                @endif
                @if($booking->status==='pending')
                    <form method="post" action="{{ route('student.tutor-lessons.bookings.cancel', $booking) }}">@csrf<button type="submit" class="sd-btn-outline">إلغاء</button></form>
                @endif
                @if($booking->status==='completed')
                    <a href="{{ route('student.tutor-lessons.bookings.rate', $booking) }}" class="sd-btn-outline">{{ __('tutor.rate_lesson') }}</a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
