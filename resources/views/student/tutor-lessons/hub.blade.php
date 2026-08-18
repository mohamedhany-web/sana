@extends('layouts.app')

@section('title', __('tutor.student_hub_title'))
@section('header', __('tutor.student_hub_title'))

@include('student.tutor-lessons.partials.dashboard-styles')

@section('content')
@php
    $user = auth()->user();
    $brandBlue = config('brand.colors.blue');
    $brandPurple = config('brand.colors.purple');
    $remainingHours = $profile->remainingHours();
    $remainingLabel = $profile->remainingLabel();
    $usedLabel = $profile->usedLabel();
@endphp

<div class="sd-page space-y-6 pb-8 w-full">
    {{-- Hero --}}
    <div class="sd-hero">
        <div class="sd-hero-main relative z-[1]">
            <div class="flex flex-col lg:flex-row lg:items-center gap-5 justify-between">
                <div class="min-w-0">
                    <p class="text-xs font-bold sd-tag mb-2">حصص مع المعلمين</p>
                    <h1 class="font-heading text-2xl sm:text-3xl font-black text-slate-800 leading-tight">
                        {{ __('tutor.student_hub_title') }}
                    </h1>
                    <p class="text-slate-600 text-sm mt-2 max-w-2xl leading-relaxed">
                        اختر معلماً واحجز حصة، وتابع حصصك وساعات الباقة من هنا.
                    </p>
                    <div class="flex flex-wrap gap-2 mt-4">
                        <a href="{{ route('student.tutor-lessons.teachers') }}" class="sd-btn-primary">
                            <i class="fas fa-user-graduate"></i> اختيار معلم والحجز
                        </a>
                        <a href="{{ route('student.tutor-lessons.bookings.index') }}" class="sd-btn-outline">
                            <i class="fas fa-calendar-check"></i> حصصي
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="sd-motivation">
            <span class="w-11 h-11 rounded-xl bg-white/20 flex items-center justify-center text-xl">
                <i class="fas fa-chalkboard-user"></i>
            </span>
            <p class="font-bold text-sm leading-relaxed">فلتر المعلمين حسب المادة واحجز مباشرة</p>
            <a href="{{ route('student.tutor-lessons.teachers') }}" class="text-xs font-bold text-white/90 hover:underline">
                تصفح المعلمين →
            </a>
        </div>
    </div>

    {{-- KPI --}}
    <div>
        <h2 class="text-sm font-bold text-slate-700 mb-3">ملخص الباقة والحصص</h2>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="sd-kpi">
                <div class="flex items-start justify-between gap-2 mb-3">
                    <span class="sd-kpi-icon" style="background:linear-gradient(135deg,{{ $brandBlue }},#2563eb)"><i class="fas fa-box"></i></span>
                </div>
                <p class="text-2xl font-black text-slate-800 tabular-nums">{{ (int) $profile->lesson_hours_quota }}</p>
                <p class="text-xs font-bold text-slate-600 mt-0.5">ساعات الباقة</p>
            </div>
            <div class="sd-kpi">
                <div class="flex items-start justify-between gap-2 mb-3">
                    <span class="sd-kpi-icon" style="background:linear-gradient(135deg,#f59e0b,#ea580c)"><i class="fas fa-hourglass-half"></i></span>
                </div>
                <p class="text-2xl font-black text-slate-800 tabular-nums leading-snug">{{ $usedLabel }}</p>
                <p class="text-xs font-bold text-slate-600 mt-0.5">مستهلكة</p>
            </div>
            <div class="sd-kpi">
                <div class="flex items-start justify-between gap-2 mb-3">
                    <span class="sd-kpi-icon" style="background:linear-gradient(135deg,#10b981,#059669)"><i class="fas fa-clock"></i></span>
                </div>
                <p class="text-2xl font-black text-slate-800 tabular-nums leading-snug">{{ $remainingLabel }}</p>
                <p class="text-xs font-bold text-slate-600 mt-0.5">متبقية</p>
            </div>
            <a href="{{ route('student.tutor-lessons.bookings.index') }}" class="sd-kpi block no-underline text-inherit">
                <div class="flex items-start justify-between gap-2 mb-3">
                    <span class="sd-kpi-icon" style="background:linear-gradient(135deg,{{ $brandPurple }},#6d28d9)"><i class="fas fa-calendar-check"></i></span>
                </div>
                <p class="text-2xl font-black text-slate-800 tabular-nums">{{ $upcoming->count() }}</p>
                <p class="text-xs font-bold text-slate-600 mt-0.5">حصص قادمة</p>
                <p class="text-[11px] text-slate-500 mt-1 sd-link">عرض الكل →</p>
            </a>
        </div>
    </div>

  <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="sd-panel xl:col-span-2">
            <div class="sd-panel-head">
                <h2 class="font-heading font-bold text-slate-800">حصصك القادمة</h2>
                <a href="{{ route('student.tutor-lessons.bookings.index') }}" class="text-sm sd-link">
                    {{ __('student.view_all') ?? 'عرض الكل' }} <i class="fas fa-arrow-left text-[10px]"></i>
                </a>
            </div>
            <div class="sd-panel-body">
                @forelse($upcoming as $b)
                    <div class="sd-lesson-row">
                        <div class="sd-avatar">{{ mb_substr($b->instructor?->name ?? '?', 0, 1) }}</div>
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-slate-800 truncate">{{ $b->instructor?->name }}</p>
                            <p class="text-xs text-slate-500 mt-0.5">
                                {{ display_datetime($b->scheduled_at) }}
                                · <span class="sd-badge sd-badge-{{ $b->status === 'confirmed' ? 'confirmed' : 'pending' }}">{{ $b->statusLabel() }}</span>
                            </p>
                        </div>
                        <a href="{{ route('student.tutor-lessons.bookings.show', $b) }}" class="sd-btn-outline text-sm py-2">تفاصيل</a>
                        @if($b->isLiveJoinable() && $b->liveJoinUrl())
                            <a href="{{ $b->liveJoinUrl() }}" class="sd-btn-primary text-sm py-2">{{ $b->liveJoinLabel() }}</a>
                        @endif
                    </div>
                @empty
                    <div class="text-center py-10 text-slate-500">
                        <i class="fas fa-calendar-plus text-3xl mb-3 opacity-40 block"></i>
                        <p class="text-sm">لا توجد حصص مجدولة بعد.</p>
                        <a href="{{ route('student.tutor-lessons.teachers') }}" class="sd-btn-primary mt-4 inline-flex">احجز حصتك الأولى</a>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="sd-panel">
            <div class="sd-panel-head">
                <h2 class="font-heading font-bold text-slate-800">اختصارات</h2>
            </div>
            <div class="sd-panel-body space-y-2">
                <a href="{{ route('student.tutor-lessons.teachers') }}" class="flex items-center gap-3 p-3 rounded-xl border border-slate-100 hover:border-purple-200 hover:bg-purple-50/50 transition no-underline text-inherit">
                    <span class="sd-kpi-icon !w-10 !h-10 text-sm" style="background:linear-gradient(135deg,#10b981,#059669)"><i class="fas fa-search"></i></span>
                    <span class="text-sm font-bold text-slate-700">تصفح المعلمين واحجز</span>
                </a>
                <a href="{{ route('student.tutor-lessons.bookings.index') }}" class="flex items-center gap-3 p-3 rounded-xl border border-slate-100 hover:border-purple-200 hover:bg-purple-50/50 transition no-underline text-inherit">
                    <span class="sd-kpi-icon !w-10 !h-10 text-sm" style="background:linear-gradient(135deg,#8b5cf6,#6d28d9)"><i class="fas fa-calendar-check"></i></span>
                    <span class="text-sm font-bold text-slate-700">متابعة حجوزاتك</span>
                </a>
                <a href="{{ route('student.tutor-lessons.hours') }}" class="flex items-center gap-3 p-3 rounded-xl border border-slate-100 hover:border-purple-200 hover:bg-purple-50/50 transition no-underline text-inherit">
                    <span class="sd-kpi-icon !w-10 !h-10 text-sm" style="background:linear-gradient(135deg,#0ea5e9,#2563eb)"><i class="fas fa-clock"></i></span>
                    <span class="text-sm font-bold text-slate-700">ساعات الباقة وشراء رصيد إضافي</span>
                </a>
                <a href="{{ url('/instructors?tutors=1') }}" class="flex items-center gap-3 p-3 rounded-xl border border-slate-100 hover:border-purple-200 hover:bg-purple-50/50 transition no-underline text-inherit">
                    <span class="sd-kpi-icon !w-10 !h-10 text-sm" style="background:linear-gradient(135deg,#f59e0b,#ea580c)"><i class="fas fa-globe"></i></span>
                    <span class="text-sm font-bold text-slate-700">دليل المعلمين العام</span>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
