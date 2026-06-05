@extends('layouts.app')

@section('title', __('tutor.student_hub_title'))
@section('header', __('tutor.student_hub_title'))

@include('student.tutor-lessons.partials.dashboard-styles')

@section('content')
@php
    $user = auth()->user();
    $brandBlue = config('brand.colors.blue');
    $brandPurple = config('brand.colors.purple');
    $matchingLabel = \App\Models\StudentLearningProfile::matchingModeLabels()[$profile->matching_mode] ?? $profile->matching_mode;
    $remainingHours = max(0, (int) $profile->lesson_hours_quota - (int) $profile->lesson_hours_used);
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
                        ملفك الدراسي، حجز المعلمين، ومتابعة الحصص وساعات الباقة — بنفس تجربة لوحة التحكم.
                    </p>
                    <div class="flex flex-wrap gap-2 mt-4">
                        <a href="{{ route('student.tutor-lessons.profile') }}" class="sd-btn-outline">
                            <i class="fas fa-id-card"></i> ملفي الدراسي
                        </a>
                        @if($profile->matching_mode === 'assisted')
                            <a href="{{ route('student.tutor-lessons.assisted') }}" class="sd-btn-primary">
                                <i class="fas fa-hands-helping"></i> طلب مساعدة
                            </a>
                        @elseif($profile->matching_mode === 'self_schedule')
                            <a href="{{ route('student.tutor-lessons.schedule') }}" class="sd-btn-primary">
                                <i class="fas fa-calendar-plus"></i> {{ __('tutor.self_schedule_title') }}
                            </a>
                        @else
                            <a href="{{ route('student.tutor-lessons.teachers') }}" class="sd-btn-primary">
                                <i class="fas fa-user-graduate"></i> اختيار معلم
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="sd-motivation">
            <span class="w-11 h-11 rounded-xl bg-white/20 flex items-center justify-center text-xl">
                <i class="fas fa-chalkboard-user"></i>
            </span>
            <p class="font-bold text-sm leading-relaxed">نمط التوافق: {{ $matchingLabel }}</p>
            <a href="{{ route('student.tutor-lessons.profile') }}" class="text-xs font-bold text-white/90 hover:underline">
                تعديل التفضيلات →
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
                <p class="text-2xl font-black text-slate-800 tabular-nums">{{ (int) $profile->lesson_hours_used }}</p>
                <p class="text-xs font-bold text-slate-600 mt-0.5">ساعات مستهلكة</p>
            </div>
            <div class="sd-kpi">
                <div class="flex items-start justify-between gap-2 mb-3">
                    <span class="sd-kpi-icon" style="background:linear-gradient(135deg,#10b981,#059669)"><i class="fas fa-clock"></i></span>
                </div>
                <p class="text-2xl font-black text-slate-800 tabular-nums">{{ $remainingHours }}</p>
                <p class="text-xs font-bold text-slate-600 mt-0.5">ساعات متبقية</p>
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
                                {{ $b->scheduled_at?->timezone(config('app.timezone'))->format('Y-m-d H:i') }}
                                · <span class="sd-badge sd-badge-{{ $b->status === 'confirmed' ? 'confirmed' : 'pending' }}">{{ $b->statusLabel() }}</span>
                            </p>
                        </div>
                        <a href="{{ route('student.tutor-lessons.bookings.show', $b) }}" class="sd-btn-outline text-sm py-2">تفاصيل</a>
                    </div>
                @empty
                    <div class="text-center py-10 text-slate-500">
                        <i class="fas fa-calendar-plus text-3xl mb-3 opacity-40 block"></i>
                        <p class="text-sm">لا توجد حصص مجدولة بعد.</p>
                        @if($profile->matching_mode !== 'assisted')
                            <a href="{{ route('student.tutor-lessons.teachers') }}" class="sd-btn-primary mt-4 inline-flex">احجز حصتك الأولى</a>
                        @endif
                    </div>
                @endforelse
            </div>
        </div>

        <div class="sd-panel">
            <div class="sd-panel-head">
                <h2 class="font-heading font-bold text-slate-800">اختصارات</h2>
            </div>
            <div class="sd-panel-body space-y-2">
                <a href="{{ route('student.tutor-lessons.profile') }}" class="flex items-center gap-3 p-3 rounded-xl border border-slate-100 hover:border-purple-200 hover:bg-purple-50/50 transition no-underline text-inherit">
                    <span class="sd-kpi-icon !w-10 !h-10 text-sm" style="background:var(--sd-gradient)"><i class="fas fa-sliders"></i></span>
                    <span class="text-sm font-bold text-slate-700">المواد والمنهج ونمط التوافق</span>
                </a>
                @if($profile->matching_mode === 'assisted')
                <a href="{{ route('student.tutor-lessons.assisted') }}" class="flex items-center gap-3 p-3 rounded-xl border border-slate-100 hover:border-purple-200 hover:bg-purple-50/50 transition no-underline text-inherit">
                    <span class="sd-kpi-icon !w-10 !h-10 text-sm" style="background:linear-gradient(135deg,#0ea5e9,#06b6d4)"><i class="fas fa-comments"></i></span>
                    <span class="text-sm font-bold text-slate-700">تواصل عبر المنصة لإيجاد معلم</span>
                </a>
                @else
                <a href="{{ route('student.tutor-lessons.teachers') }}" class="flex items-center gap-3 p-3 rounded-xl border border-slate-100 hover:border-purple-200 hover:bg-purple-50/50 transition no-underline text-inherit">
                    <span class="sd-kpi-icon !w-10 !h-10 text-sm" style="background:linear-gradient(135deg,#10b981,#059669)"><i class="fas fa-search"></i></span>
                    <span class="text-sm font-bold text-slate-700">تصفح المعلمين واحجز</span>
                </a>
                @endif
                <a href="{{ url('/instructors?tutors=1') }}" class="flex items-center gap-3 p-3 rounded-xl border border-slate-100 hover:border-purple-200 hover:bg-purple-50/50 transition no-underline text-inherit">
                    <span class="sd-kpi-icon !w-10 !h-10 text-sm" style="background:linear-gradient(135deg,#f59e0b,#ea580c)"><i class="fas fa-globe"></i></span>
                    <span class="text-sm font-bold text-slate-700">دليل المعلمين العام</span>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
