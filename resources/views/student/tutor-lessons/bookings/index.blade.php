@extends('layouts.app')
@section('title', __('tutor.my_lessons'))
@section('header', __('tutor.my_lessons'))
@include('student.tutor-lessons.partials.dashboard-styles')
@section('content')
<div class="sd-page space-y-6 w-full pb-8">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="font-heading text-xl font-black text-slate-800">{{ __('tutor.my_lessons') }}</h1>
        <a href="{{ route('student.tutor-lessons.hub') }}" class="sd-btn-outline text-sm">← الرئيسية</a>
    </div>
    <div class="sd-panel">
        <div class="sd-panel-body">
            @forelse($bookings as $b)
                <div class="sd-lesson-row">
                    <a href="{{ route('student.tutor-lessons.bookings.show',$b) }}" class="sd-avatar no-underline">{{ mb_substr($b->instructor?->name ?? '?',0,1) }}</a>
                    <a href="{{ route('student.tutor-lessons.bookings.show',$b) }}" class="flex-1 min-w-0 no-underline text-inherit">
                        <p class="font-bold text-slate-800">{{ $b->instructor?->name }}</p>
                        <p class="text-xs text-slate-500">{{ display_datetime($b->scheduled_at) }} · {{ $b->statusLabel() }}</p>
                    </a>
                    @if($b->isLiveJoinable() && $b->liveJoinUrl())
                        <a href="{{ $b->liveJoinUrl() }}" class="sd-btn-primary text-xs py-2">{{ $b->liveJoinLabel() }}</a>
                    @else
                        <a href="{{ route('student.tutor-lessons.bookings.show',$b) }}" class="text-slate-300 no-underline"><i class="fas fa-chevron-left text-sm"></i></a>
                    @endif
                </div>
            @empty
                <p class="text-slate-500 text-sm text-center py-8">لا توجد حصص.</p>
            @endforelse
            <div class="mt-4">{{ $bookings->links() }}</div>
        </div>
    </div>
</div>
@endsection
