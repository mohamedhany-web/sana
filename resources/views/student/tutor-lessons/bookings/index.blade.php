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
                <a href="{{ route('student.tutor-lessons.bookings.show',$b) }}" class="sd-lesson-row block no-underline text-inherit hover:bg-slate-50/80 -mx-2 px-2 rounded-xl">
                    <div class="sd-avatar">{{ mb_substr($b->instructor?->name ?? '?',0,1) }}</div>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-slate-800">{{ $b->instructor?->name }}</p>
                        <p class="text-xs text-slate-500">{{ $b->scheduled_at?->format('Y-m-d H:i') }} · {{ $b->statusLabel() }}</p>
                    </div>
                    <i class="fas fa-chevron-left text-slate-300 text-sm"></i>
                </a>
            @empty
                <p class="text-slate-500 text-sm text-center py-8">لا توجد حصص.</p>
            @endforelse
            <div class="mt-4">{{ $bookings->links() }}</div>
        </div>
    </div>
</div>
@endsection
