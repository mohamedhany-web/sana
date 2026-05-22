@extends('layouts.app')

@section('title', __('student.dashboard_title'))

@push('styles')
<style>
    .sd-page { --sd-primary: #283593; --sd-accent: #FB5607; --sd-rose: #FFE5F7; }
    .sd-hero {
        display: grid;
        gap: 1rem;
        grid-template-columns: 1fr;
    }
    @media (min-width: 1024px) {
        .sd-hero { grid-template-columns: 1fr 280px; align-items: stretch; }
    }
    .sd-hero-main {
        position: relative;
        overflow: hidden;
        border-radius: 22px;
        border: 1px solid #e5e7eb;
        background: linear-gradient(135deg, rgba(255,229,247,.55) 0%, #fff 42%, #f8fafc 100%);
        padding: 1.5rem 1.75rem;
        box-shadow: 0 12px 40px -24px rgba(31,42,122,.2);
    }
    html.dark .sd-hero-main {
        background: linear-gradient(135deg, rgba(40,53,147,.25) 0%, rgba(30,41,59,.95) 55%);
        border-color: #334155;
    }
    .sd-hero-main::after {
        content: '';
        position: absolute;
        width: 200px;
        height: 200px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(251,86,7,.12), transparent 70%);
        top: -60px;
        left: -40px;
        pointer-events: none;
    }
    .sd-motivation {
        border-radius: 22px;
        border: 1px solid #e5e7eb;
        background: #fff;
        padding: 1.25rem;
        display: flex;
        flex-direction: column;
        justify-content: center;
        gap: 0.75rem;
        box-shadow: 0 8px 28px -16px rgba(31,42,122,.15);
    }
    html.dark .sd-motivation { background: rgba(30,41,59,.85); border-color: #334155; }
    .sd-kpi {
        border-radius: 18px;
        border: 1px solid #e5e7eb;
        background: #fff;
        padding: 1.1rem 1.2rem;
        transition: transform .2s, box-shadow .2s;
    }
    .sd-kpi:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 32px -18px rgba(31,42,122,.22);
    }
    html.dark .sd-kpi { background: rgba(30,41,59,.8); border-color: #334155; }
    .sd-kpi-icon {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        color: #fff;
    }
    .sd-panel {
        border-radius: 20px;
        border: 1px solid #e5e7eb;
        background: #fff;
        overflow: hidden;
        box-shadow: 0 8px 28px -18px rgba(15,23,42,.08);
    }
    html.dark .sd-panel { background: rgba(30,41,59,.85); border-color: #334155; }
    .sd-panel-head {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
    }
    html.dark .sd-panel-head { border-bottom-color: #334155; }
    .sd-courses-wrap { position: relative; padding: 0 0 1rem; }
    .sd-courses-track {
        display: flex;
        gap: 1rem;
        overflow-x: auto;
        scroll-snap-type: x mandatory;
        padding: 0 1.25rem 0.5rem;
        scrollbar-width: thin;
        -webkit-overflow-scrolling: touch;
    }
    .sd-courses-track::-webkit-scrollbar { height: 6px; }
    .sd-course-card {
        flex: 0 0 min(300px, 88vw);
        scroll-snap-align: start;
        border-radius: 18px;
        border: 1px solid #e5e7eb;
        background: #fff;
        padding: 1.1rem;
        transition: border-color .2s, box-shadow .2s;
    }
    .sd-course-card:hover {
        border-color: rgba(40,53,147,.25);
        box-shadow: 0 14px 36px -20px rgba(31,42,122,.25);
    }
    html.dark .sd-course-card { background: rgba(15,23,42,.4); border-color: #475569; }
    .sd-course-icon {
        width: 3rem;
        height: 3rem;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        color: #fff;
        margin-bottom: 0.85rem;
    }
    .sd-progress {
        height: 7px;
        background: #e2e8f0;
        border-radius: 999px;
        overflow: hidden;
    }
    html.dark .sd-progress { background: #334155; }
    .sd-progress > span {
        display: block;
        height: 100%;
        border-radius: 999px;
        background: linear-gradient(90deg, var(--sd-primary), var(--sd-accent));
    }
    .sd-scroll-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 2.25rem;
        height: 2.25rem;
        border-radius: 50%;
        border: 1px solid #e2e8f0;
        background: #fff;
        color: var(--sd-primary);
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 14px rgba(0,0,0,.08);
        z-index: 2;
    }
    html.dark .sd-scroll-btn { background: #1e293b; border-color: #475569; color: #c7d2fe; }
    .sd-scroll-btn--prev { right: 0.35rem; }
    .sd-scroll-btn--next { left: 0.35rem; }
    .sd-event {
        display: flex;
        gap: 0.75rem;
        padding: 0.85rem 1rem;
        border-radius: 14px;
        border: 1px solid #f1f5f9;
        transition: background .2s;
    }
    .sd-event:hover { background: #f8fafc; }
    html.dark .sd-event { border-color: #334155; }
    html.dark .sd-event:hover { background: rgba(51,65,85,.35); }
    .sd-event-dot {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 10px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 0.8rem;
    }
    .sd-activity-item {
        display: flex;
        gap: 0.75rem;
        padding: 0.75rem 0;
        border-bottom: 1px solid #f1f5f9;
    }
    .sd-activity-item:last-child { border-bottom: none; }
    html.dark .sd-activity-item { border-bottom-color: #334155; }
    .sd-activity-icon {
        width: 2.25rem;
        height: 2.25rem;
        border-radius: 10px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .sd-activity-icon--emerald { background: #ecfdf5; color: #059669; }
    .sd-activity-icon--amber { background: #fffbeb; color: #d97706; }
    .sd-activity-icon--violet { background: #ede9fe; color: #7c3aed; }
    html.dark .sd-activity-icon--emerald { background: rgba(16,185,129,.15); color: #6ee7b7; }
    html.dark .sd-activity-icon--amber { background: rgba(245,158,11,.15); color: #fcd34d; }
    html.dark .sd-activity-icon--violet { background: rgba(139,92,246,.15); color: #c4b5fd; }
    .sd-btn-primary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
        padding: 0.55rem 1rem;
        border-radius: 12px;
        background: var(--sd-primary);
        color: #fff;
        font-size: 0.8125rem;
        font-weight: 700;
        transition: background .2s;
    }
    .sd-btn-primary:hover { background: #1f2a7a; color: #fff; }
    .sd-ring { width: 88px; height: 88px; flex-shrink: 0; }
</style>
@endpush

@section('content')
@php
    $user = auth()->user();
    $progress = min((int) $stats['total_progress'], 100);
    $circumference = 2 * 3.14159 * 36;
    $strokeDashoffset = $circumference - ($progress / 100) * $circumference;
    $courseGradients = [
        ['from' => '#283593', 'to' => '#6366f1', 'icon' => 'fa-book-open'],
        ['from' => '#0ea5e9', 'to' => '#06b6d4', 'icon' => 'fa-code'],
        ['from' => '#FB5607', 'to' => '#f59e0b', 'icon' => 'fa-palette'],
        ['from' => '#8b5cf6', 'to' => '#a855f7', 'icon' => 'fa-graduation-cap'],
    ];
    $levelLabels = [
        'beginner' => 'مبتدئ',
        'intermediate' => 'متوسط',
        'advanced' => 'متقدم',
    ];
@endphp

<div class="sd-page space-y-6 pb-8">
    @if(isset($activeSubscription) && $activeSubscription)
    <div class="rounded-2xl border border-[#f5c7e8] dark:border-indigo-900/50 bg-gradient-to-l from-[#FFE5F7]/80 to-white dark:from-slate-800/90 dark:to-slate-900/90 px-5 py-4 flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3 min-w-0">
            <span class="w-10 h-10 rounded-xl bg-[#283593] text-white flex items-center justify-center"><i class="fas fa-gem"></i></span>
            <div>
                <p class="font-bold text-slate-800 dark:text-white">{{ $activeSubscription->plan_name }}</p>
                <p class="text-xs text-slate-500 dark:text-slate-400">
                    {{ \App\Models\Subscription::getDurationLabel($activeSubscription->billing_cycle) }}
                    · ينتهي {{ $activeSubscription->end_date?->format('Y-m-d') ?? '—' }}
                </p>
            </div>
        </div>
        <a href="{{ route('student.my-subscription') }}" class="sd-btn-primary text-sm">{{ __('student.view_all') }}</a>
    </div>
    @endif

    {{-- Hero --}}
    <div class="sd-hero">
        <div class="sd-hero-main relative z-[1]">
            <div class="flex flex-col sm:flex-row sm:items-center gap-5 justify-between">
                <div class="min-w-0">
                    <p class="text-xs font-bold text-[#283593] dark:text-indigo-300 mb-2">{{ __('student.dashboard_overview_today') }}</p>
                    <h1 class="font-heading text-2xl sm:text-3xl font-black text-slate-800 dark:text-white leading-tight">
                        {{ __('student.welcome_name', ['name' => $user->name]) }}
                    </h1>
                    <p class="text-slate-600 dark:text-slate-400 text-sm mt-2 max-w-lg leading-relaxed">{{ __('student.dashboard_subtitle') }}</p>
                </div>
                <div class="flex items-center gap-4 flex-shrink-0">
                    <div class="sd-ring relative">
                        <svg class="w-full h-full" viewBox="0 0 80 80" style="transform:rotate(-90deg)">
                            <circle cx="40" cy="40" r="36" fill="none" class="stroke-slate-200 dark:stroke-slate-600" stroke-width="6"/>
                            <circle cx="40" cy="40" r="36" fill="none" stroke="url(#sdProg)" stroke-width="6" stroke-linecap="round"
                                stroke-dasharray="{{ $circumference }}" stroke-dashoffset="{{ $strokeDashoffset }}"/>
                            <defs>
                                <linearGradient id="sdProg" x1="0%" y1="0%" x2="100%" y2="0%">
                                    <stop offset="0%" stop-color="#283593"/>
                                    <stop offset="100%" stop-color="#FB5607"/>
                                </linearGradient>
                            </defs>
                        </svg>
                        <span class="absolute inset-0 flex items-center justify-center font-black text-lg text-[#283593] dark:text-indigo-300">{{ $progress }}%</span>
                    </div>
                    <div class="hidden sm:block text-right">
                        <p class="text-sm font-bold text-slate-700 dark:text-slate-200">{{ __('student.dashboard_general_progress') }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">{{ __('student.from_course_completion') }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="sd-motivation">
            <span class="w-11 h-11 rounded-xl bg-[#FFE5F7] dark:bg-indigo-950/50 text-[#283593] dark:text-indigo-300 flex items-center justify-center text-xl">
                <i class="fas fa-rocket"></i>
            </span>
            <p class="font-bold text-slate-800 dark:text-white text-sm leading-relaxed">{{ __('student.dashboard_motivation') }}</p>
            @hasPermission('student.view.courses')
            <a href="{{ route('public.courses') }}" class="text-xs font-bold text-[#283593] dark:text-indigo-400 hover:underline">{{ __('student.explore_courses') }} →</a>
            @endhasPermission
        </div>
    </div>

    {{-- KPI --}}
    <div>
        <h2 class="text-sm font-bold text-slate-700 dark:text-slate-300 mb-3">{{ __('student.dashboard_progress_summary') }}</h2>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="sd-kpi">
                <div class="flex items-start justify-between gap-2 mb-3">
                    <span class="sd-kpi-icon" style="background:linear-gradient(135deg,#3b82f6,#2563eb)"><i class="fas fa-clock"></i></span>
                </div>
                <p class="text-2xl font-black text-slate-800 dark:text-white tabular-nums">{{ (int) $stats['total_learning_hours'] }}</p>
                <p class="text-xs font-bold text-slate-600 dark:text-slate-300 mt-0.5">{{ __('student.dashboard_learning_hours') }}</p>
                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">{{ __('student.dashboard_learning_hours_hint') }}</p>
            </div>
            <a href="{{ route('student.certificates.index') }}" class="sd-kpi block no-underline text-inherit">
                <div class="flex items-start justify-between gap-2 mb-3">
                    <span class="sd-kpi-icon" style="background:linear-gradient(135deg,#10b981,#059669)"><i class="fas fa-check-circle"></i></span>
                </div>
                <p class="text-2xl font-black text-slate-800 dark:text-white tabular-nums">{{ $stats['completed_courses'] }}</p>
                <p class="text-xs font-bold text-slate-600 dark:text-slate-300 mt-0.5">{{ __('student.dashboard_completed_this_month') }}</p>
            </a>
            <a href="{{ route('student.exams.index') }}" class="sd-kpi block no-underline text-inherit">
                <div class="flex items-start justify-between gap-2 mb-3">
                    <span class="sd-kpi-icon" style="background:linear-gradient(135deg,#8b5cf6,#7c3aed)"><i class="fas fa-clipboard-check"></i></span>
                </div>
                <p class="text-2xl font-black text-slate-800 dark:text-white tabular-nums">
                    {{ $stats['average_score'] > 0 ? number_format($stats['average_score'], 0).'%' : '—' }}
                </p>
                <p class="text-xs font-bold text-slate-600 dark:text-slate-300 mt-0.5">{{ __('student.dashboard_exam_average') }}</p>
                @if($stats['completed_exams'] > 0)
                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">{{ __('student.dashboard_exams_taken', ['count' => $stats['completed_exams']]) }}</p>
                @endif
            </a>
            @if(Route::has('student.achievements.index'))
            <a href="{{ route('student.achievements.index') }}" class="sd-kpi block no-underline text-inherit">
                <div class="flex items-start justify-between gap-2 mb-3">
                    <span class="sd-kpi-icon" style="background:linear-gradient(135deg,#FB5607,#ea580c)"><i class="fas fa-medal"></i></span>
                </div>
                <p class="text-2xl font-black text-slate-800 dark:text-white tabular-nums">{{ $achievementsCount }}</p>
                <p class="text-xs font-bold text-slate-600 dark:text-slate-300 mt-0.5">{{ __('student.dashboard_achievements') }}</p>
                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">{{ __('student.dashboard_achievements_hint') }}</p>
            </a>
            @else
            <div class="sd-kpi">
                <div class="flex items-start justify-between gap-2 mb-3">
                    <span class="sd-kpi-icon" style="background:linear-gradient(135deg,#FB5607,#ea580c)"><i class="fas fa-medal"></i></span>
                </div>
                <p class="text-2xl font-black text-slate-800 dark:text-white tabular-nums">{{ $achievementsCount }}</p>
                <p class="text-xs font-bold text-slate-600 dark:text-slate-300 mt-0.5">{{ __('student.dashboard_achievements') }}</p>
            </div>
            @endif
        </div>
    </div>

    {{-- Courses carousel --}}
    <div class="sd-panel">
        <div class="sd-panel-head">
            <h2 class="font-heading font-bold text-slate-800 dark:text-white">{{ __('student.dashboard_current_courses') }}</h2>
            <a href="{{ route('my-courses.index') }}" class="text-sm font-bold text-[#283593] dark:text-indigo-400 hover:underline">
                {{ __('student.view_all') }} <i class="fas fa-arrow-left text-[10px]"></i>
            </a>
        </div>
        @if($activeCourses->isNotEmpty())
        <div class="sd-courses-wrap">
            <button type="button" class="sd-scroll-btn sd-scroll-btn--prev hidden sm:flex" onclick="sdScrollCourses(1)" aria-label="السابق">
                <i class="fas fa-chevron-right"></i>
            </button>
            <button type="button" class="sd-scroll-btn sd-scroll-btn--next hidden sm:flex" onclick="sdScrollCourses(-1)" aria-label="التالي">
                <i class="fas fa-chevron-left"></i>
            </button>
            <div id="sd-courses-track" class="sd-courses-track" dir="ltr">
                @foreach($activeCourses as $index => $course)
                    @php
                        $prog = (float) ($course->pivot->progress ?? optional($course->enrollment ?? null)->progress ?? 0);
                        $g = $courseGradients[$index % count($courseGradients)];
                        $lessonsCount = (int) ($course->lessons_count ?? 0);
                        $levelKey = strtolower((string) ($course->level ?? ''));
                        $levelLabel = $levelLabels[$levelKey] ?? ($course->level ?: __('student.not_specified'));
                    @endphp
                    <article class="sd-course-card" dir="rtl">
                        <div class="sd-course-icon" style="background:linear-gradient(135deg,{{ $g['from'] }},{{ $g['to'] }})">
                            <i class="fas {{ $g['icon'] }}"></i>
                        </div>
                        <h3 class="font-bold text-slate-800 dark:text-white text-sm leading-snug mb-2 line-clamp-2 min-h-[2.5rem]">{{ $course->title }}</h3>
                        <div class="flex flex-wrap gap-2 text-[11px] text-slate-500 dark:text-slate-400 mb-3">
                            @if($lessonsCount > 0)
                            <span><i class="fas fa-layer-group ms-1 opacity-60"></i>{{ __('student.dashboard_lessons_count', ['count' => $lessonsCount]) }}</span>
                            @endif
                            <span><i class="fas fa-signal ms-1 opacity-60"></i>{{ $levelLabel }}</span>
                        </div>
                        <div class="flex items-center gap-2 mb-3">
                            <div class="sd-progress flex-1"><span style="width:{{ $prog }}%"></span></div>
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-200 tabular-nums">{{ $prog }}%</span>
                        </div>
                        <a href="{{ route('my-courses.show', $course->id) }}" class="sd-btn-primary w-full">
                            <i class="fas fa-play text-[10px]"></i>
                            {{ __('student.dashboard_continue') }}
                        </a>
                    </article>
                @endforeach
            </div>
        </div>
        @else
        <div class="text-center py-14 px-4">
            <div class="w-16 h-16 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-book-open text-2xl text-slate-300"></i>
            </div>
            <p class="font-bold text-slate-700 dark:text-slate-200">{{ __('student.no_active_courses') }}</p>
            <p class="text-sm text-slate-500 mt-1 mb-4">{{ __('student.start_journey_now') }}</p>
            <a href="{{ route('public.courses') }}" class="sd-btn-primary">{{ __('student.explore_courses') }}</a>
        </div>
        @endif
    </div>

    {{-- Calendar + Activity --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <div class="lg:col-span-5 sd-panel min-w-0">
            <div class="sd-panel-head">
                <h2 class="font-heading font-bold text-slate-800 dark:text-white">{{ __('student.dashboard_calendar') }}</h2>
                @if(Route::has('calendar'))
                <a href="{{ route('calendar') }}" class="text-xs font-bold text-[#283593] dark:text-indigo-400">{{ __('student.view_all') }}</a>
                @endif
            </div>
            <div class="p-3 space-y-2">
                @forelse($upcomingCalendar as $event)
                    @php $at = $event['at']; @endphp
                    @if($event['url'])
                    <a href="{{ $event['url'] }}" class="sd-event block no-underline text-inherit">
                    @else
                    <div class="sd-event">
                    @endif
                        <span class="sd-event-dot" style="background:{{ $event['color'] }}"><i class="fas {{ $event['icon'] }}"></i></span>
                        <div class="min-w-0 flex-1">
                            <p class="font-bold text-sm text-slate-800 dark:text-slate-100 truncate">{{ $event['title'] }}</p>
                            @if($event['subtitle'])
                            <p class="text-xs text-slate-500 dark:text-slate-400 truncate">{{ $event['subtitle'] }}</p>
                            @endif
                            <p class="text-[11px] font-semibold text-[#283593] dark:text-indigo-300 mt-1">
                                {{ $event['type_label'] }} · {{ $dashboardService->humanEventTime($at) }}
                            </p>
                        </div>
                    @if($event['url'])
                    </a>
                    @else
                    </div>
                    @endif
                @empty
                    <p class="text-center text-sm text-slate-500 py-10">{{ __('student.dashboard_no_events') }}</p>
                @endforelse
            </div>
            @if(Route::has('calendar'))
            <div class="px-4 pb-4">
                <a href="{{ route('calendar') }}" class="sd-btn-primary w-full text-center">{{ __('student.dashboard_view_calendar') }}</a>
            </div>
            @endif
        </div>

        <div class="lg:col-span-7 sd-panel min-w-0">
            <div class="sd-panel-head">
                <h2 class="font-heading font-bold text-slate-800 dark:text-white">{{ __('student.dashboard_recent_activity') }}</h2>
            </div>
            <div class="px-4 py-2">
                @forelse($recentActivity as $activity)
                    @if(!empty($activity['url']))
                    <a href="{{ $activity['url'] }}" class="sd-activity-item block no-underline text-inherit hover:bg-slate-50/80 dark:hover:bg-slate-800/40 rounded-xl px-2 -mx-2">
                    @else
                    <div class="sd-activity-item">
                    @endif
                        <span class="sd-activity-icon sd-activity-icon--{{ $activity['tone'] }}"><i class="fas {{ $activity['icon'] }} text-sm"></i></span>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-semibold text-slate-800 dark:text-slate-100 leading-relaxed">{{ $activity['text'] }}</p>
                            <div class="flex items-center gap-2 mt-1 flex-wrap">
                                <span class="text-[11px] text-slate-500 dark:text-slate-400">{{ $dashboardService->humanActivityTime($activity['at']) }}</span>
                                @if(!empty($activity['meta']))
                                <span class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400">{{ $activity['meta'] }}</span>
                                @endif
                            </div>
                        </div>
                    @if(!empty($activity['url']))
                    </a>
                    @else
                    </div>
                    @endif
                @empty
                    <p class="text-center text-sm text-slate-500 py-12">{{ __('student.dashboard_no_activity') }}</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function sdScrollCourses(direction) {
    var track = document.getElementById('sd-courses-track');
    if (!track) return;
    track.scrollBy({ left: direction * 320, behavior: 'smooth' });
}
</script>
@endpush
@endsection
