@extends('layouts.app')

@section('title', __('instructor.dashboard_title'))
@section('header', __('instructor.dashboard'))

@push('styles')
<style>
    .id-page { --id-primary: #283593; --id-accent: #FB5607; --id-rose: #FFE5F7; }
    .id-hero {
        display: grid;
        gap: 1rem;
        grid-template-columns: 1fr;
    }
    @media (min-width: 1024px) {
        .id-hero { grid-template-columns: 1fr 260px; align-items: stretch; }
    }
    .id-hero-main {
        position: relative;
        overflow: hidden;
        border-radius: 22px;
        border: 1px solid #e5e7eb;
        background: linear-gradient(135deg, rgba(255,229,247,.6) 0%, #fff 40%, #f8fafc 100%);
        padding: 1.5rem 1.75rem;
        box-shadow: 0 12px 40px -24px rgba(31,42,122,.22);
    }
        background: linear-gradient(135deg, rgba(40,53,147,.28) 0%, rgba(30,41,59,.95) 55%);
        border-color: #334155;
    }
    .id-hero-main::after {
        content: '';
        position: absolute;
        width: 220px;
        height: 220px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(251,86,7,.14), transparent 70%);
        top: -70px;
        left: -50px;
        pointer-events: none;
    }
    .id-hero-aside {
        border-radius: 22px;
        border: 1px solid #e5e7eb;
        background: #fff;
        padding: 1.25rem;
        display: flex;
        flex-direction: column;
        justify-content: center;
        gap: 0.65rem;
        box-shadow: 0 8px 28px -16px rgba(31,42,122,.16);
    }
    .id-kpi {
        border-radius: 18px;
        border: 1px solid #e5e7eb;
        background: #fff;
        padding: 1.1rem 1.2rem;
        transition: transform .2s, box-shadow .2s, border-color .2s;
        text-decoration: none;
        color: inherit;
        display: block;
    }
    .id-kpi:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 32px -18px rgba(31,42,122,.25);
        border-color: rgba(40,53,147,.2);
    }
    .id-kpi-icon {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
    }
    .id-panel {
        border-radius: 20px;
        border: 1px solid #e5e7eb;
        background: #fff;
        overflow: hidden;
        box-shadow: 0 8px 28px -18px rgba(15,23,42,.08);
    }
    .id-panel-head {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
    }
    .id-row {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 0.85rem 1.25rem;
        border-bottom: 1px solid #f8fafc;
        transition: background .15s;
        text-decoration: none;
        color: inherit;
    }
    .id-row:last-child { border-bottom: none; }
    .id-row:hover { background: #f8fafc; }
    .id-quick {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 1rem 1.1rem;
        border-radius: 16px;
        border: 1px solid #e5e7eb;
        background: #fff;
        transition: all .2s;
        text-decoration: none;
        color: inherit;
    }
    .id-quick:hover {
        border-color: rgba(40,53,147,.22);
        box-shadow: 0 10px 28px -16px rgba(31,42,122,.22);
        transform: translateY(-2px);
    }
    .id-btn-primary {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.6rem 1.1rem;
        border-radius: 12px;
        background: #283593;
        color: #fff;
        font-size: 0.8125rem;
        font-weight: 700;
        transition: background .15s;
        text-decoration: none;
    }
    .id-btn-primary:hover { background: #1F2A7A; color: #fff; }
    .id-btn-ghost {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.6rem 1.1rem;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        background: #fff;
        color: #334155;
        font-size: 0.8125rem;
        font-weight: 700;
        transition: all .15s;
        text-decoration: none;
    }
    .id-btn-ghost:hover { border-color: #cbd5e1; background: #f8fafc; }
</style>
@endpush

@section('content')
<div class="id-page space-y-6 pb-6">
    <section class="id-hero">
        <div class="id-hero-main relative z-[1]">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div class="flex items-start gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-[#283593] to-[#FB5607] text-white flex items-center justify-center flex-shrink-0 shadow-lg shadow-indigo-900/20">
                        <i class="fas fa-chalkboard-teacher text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-[#283593] uppercase tracking-wider mb-1">{{ __('instructor.instructor_panel') }}</p>
                        <h2 class="text-xl sm:text-2xl font-black text-slate-900 leading-tight m-0">
                            {{ __('instructor.welcome') }}، {{ auth()->user()->name }}
                        </h2>
                        <p class="text-sm text-slate-600 mt-1 mb-0">{{ __('instructor.overview_activity_today') }}</p>
                        <p class="text-xs text-slate-500 mt-2 flex items-center gap-2 mb-0">
                            <i class="fas fa-calendar-day opacity-60"></i>
                            <time datetime="{{ now()->toIso8601String() }}">{{ now()->translatedFormat('l، d F Y') }}</time>
                        </p>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('instructor.lectures.create') }}" class="id-btn-primary">
                        <i class="fas fa-video text-xs"></i>
                        {{ __('instructor.add_lecture') }}
                    </a>
                    <a href="{{ route('instructor.assignments.create') }}" class="id-btn-ghost">
                        <i class="fas fa-tasks text-xs"></i>
                        {{ __('instructor.add_assignment') }}
                    </a>
                    @if(Route::has('instructor.classroom.index') && auth()->user()->hasSubscriptionFeature('classroom_access'))
                    <a href="{{ route('instructor.classroom.index') }}" class="id-btn-ghost">
                        <i class="fas fa-chalkboard-teacher text-xs"></i>
                        Classroom
                    </a>
                    @endif
                </div>
            </div>
        </div>
        <aside class="id-hero-aside">
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider m-0">{{ __('instructor.quick_actions') }}</p>
            @if($stats['pending_submissions'] > 0)
                <p class="text-sm font-bold text-amber-700 m-0">
                    <i class="fas fa-circle-exclamation ml-1"></i>
                    {{ $stats['pending_submissions'] }} {{ __('instructor.need_grading') }}
                </p>
                <a href="{{ route('instructor.assignments.index') }}" class="text-xs font-bold text-[#283593] hover:underline">{{ __('instructor.view_all') }} ←</a>
            @else
                <p class="text-sm text-emerald-700 font-semibold m-0">
                    <i class="fas fa-check-circle ml-1"></i>
                    {{ __('instructor.all_assignments_graded') }}
                </p>
            @endif
            @php $liveCount = \App\Models\LiveSession::where('instructor_id', auth()->id())->where('status', 'live')->count(); @endphp
            @if($liveCount > 0)
                <a href="{{ route('instructor.live-sessions.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-red-600 mt-1">
                    <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></span>
                    {{ $liveCount }} بث مباشر الآن
                </a>
            @endif
        </aside>
    </section>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <a href="{{ route('instructor.courses.index') }}" class="id-kpi">
            <div class="flex items-center justify-between mb-3">
                <span class="id-kpi-icon bg-[#eef2ff] text-[#283593]"><i class="fas fa-book"></i></span>
                <i class="fas fa-arrow-left text-slate-300 text-xs"></i>
            </div>
            <div class="text-2xl sm:text-3xl font-black text-slate-900 tabular-nums">{{ number_format($stats['my_courses']) }}</div>
            <div class="text-[11px] font-bold text-slate-500 mt-1 uppercase tracking-wide">{{ __('instructor.my_courses') }}</div>
        </a>
        <a href="{{ route('instructor.courses.index') }}" class="id-kpi">
            <div class="flex items-center justify-between mb-3">
                <span class="id-kpi-icon bg-emerald-100 text-emerald-700"><i class="fas fa-user-graduate"></i></span>
                <i class="fas fa-arrow-left text-slate-300 text-xs"></i>
            </div>
            <div class="text-2xl sm:text-3xl font-black text-slate-900 tabular-nums">{{ number_format($stats['total_students']) }}</div>
            <div class="text-[11px] font-bold text-slate-500 mt-1 uppercase tracking-wide">{{ __('instructor.total_students') }}</div>
        </a>
        <a href="{{ route('instructor.lectures.index') }}" class="id-kpi">
            <div class="flex items-center justify-between mb-3">
                <span class="id-kpi-icon bg-violet-100 text-violet-700"><i class="fas fa-chalkboard"></i></span>
                <i class="fas fa-arrow-left text-slate-300 text-xs"></i>
            </div>
            <div class="text-2xl sm:text-3xl font-black text-slate-900 tabular-nums">{{ number_format($stats['total_lectures']) }}</div>
            <div class="text-[11px] font-bold text-slate-500 mt-1 uppercase tracking-wide">
                {{ __('instructor.lectures') }}
                @if($stats['upcoming_lectures'] > 0)
                    <span class="text-violet-600 font-normal normal-case">· {{ $stats['upcoming_lectures'] }} {{ __('instructor.upcoming') }}</span>
                @endif
            </div>
        </a>
        <a href="{{ route('instructor.assignments.index') }}" class="id-kpi">
            <div class="flex items-center justify-between mb-3">
                <span class="id-kpi-icon bg-amber-100 text-amber-700"><i class="fas fa-tasks"></i></span>
                <i class="fas fa-arrow-left text-slate-300 text-xs"></i>
            </div>
            <div class="text-2xl sm:text-3xl font-black text-slate-900 tabular-nums">{{ number_format($stats['total_assignments']) }}</div>
            <div class="text-[11px] font-bold text-slate-500 mt-1 uppercase tracking-wide">
                {{ __('instructor.assignments') }}
                @if($stats['pending_submissions'] > 0)
                    <span class="text-amber-600 font-normal normal-case">· {{ $stats['pending_submissions'] }} {{ __('instructor.need_grading') }}</span>
                @endif
            </div>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        <div class="id-panel">
            <div class="id-panel-head">
                <div class="flex items-center gap-3">
                    <span class="w-10 h-10 rounded-xl bg-[#eef2ff] text-[#283593] flex items-center justify-center"><i class="fas fa-book text-sm"></i></span>
                    <h3 class="text-sm font-bold text-slate-900 m-0">{{ __('instructor.my_recent_courses') }}</h3>
                </div>
                <a href="{{ route('instructor.courses.index') }}" class="text-xs font-bold text-[#283593] hover:underline">{{ __('instructor.view_all') }} ←</a>
            </div>
            <div>
                @forelse($my_courses as $course)
                <a href="{{ route('instructor.courses.show', $course) }}" class="id-row">
                    <span class="w-10 h-10 rounded-xl bg-slate-100 text-[#283593] flex items-center justify-center flex-shrink-0"><i class="fas fa-play text-xs"></i></span>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-slate-900 truncate m-0">{{ $course->title }}</p>
                        <p class="text-xs text-slate-500 mt-0.5 m-0">
                            {{ $course->active_students_count ?? 0 }} {{ __('instructor.student_single') }}
                            @if($course->academicSubject) · {{ $course->academicSubject->name }} @endif
                        </p>
                    </div>
                    <i class="fas fa-chevron-left text-slate-300 text-xs"></i>
                </a>
                @empty
                <div class="px-6 py-14 text-center text-slate-500 text-sm">{{ __('instructor.no_courses_assigned') }}</div>
                @endforelse
            </div>
        </div>

        <div class="id-panel">
            <div class="id-panel-head">
                <div class="flex items-center gap-3">
                    <span class="w-10 h-10 rounded-xl bg-violet-100 text-violet-600 flex items-center justify-center"><i class="fas fa-calendar-alt text-sm"></i></span>
                    <h3 class="text-sm font-bold text-slate-900 m-0">{{ __('instructor.upcoming_lectures') }}</h3>
                </div>
                <a href="{{ route('instructor.lectures.index') }}" class="text-xs font-bold text-[#283593] hover:underline">{{ __('instructor.view_all') }} ←</a>
            </div>
            <div>
                @forelse($upcoming_lectures as $lecture)
                <a href="{{ route('instructor.lectures.show', $lecture) }}" class="id-row">
                    <span class="w-10 h-10 rounded-xl bg-violet-50 text-violet-600 flex items-center justify-center flex-shrink-0"><i class="fas fa-video text-xs"></i></span>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-slate-900 truncate m-0">{{ $lecture->title }}</p>
                        <p class="text-xs text-slate-500 mt-0.5 m-0">{{ $lecture->course->title ?? __('instructor.not_specified') }} · {{ $lecture->scheduled_at->format('m/d H:i') }}</p>
                    </div>
                    <span class="text-[10px] font-bold text-violet-600 bg-violet-50 px-2 py-1 rounded-lg">{{ $lecture->scheduled_at->diffForHumans() }}</span>
                </a>
                @empty
                <div class="px-6 py-14 text-center">
                    <p class="text-sm text-slate-500 mb-3">{{ __('instructor.no_lectures') }}</p>
                    <a href="{{ route('instructor.lectures.create') }}" class="text-xs font-bold text-[#283593] hover:underline">{{ __('instructor.add_lecture') }} ←</a>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="id-panel">
        <div class="id-panel-head">
            <div class="flex items-center gap-3">
                <span class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center"><i class="fas fa-file-alt text-sm"></i></span>
                <h3 class="text-sm font-bold text-slate-900 m-0">{{ __('instructor.assignments_need_grading') }}</h3>
                @if($stats['pending_submissions'] > 0)
                    <span class="min-w-[24px] h-6 px-2 rounded-full bg-amber-100 text-amber-800 text-xs font-black flex items-center justify-center">{{ $stats['pending_submissions'] }}</span>
                @endif
            </div>
            <a href="{{ route('instructor.assignments.index') }}" class="text-xs font-bold text-[#283593] hover:underline">{{ __('instructor.view_all') }} ←</a>
        </div>
        <div>
            @forelse($pending_assignments as $submission)
            <a href="{{ route('instructor.assignments.submissions', $submission->assignment) }}" class="id-row">
                <span class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center flex-shrink-0"><i class="fas fa-pen text-xs"></i></span>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-slate-900 truncate m-0">{{ $submission->assignment->title ?? __('instructor.assignment_default') }}</p>
                    <p class="text-xs text-slate-500 mt-0.5 m-0">{{ $submission->student->name ?? __('instructor.student_single') }} · {{ $submission->created_at->diffForHumans() }}</p>
                </div>
                <span class="text-[10px] font-bold text-amber-700 bg-amber-50 px-2.5 py-1 rounded-lg">{{ __('instructor.grade_assignment') }}</span>
            </a>
            @empty
            <div class="px-6 py-14 text-center text-emerald-600 text-sm font-semibold">
                <i class="fas fa-check-circle text-xl mb-2 block opacity-70"></i>
                {{ __('instructor.all_assignments_graded') }}
            </div>
            @endforelse
        </div>
    </div>

    <div>
        <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3 px-1">{{ __('instructor.quick_actions') }}</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <a href="{{ route('instructor.lectures.create') }}" class="id-quick">
                <span class="w-11 h-11 rounded-xl bg-[#eef2ff] text-[#283593] flex items-center justify-center"><i class="fas fa-video"></i></span>
                <span class="text-sm font-bold text-slate-700">{{ __('instructor.add_lecture') }}</span>
            </a>
            <a href="{{ route('instructor.assignments.create') }}" class="id-quick">
                <span class="w-11 h-11 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center"><i class="fas fa-tasks"></i></span>
                <span class="text-sm font-bold text-slate-700">{{ __('instructor.add_assignment') }}</span>
            </a>
            <a href="{{ route('instructor.exams.index') }}" class="id-quick">
                <span class="w-11 h-11 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center"><i class="fas fa-clipboard-check"></i></span>
                <span class="text-sm font-bold text-slate-700">{{ __('instructor.manage_exams') }}</span>
            </a>
            <a href="{{ route('instructor.attendance.index') }}" class="id-quick">
                <span class="w-11 h-11 rounded-xl bg-cyan-100 text-cyan-700 flex items-center justify-center"><i class="fas fa-clipboard-list"></i></span>
                <span class="text-sm font-bold text-slate-700">{{ __('instructor.attendance_absence') }}</span>
            </a>
        </div>
    </div>
</div>
@endsection
