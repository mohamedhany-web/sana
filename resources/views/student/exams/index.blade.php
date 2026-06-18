@extends('layouts.app')

@section('title', __('student.exams_page_title'))

@push('styles')
@include('dashboard.partials.sanua-theme')
@endpush

@section('content')
@php
    $completedExams = $availableExams->filter(function ($exam) {
        return $exam->last_attempt && $exam->last_attempt->status === 'completed';
    });
    $canAttemptCount = $availableExams->where('can_attempt', true)->count();
    $avgScore = $completedExams->where('best_score', '!=', null)->avg('best_score');
@endphp

<div class="sanua-dash">

    <header class="sanua-page-head">
        <div>
            <h1 class="sanua-page-head__title">{{ __('student.exams_page_title') }}</h1>
            <p class="sanua-page-head__sub">{{ __('student.exams_subtitle') }}</p>
        </div>
        <div class="sanua-page-head__actions">
            <a href="{{ route('my-courses.index') }}" class="sanua-page-head__btn">
                <i class="fas fa-book-open"></i>
                {{ __('student.my_courses_link') }}
            </a>
        </div>
    </header>

    @if($availableExams->count() > 0)
        <div class="sanua-stats-row">
            <div class="sanua-stat-pill">
                <span class="sanua-stat-pill__icon sanua-stat-pill__icon--purple" aria-hidden="true">
                    <i class="fas fa-clipboard-check"></i>
                </span>
                <div class="sanua-stat-pill__body">
                    <strong>{{ $availableExams->count() }}</strong>
                    <span>{{ __('student.available') }}</span>
                </div>
            </div>
            <div class="sanua-stat-pill">
                <span class="sanua-stat-pill__icon sanua-stat-pill__icon--green" aria-hidden="true">
                    <i class="fas fa-check"></i>
                </span>
                <div class="sanua-stat-pill__body">
                    <strong>{{ $completedExams->count() }}</strong>
                    <span>{{ __('student.completed') }}</span>
                </div>
            </div>
            <div class="sanua-stat-pill">
                <span class="sanua-stat-pill__icon sanua-stat-pill__icon--gold" aria-hidden="true">
                    <i class="fas fa-play"></i>
                </span>
                <div class="sanua-stat-pill__body">
                    <strong>{{ $canAttemptCount }}</strong>
                    <span>{{ __('student.can_attempt_label') }}</span>
                </div>
            </div>
            <div class="sanua-stat-pill">
                <span class="sanua-stat-pill__icon sanua-stat-pill__icon--amber" aria-hidden="true">
                    <i class="fas fa-percentage"></i>
                </span>
                <div class="sanua-stat-pill__body">
                    <strong>{{ $avgScore ? number_format($avgScore, 1) : 0 }}%</strong>
                    <span>{{ __('student.avg_results_label') }}</span>
                </div>
            </div>
        </div>

        <section class="sanua-section">
            <h2 class="sanua-section-title">✅ {{ __('student.exams_page_title') }}</h2>
            <div class="sanua-exam-grid">
                @foreach($availableExams as $exam)
                    <article class="sanua-exam-card">
                        <div class="sanua-exam-card__head">
                            <h3 class="sanua-exam-card__title">{{ $exam->title }}</h3>
                            @if($exam->can_attempt)
                                <span class="sanua-badge sanua-badge--available">
                                    <i class="fas fa-check-circle"></i>
                                    {{ __('student.available') }}
                                </span>
                            @else
                                <span class="sanua-badge sanua-badge--locked">
                                    <i class="fas fa-times-circle"></i>
                                    {{ __('student.not_available_now') }}
                                </span>
                            @endif
                        </div>

                        <div class="sanua-exam-card__body">
                            <p class="sanua-exam-card__course">
                                <strong>{{ $exam->offlineCourse->title ?? $exam->course->title ?? '—' }}</strong>
                                @if($exam->offline_course_id)
                                    <span class="text-amber-600">({{ __('student.offline_badge') }})</span>
                                @elseif(optional($exam->course)->academicSubject)
                                    · {{ $exam->course->academicSubject->name }}
                                @endif
                            </p>

                            <div class="sanua-metric-grid">
                                <div class="sanua-metric">
                                    <span class="sanua-metric__label">{{ __('student.duration_label') }}</span>
                                    <span class="sanua-metric__value">{{ $exam->duration_minutes }} {{ __('student.minutes') }}</span>
                                </div>
                                <div class="sanua-metric">
                                    <span class="sanua-metric__label">{{ __('student.questions_label') }}</span>
                                    <span class="sanua-metric__value">{{ $exam->questions_count }} {{ __('student.question_singular') }}</span>
                                </div>
                                <div class="sanua-metric">
                                    <span class="sanua-metric__label">{{ __('student.passing_marks_label') }}</span>
                                    <span class="sanua-metric__value">{{ $exam->passing_marks }}%</span>
                                </div>
                                <div class="sanua-metric">
                                    <span class="sanua-metric__label">{{ __('student.attempts_label') }}</span>
                                    <span class="sanua-metric__value">{{ $exam->attempts_allowed == 0 ? __('student.unlimited_attempts') : $exam->attempts_allowed }}</span>
                                </div>
                            </div>

                            @if($exam->user_attempts > 0)
                                <div class="sanua-exam-card__attempt">
                                    <span>
                                        {{ __('student.your_attempts') }}:
                                        <strong>{{ $exam->user_attempts }}</strong>
                                        {{ __('student.of_attempts') }}
                                        {{ $exam->attempts_allowed == 0 ? __('student.unlimited_attempts') : $exam->attempts_allowed }}
                                    </span>
                                    @if($exam->best_score !== null)
                                        <span>{{ __('student.best_score_label') }}: <strong>{{ number_format($exam->best_score, 1) }}%</strong></span>
                                    @endif
                                </div>
                            @endif

                            @if($exam->description)
                                <p class="sanua-exam-card__desc">{{ $exam->description }}</p>
                            @endif

                            <div class="sanua-exam-card__foot">
                                <div class="sanua-exam-card__dates">
                                    @if($exam->start_time)
                                        {{ __('student.starts_at') }}: {{ $exam->start_time->format('Y-m-d H:i') }}<br>
                                    @endif
                                    @if($exam->end_time)
                                        {{ __('student.ends_at') }}: {{ $exam->end_time->format('Y-m-d H:i') }}
                                    @endif
                                </div>
                                <div>
                                    @if($exam->can_attempt)
                                        <a href="{{ route('student.exams.show', $exam) }}" class="sanua-btn sanua-btn--purple">
                                            <i class="fas fa-play"></i>
                                            {{ __('student.start_exam') }}
                                        </a>
                                    @elseif($exam->user_attempts >= $exam->attempts_allowed && $exam->attempts_allowed > 0)
                                        <span class="sanua-btn sanua-btn--danger-soft">
                                            <i class="fas fa-ban"></i>
                                            {{ __('student.attempts_exhausted') }}
                                        </span>
                                    @else
                                        <span class="sanua-btn sanua-btn--muted">
                                            <i class="fas fa-lock"></i>
                                            {{ __('student.not_available_now') }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            @if($exam->prevent_tab_switch || $exam->require_camera || $exam->require_microphone)
                                <div class="sanua-exam-card__tags">
                                    <span><i class="fas fa-shield-alt"></i> {{ __('student.protected_exam') }}</span>
                                    @if($exam->prevent_tab_switch)<span>{{ __('student.no_tab_switch') }}</span>@endif
                                    @if($exam->require_camera)<span>{{ __('student.camera_label') }}</span>@endif
                                    @if($exam->require_microphone)<span>{{ __('student.microphone_label') }}</span>@endif
                                </div>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

        @if($completedExams->count() > 0)
            <section class="sanua-section">
                <div class="sanua-panel">
                    <div class="sanua-panel__head">
                        <h3>{{ __('student.completed_exams_title') }}</h3>
                    </div>
                    <div class="sanua-panel__body">
                        @foreach($completedExams as $exam)
                            <div class="sanua-result-row">
                                <div>
                                    <p class="sanua-result-row__title">{{ $exam->title }}</p>
                                    <p class="sanua-result-row__sub">
                                        {{ $exam->offlineCourse->title ?? $exam->course->title ?? '—' }}
                                        · {{ $exam->last_attempt->created_at->diffForHumans() }}
                                    </p>
                                </div>
                                <div class="flex items-center gap-3 flex-wrap">
                                    <div class="sanua-result-row__score {{ $exam->last_attempt->result_color == 'green' ? 'is-pass' : 'is-fail' }}">
                                        {{ number_format($exam->last_attempt->percentage, 1) }}%
                                        <div class="text-[10px] font-bold text-slate-400">{{ $exam->last_attempt->result_status }}</div>
                                    </div>
                                    @if($exam->show_results_immediately)
                                        <a href="{{ route('student.exams.result', [$exam, $exam->last_attempt]) }}" class="sanua-result-row__link">
                                            <i class="fas fa-chart-line"></i>
                                            {{ __('student.view_result') }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif
    @else
        <div class="sanua-empty">
            <div class="sanua-empty__icon">
                <i class="fas fa-clipboard-check"></i>
            </div>
            <h3>{{ __('student.no_exams_available') }}</h3>
            <p>{{ __('student.no_exams_desc') }}</p>
            <a href="{{ route('my-courses.index') }}" class="sanua-empty__btn">
                <i class="fas fa-book-open"></i>
                {{ __('student.view_my_courses') }}
            </a>
        </div>
    @endif
</div>
@endsection
