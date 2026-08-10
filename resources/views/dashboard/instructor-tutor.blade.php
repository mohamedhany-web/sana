@extends('layouts.app')

@section('title', __('tutor.hub_title'))
@section('header', __('tutor.hub_title'))

@include('instructor.tutor-lessons.partials.dashboard-styles')

@section('content')
@php
    $user = auth()->user();
@endphp

<div class="id-tutor-page space-y-6 pb-6">
    <section class="id-hero">
        <div class="id-hero-main relative z-[1]">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div class="min-w-0">
                    <p class="text-xs font-bold id-tag uppercase tracking-wider mb-1">{{ __('tutor.hub_title') }}</p>
                    <h2 class="text-xl sm:text-2xl font-black text-slate-900 leading-tight m-0">
                        {{ __('instructor.welcome') }}، {{ $user->name }}
                    </h2>
                    <p class="text-sm text-slate-600 mt-1 mb-0">ملخص حصصك مع الطلاب: الحجوزات القادمة والجدول وساعات العمل.</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    @if(Route::has('instructor.tutor-lessons.bookings.index'))
                    <a href="{{ route('instructor.tutor-lessons.bookings.index') }}" class="id-btn-primary">
                        <i class="fas fa-calendar-check text-xs"></i>
                        الحجوزات
                        @if(($stats['pending_bookings'] ?? 0) > 0)
                            <span class="bg-white/20 px-2 py-0.5 rounded-full text-[10px]">{{ $stats['pending_bookings'] }}</span>
                        @endif
                    </a>
                    @endif
                    @if(Route::has('instructor.tutor-lessons.setup'))
                    <a href="{{ route('instructor.tutor-lessons.setup') }}" class="id-btn-ghost">
                        <i class="fas fa-sliders text-xs"></i>
                        {{ __('tutor.complete_profile') }}
                    </a>
                    @endif
                </div>
            </div>
        </div>
        <aside class="id-hero-aside">
            <p class="text-xs font-bold uppercase tracking-wider m-0 opacity-90">حالة الحساب</p>
            <p class="text-lg font-black m-0">{{ !empty($stats['is_activated']) ? 'مفعّل للحجز' : 'قيد التفعيل' }}</p>
            @if(empty($stats['is_activated']) && Route::has('instructor.tutor-lessons.setup'))
                <a href="{{ route('instructor.tutor-lessons.setup') }}" class="text-xs font-bold text-white/90 hover:underline">أكمل الإعداد ←</a>
            @elseif(Route::has('dashboard'))
                <a href="{{ route('dashboard') }}" class="text-xs font-bold text-white/90 hover:underline">{{ __('instructor.dashboard') }} ←</a>
            @endif
        </aside>
    </section>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <a href="{{ route('instructor.tutor-lessons.bookings.index') }}" class="id-kpi">
            <p class="text-[11px] font-bold text-slate-500 m-0">بانتظار التأكيد</p>
            <p class="text-2xl font-black text-slate-900 m-0 mt-1 tabular-nums">{{ (int) ($stats['pending_bookings'] ?? 0) }}</p>
        </a>
        <div class="id-kpi">
            <p class="text-[11px] font-bold text-slate-500 m-0">حصص قادمة مؤكدة</p>
            <p class="text-2xl font-black text-slate-900 m-0 mt-1 tabular-nums">{{ (int) ($stats['confirmed_upcoming'] ?? 0) }}</p>
        </div>
        <div class="id-kpi">
            <p class="text-[11px] font-bold text-slate-500 m-0">دقائق اليوم</p>
            <p class="text-2xl font-black text-slate-900 m-0 mt-1 tabular-nums">{{ (int) ($stats['today_minutes'] ?? 0) }}</p>
        </div>
        <a href="{{ route('instructor.tutor-lessons.setup') }}" class="id-kpi">
            <p class="text-[11px] font-bold text-slate-500 m-0">أيام الجدول</p>
            <p class="text-2xl font-black text-slate-900 m-0 mt-1 tabular-nums">{{ (int) ($stats['availability_days'] ?? 0) }}</p>
        </a>
    </div>

    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex items-center justify-between gap-3 mb-4">
            <h3 class="text-base font-black text-slate-900 m-0">أقرب الحصص</h3>
            @if(Route::has('instructor.tutor-lessons.bookings.index'))
            <a href="{{ route('instructor.tutor-lessons.bookings.index') }}" class="text-xs font-bold text-[#283593] hover:underline">كل الحجوزات</a>
            @endif
        </div>

        @if($upcoming->isEmpty())
            <p class="text-sm text-slate-500 m-0">لا توجد حصص قادمة حالياً.</p>
        @else
            <ul class="divide-y divide-slate-100 m-0 p-0 list-none">
                @foreach($upcoming as $booking)
                    <li class="py-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                        <div class="min-w-0">
                            <p class="font-bold text-slate-900 m-0 truncate">{{ $booking->student?->name ?? 'طالب' }}</p>
                            <p class="text-xs text-slate-500 m-0 mt-0.5">
                                {{ optional($booking->scheduled_at)->locale('ar')->translatedFormat('l j F — H:i') }}
                                @if($booking->subject?->name) · {{ $booking->subject->name }} @endif
                                · {{ $booking->status === 'pending' ? 'بانتظار التأكيد' : 'مؤكدة' }}
                            </p>
                        </div>
                        @if(Route::has('instructor.tutor-lessons.bookings.show'))
                        <a href="{{ route('instructor.tutor-lessons.bookings.show', $booking) }}" class="text-xs font-bold text-[#283593] hover:underline whitespace-nowrap">عرض</a>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif
    </section>
</div>
@endsection
