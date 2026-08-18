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
            <div class="flex justify-between gap-4 text-sm"><span class="text-slate-500">الموعد</span><strong>{{ display_datetime($booking->scheduled_at) }}</strong></div>
            <div class="flex justify-between gap-4 text-sm"><span class="text-slate-500">الحالة</span><strong>{{ $booking->statusLabel() }}</strong></div>
            @if($booking->billable_minutes > 0)
            <div class="flex justify-between gap-4 text-sm"><span class="text-slate-500">دقائق اللايف مع المعلم</span><strong>{{ $booking->billable_minutes }}</strong></div>
            @endif
            <div class="flex flex-wrap gap-2 pt-3">
                @if($booking->isLiveJoinable() && $booking->liveJoinUrl())
                    <a href="{{ $booking->liveJoinUrl() }}" class="sd-btn-primary">
                        <i class="fas fa-video text-xs"></i>
                        {{ $booking->liveJoinLabel() }}
                    </a>
                    @if($booking->status === 'in_progress')
                        <p class="w-full text-xs text-slate-500 m-0">{{ __('tutor.rejoin_lesson_hint') }}</p>
                    @endif
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
