@extends('layouts.app')
@section('title', __('tutor.my_lessons'))
@section('header', __('tutor.my_lessons'))
@include('instructor.tutor-lessons.partials.dashboard-styles')
@section('content')
<div class="id-tutor-page space-y-6 pb-6 w-full">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-black text-slate-900 m-0">{{ __('tutor.my_lessons') }}</h2>
            <p class="text-sm text-slate-500 mt-1 mb-0">كل طلبات الحجز والحصص المؤكدة</p>
        </div>
        <a href="{{ route('instructor.tutor-lessons.hub') }}" class="id-btn-ghost"><i class="fas fa-arrow-right text-xs"></i> {{ __('tutor.hub_title') }}</a>
    </div>
    <div class="id-panel">
        <div class="id-panel-body">
            @forelse($bookings as $b)
                <a href="{{ route('instructor.tutor-lessons.bookings.show', $b) }}" class="id-row block">
                    <div class="id-avatar">{{ mb_substr($b->student?->name ?? '?', 0, 1) }}</div>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-slate-800 m-0 truncate">{{ $b->student?->name }} @if($b->is_trial)<span class="id-badge id-badge-pending">تجريبي</span>@endif</p>
                        <p class="text-xs text-slate-500 m-0">{{ $b->scheduled_at?->format('Y-m-d H:i') }} · {{ $b->statusLabel() }}</p>
                    </div>
                    <i class="fas fa-chevron-left text-slate-300 text-sm"></i>
                </a>
            @empty
                <p class="text-center text-slate-500 py-10 text-sm">لا توجد حجوزات.</p>
            @endforelse
            <div class="mt-4">{{ $bookings->links() }}</div>
        </div>
    </div>
</div>
@endsection
