@extends('layouts.app')

@section('title', __('tutor.hub_title'))
@section('header', __('tutor.hub_title'))

@include('instructor.tutor-lessons.partials.dashboard-styles')

@section('content')
@php
    $user = auth()->user();
    $isActivated = $profile?->isTutorActivated();
@endphp

<div class="id-tutor-page space-y-6 pb-6">
    <section class="id-hero">
        <div class="id-hero-main relative z-[1]">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div class="flex items-start gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-[#283593] to-[#FB5607] text-white flex items-center justify-center flex-shrink-0 shadow-lg shadow-indigo-900/20">
                        <i class="fas fa-user-clock text-2xl"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-bold id-tag uppercase tracking-wider mb-1">{{ __('tutor.hub_title') }}</p>
                        <h2 class="text-xl sm:text-2xl font-black text-slate-900 leading-tight m-0">
                            {{ __('instructor.welcome') }}، {{ $user->name }}
                        </h2>
                        <p class="text-sm text-slate-600 mt-1 mb-0">{{ __('tutor.hub_subtitle') }}</p>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-2 relative z-[1]">
                    <a href="{{ route('instructor.tutor-lessons.setup') }}" class="id-btn-ghost">
                        <i class="fas fa-sliders text-xs"></i>
                        {{ __('tutor.complete_profile') }}
                    </a>
                    <a href="{{ route('instructor.tutor-lessons.bookings.index') }}" class="id-btn-primary">
                        <i class="fas fa-calendar-check text-xs"></i>
                        الحجوزات
                        @if($pendingCount > 0)
                            <span class="bg-white/20 px-2 py-0.5 rounded-full text-[10px]">{{ $pendingCount }}</span>
                        @endif
                    </a>
                </div>
            </div>
        </div>
        <aside class="id-hero-aside">
            <p class="text-xs font-bold uppercase tracking-wider m-0 opacity-90">حالة الحساب</p>
            <p class="text-lg font-black m-0">{{ $isActivated ? 'مفعّل للحجز' : 'قيد التفعيل' }}</p>
            @if(!$isActivated)
                <a href="{{ route('instructor.tutor-lessons.setup') }}" class="text-xs font-bold text-white/90 hover:underline">أكمل الجلسة التجريبية ←</a>
            @else
                <a href="{{ route('instructor.tutor-lessons.bookings.index') }}" class="text-xs font-bold text-white/90 hover:underline">عرض الحجوزات ←</a>
            @endif
        </aside>
    </section>

    <div>
        <h3 class="text-sm font-bold text-slate-700 mb-3">ملخص اليوم والأسبوع</h3>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="id-kpi">
                <span class="id-kpi-icon" style="background:linear-gradient(135deg,#283593,#4338ca)"><i class="fas fa-clock"></i></span>
                <p class="text-2xl font-black text-slate-900 tabular-nums m-0">{{ $todayMinutes }}</p>
                <p class="text-xs font-bold text-slate-600 mt-0.5 mb-0">دقيقة اليوم</p>
            </div>
            <div class="id-kpi">
                <span class="id-kpi-icon" style="background:linear-gradient(135deg,#FB5607,#ea580c)"><i class="fas fa-calendar-week"></i></span>
                <p class="text-2xl font-black text-slate-900 tabular-nums m-0">{{ $weekMinutes }}</p>
                <p class="text-xs font-bold text-slate-600 mt-0.5 mb-0">دقيقة هذا الأسبوع</p>
            </div>
            <div class="id-kpi">
                <span class="id-kpi-icon" style="background:linear-gradient(135deg,#0ea5e9,#0284c7)"><i class="fas fa-table"></i></span>
                <p class="text-2xl font-black text-slate-900 tabular-nums m-0">{{ $availabilities->count() }}</p>
                <p class="text-xs font-bold text-slate-600 mt-0.5 mb-0">فترات أسبوعية</p>
            </div>
            <a href="{{ route('instructor.tutor-lessons.bookings.index') }}" class="id-kpi">
                <span class="id-kpi-icon" style="background:linear-gradient(135deg,#10b981,#059669)"><i class="fas fa-hourglass-half"></i></span>
                <p class="text-2xl font-black text-slate-900 tabular-nums m-0">{{ $pendingCount }}</p>
                <p class="text-xs font-bold text-slate-600 mt-0.5 mb-0">طلبات بانتظار التأكيد</p>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="id-panel xl:col-span-2">
            <div class="id-panel-head">
                <h3 class="font-bold text-slate-800 m-0">الحصص القادمة</h3>
                <a href="{{ route('instructor.tutor-lessons.bookings.index') }}" class="id-link">
                    {{ __('instructor.view_all') ?? 'عرض الكل' }} <i class="fas fa-arrow-left text-[10px]"></i>
                </a>
            </div>
            <div class="id-panel-body">
                @forelse($upcoming as $b)
                    <div class="id-row">
                        <div class="id-avatar">{{ mb_substr($b->student?->name ?? '?', 0, 1) }}</div>
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-slate-800 truncate m-0">{{ $b->student?->name }}</p>
                            <p class="text-xs text-slate-500 m-0 mt-0.5">
                                {{ $b->scheduled_at?->timezone(config('app.timezone'))->format('Y-m-d H:i') }}
                                · <span class="id-badge id-badge-{{ $b->status === 'confirmed' ? 'confirmed' : 'pending' }}">{{ $b->statusLabel() }}</span>
                                @if($b->is_trial)<span class="id-badge id-badge-pending">تجريبي</span>@endif
                            </p>
                        </div>
                        <a href="{{ route('instructor.tutor-lessons.bookings.show', $b) }}" class="id-btn-ghost text-sm py-2">عرض</a>
                    </div>
                @empty
                    <div class="text-center py-10 text-slate-500">
                        <i class="fas fa-calendar-plus text-3xl mb-3 opacity-40 block"></i>
                        <p class="text-sm m-0">لا توجد حصص قادمة.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="space-y-4">
            <div class="id-panel">
                <div class="id-panel-head">
                    <h3 class="font-bold text-slate-800 m-0">جدولك الأسبوعي</h3>
                    <a href="{{ route('instructor.tutor-lessons.setup') }}" class="id-link text-xs">تعديل</a>
                </div>
                <div class="id-panel-body">
                    @forelse($availabilities as $a)
                        <div class="text-sm py-2 border-b border-slate-100 last:border-0 flex justify-between gap-2">
                            <span class="font-semibold text-slate-700">{{ $a->dayLabel() }}</span>
                            <span class="text-slate-500 tabular-nums">{{ \Carbon\Carbon::parse($a->start_time)->format('H:i') }} — {{ \Carbon\Carbon::parse($a->end_time)->format('H:i') }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500 mb-3">أضف أوقات التفرغ من إعداد الملف.</p>
                        <a href="{{ route('instructor.tutor-lessons.setup') }}" class="id-btn-primary w-full justify-center">إعداد الجدول</a>
                    @endforelse
                </div>
            </div>

            <div class="id-panel">
                <div class="id-panel-head">
                    <h3 class="font-bold text-slate-800 m-0">{{ __('instructor.quick_actions') }}</h3>
                </div>
                <div class="id-panel-body space-y-2">
                    <a href="{{ route('instructor.tutor-lessons.work-log') }}" class="id-quick">
                        <span class="id-kpi-icon !w-10 !h-10 !mb-0 text-sm" style="background:linear-gradient(135deg,#283593,#FB5607)"><i class="fas fa-clipboard-list"></i></span>
                        <span class="text-sm font-bold text-slate-700">سجل ساعات العمل</span>
                    </a>
                    <a href="{{ route('instructor.tutor-lessons.setup') }}" class="id-quick">
                        <span class="id-kpi-icon !w-10 !h-10 !mb-0 text-sm" style="background:linear-gradient(135deg,#8b5cf6,#6d28d9)"><i class="fas fa-id-card"></i></span>
                        <span class="text-sm font-bold text-slate-700">ملف المعلم والمواد</span>
                    </a>
                    @if(Route::has('instructor.classroom.index'))
                    <a href="{{ route('instructor.classroom.index') }}" class="id-quick">
                        <span class="id-kpi-icon !w-10 !h-10 !mb-0 text-sm" style="background:linear-gradient(135deg,#0ea5e9,#06b6d4)"><i class="fas fa-chalkboard"></i></span>
                        <span class="text-sm font-bold text-slate-700">{{ __('platform.classroom') }}</span>
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
