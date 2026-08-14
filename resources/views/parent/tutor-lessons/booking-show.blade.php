@extends('layouts.app')
@section('title', 'حصة الابن')
@include('partials.tutor-lesson-ui')
@section('content')
@php
    $evaluation = $instructorEvaluation ?? $booking->instructorEvaluation();
@endphp
<div class="tl-page max-w-xl mx-auto py-2 space-y-4">
    @if(session('success'))
        <div class="tl-card" style="border-color:#a7f3d0;background:#ecfdf5;color:#065f46;">{{ session('success') }}</div>
    @endif

    <div class="tl-card space-y-2">
        <p class="m-0"><strong>{{ $booking->student?->name }}</strong> مع {{ $booking->instructor?->name }}</p>
        <p class="m-0 text-sm text-slate-600">{{ $booking->scheduled_at?->format('Y-m-d H:i') }} — {{ $booking->statusLabel() }}</p>
        @if($booking->subject)
            <p class="m-0 text-sm text-slate-600">{{ $booking->subject->name }}</p>
        @endif
        @if($booking->classroomMeeting && in_array($booking->status, ['confirmed', 'in_progress'], true))
            <a href="{{ url('classroom/join/'.$booking->classroomMeeting->code) }}" class="tl-btn tl-btn-primary">{{ __('tutor.enter_lesson') }}</a>
        @endif
    </div>

    @if($booking->status === 'completed')
        <div class="tl-card space-y-3">
            <h2 class="text-base font-black m-0">{{ __('tutor.parent_evaluation_title') }}</h2>
            @if($evaluation)
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div class="rounded-xl bg-amber-50 border border-amber-100 p-3">
                        <p class="text-xs text-amber-800 m-0 mb-1">{{ __('tutor.student_rating_label') }}</p>
                        <p class="text-xl font-black m-0 text-amber-900">{{ (int) $evaluation->rating }}★</p>
                    </div>
                    <div class="rounded-xl bg-sky-50 border border-sky-100 p-3">
                        <p class="text-xs text-sky-800 m-0 mb-1">{{ __('tutor.lesson_rating_label') }}</p>
                        <p class="text-xl font-black m-0 text-sky-900">{{ (int) ($evaluation->lesson_rating ?? $evaluation->rating) }}★</p>
                    </div>
                </div>
                @if($evaluation->comment)
                    <div class="rounded-xl bg-slate-50 border border-slate-200 p-3 text-sm whitespace-pre-line">{{ $evaluation->comment }}</div>
                @endif
                <p class="text-xs text-slate-500 m-0">{{ __('tutor.parent_evaluation_from', ['teacher' => $booking->instructor?->name ?? 'المعلم']) }}</p>
            @else
                <p class="text-sm text-amber-800 m-0">{{ __('tutor.parent_evaluation_pending') }}</p>
            @endif
        </div>
    @endif
</div>
@endsection
