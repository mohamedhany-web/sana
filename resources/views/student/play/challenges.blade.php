@extends('layouts.app')

@section('title', __('student.play.challenges_title'))

@push('styles')
@include('dashboard.partials.sanua-theme')
@endpush

@section('content')
<div class="sanua-dash">

    <header class="sanua-page-head">
        <div>
            <h1 class="sanua-page-head__title">{{ __('student.play.challenges_title') }}</h1>
            <p class="sanua-page-head__sub">{{ __('student.play.challenges_sub') }}</p>
        </div>
        <div class="sanua-page-head__actions">
            <a href="{{ route('dashboard') }}" class="sanua-page-head__btn sanua-page-head__btn--ghost">
                <i class="fas fa-home"></i>
                {{ __('student.play.back_dashboard') }}
            </a>
        </div>
    </header>

    <div class="sanua-challenge" style="margin-bottom:16px;">
        <span class="sanua-challenge__icon">🏆</span>
        <div class="sanua-challenge__text">
            <strong>{{ __('student.play.weekly_headline', ['count' => $goal]) }}</strong>
            <span>
                @if($done)
                    {{ __('student.play.weekly_done', ['xp' => $xpReward]) }}
                @else
                    {{ __('student.play.weekly_hint', ['xp' => $xpReward]) }}
                @endif
            </span>
        </div>
        <a href="{{ $startUrl }}" class="sanua-challenge__btn">
            {{ $done ? __('student.play.continue_lessons') : __('student.play.start_challenge') }}
        </a>
    </div>

    <div class="sanua-stats-row">
        <div class="sanua-stat-pill">
            <span class="sanua-stat-pill__icon sanua-stat-pill__icon--gold" aria-hidden="true">
                <i class="fas fa-trophy"></i>
            </span>
            <div class="sanua-stat-pill__body">
                <strong>{{ $completedLessons }}/{{ $goal }}</strong>
                <span>{{ __('student.play.lessons_this_week') }}</span>
            </div>
        </div>
        <div class="sanua-stat-pill">
            <span class="sanua-stat-pill__icon sanua-stat-pill__icon--purple" aria-hidden="true">
                <i class="fas fa-bolt"></i>
            </span>
            <div class="sanua-stat-pill__body">
                <strong>+{{ $xpReward }}</strong>
                <span>{{ __('student.play.xp_reward') }}</span>
            </div>
        </div>
    </div>

    <section class="sanua-section">
        <h2 class="sanua-section-title">{{ __('student.play.weekly_progress') }}</h2>
        <div class="sanua-panel">
            <div class="sanua-panel__body">
                <p class="sanua-result-row__title" style="margin-bottom:10px;">
                    {{ __('student.play.progress_label', ['done' => $completedLessons, 'goal' => $goal]) }}
                </p>
                <div class="sanua-subject-bar-track">
                    <div class="sanua-subject-bar-fill" style="width:{{ $percent }}%;background:linear-gradient(90deg,#FBBF24,#F59E0B);"></div>
                </div>
                <p class="sanua-subject-pct">{{ $percent }}%</p>
            </div>
        </div>
    </section>
</div>
@endsection
