@extends('layouts.app')
@section('header', __('tutor.parent_hub_title'))
@section('title', __('tutor.parent_hub_title'))
@section('content')
@include('partials.tutor-lesson-ui')
<div class="tl-page max-w-4xl mx-auto py-4 space-y-4">
    <div class="tl-hero">
        <h1>{{ __('tutor.parent_hub_title') }}</h1>
        <p>متابعة حصص الأبناء وتقييمات المعلمين وطلب مساعدة في إيجاد معلم.</p>
        <a href="{{ route('parent.tutor-lessons.assisted') }}" class="tl-btn tl-btn-primary mt-3 inline-flex">طلب مساعدة</a>
    </div>
    @forelse($bookings as $b)
        @php $eval = $b->instructorEvaluation(); @endphp
        <a href="{{ route('parent.tutor-lessons.bookings.show',$b) }}" class="tl-card block no-underline text-inherit">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <div>
                    <strong>{{ $b->student?->name }}</strong> — {{ $b->instructor?->name }}
                    <div class="text-sm text-slate-600">{{ display_datetime($b->scheduled_at) }} · {{ $b->statusLabel() }}</div>
                </div>
                @if($eval)
                    <span class="text-xs font-bold rounded-full bg-emerald-50 text-emerald-800 px-2.5 py-1">{{ __('tutor.evaluation_available_badge') }} · {{ (int) $eval->rating }}★</span>
                @elseif($b->status === 'completed')
                    <span class="text-xs font-bold rounded-full bg-amber-50 text-amber-800 px-2.5 py-1">{{ __('tutor.evaluation_pending_badge') }}</span>
                @endif
            </div>
        </a>
    @empty
        <div class="tl-card text-slate-500 text-sm">لا توجد حصص حديثة.</div>
    @endforelse
</div>
@endsection
