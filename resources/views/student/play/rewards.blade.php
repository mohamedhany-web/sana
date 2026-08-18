@extends('layouts.app')

@section('title', __('student.play.rewards_title'))

@push('styles')
@include('dashboard.partials.sanua-theme')
@endpush

@section('content')
<div class="sanua-dash">

    <header class="sanua-page-head">
        <div>
            <h1 class="sanua-page-head__title">{{ __('student.play.rewards_title') }}</h1>
            <p class="sanua-page-head__sub">{{ __('student.play.rewards_sub') }}</p>
        </div>
        <div class="sanua-page-head__actions">
            <a href="{{ route('dashboard') }}" class="sanua-page-head__btn sanua-page-head__btn--ghost">
                <i class="fas fa-home"></i>
                {{ __('student.play.back_dashboard') }}
            </a>
        </div>
    </header>

    <div class="sanua-stats-row">
        <div class="sanua-stat-pill">
            <span class="sanua-stat-pill__icon sanua-stat-pill__icon--gold" aria-hidden="true">
                <i class="fas fa-trophy"></i>
            </span>
            <div class="sanua-stat-pill__body">
                <strong>{{ $achievements->count() }}</strong>
                <span>{{ __('student.play.badges_count') }}</span>
            </div>
        </div>
        <div class="sanua-stat-pill">
            <span class="sanua-stat-pill__icon sanua-stat-pill__icon--purple" aria-hidden="true">
                <i class="fas fa-star"></i>
            </span>
            <div class="sanua-stat-pill__body">
                <strong>{{ number_format($points) }}</strong>
                <span>{{ __('student.total_points') }}</span>
            </div>
        </div>
        <div class="sanua-stat-pill">
            <span class="sanua-stat-pill__icon sanua-stat-pill__icon--green" aria-hidden="true">
                <i class="fas fa-gift"></i>
            </span>
            <div class="sanua-stat-pill__body">
                <strong>{{ number_format($referralRewards, 0) }}</strong>
                <span>{{ __('student.play.referral_rewards') }}</span>
            </div>
        </div>
    </div>

    @if(! empty($tiles))
        <section class="sanua-section">
            <h2 class="sanua-section-title">{{ __('student.play.rewards_sources') }}</h2>
            <div class="sanua-hub-grid">
                @foreach($tiles as $tile)
                    <a href="{{ $tile['url'] }}" class="sanua-hub-tile">
                        <span class="sanua-hub-tile__emoji">{{ $tile['emoji'] }}</span>
                        <span class="sanua-hub-tile__label">{{ $tile['label'] }}</span>
                        <span class="sanua-hub-tile__hint">{{ $tile['hint'] }}</span>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    <section class="sanua-section">
        <h2 class="sanua-section-title">🏆 {{ __('student.achievements_title') }}</h2>
        @if($achievements->isEmpty())
            <div class="sanua-empty">
                <div class="sanua-empty__icon"><i class="fas fa-gift"></i></div>
                <h3>{{ __('student.no_achievements') }}</h3>
                <p>{{ __('student.play.rewards_empty_hint') }}</p>
            </div>
        @else
            <div class="sanua-achievements-grid">
                @foreach($achievements as $achievement)
                    <article class="sanua-achievement-card">
                        <div class="sanua-achievement-card__icon">
                            <i class="{{ $achievement->achievement->icon ?? 'fas fa-trophy' }}"></i>
                        </div>
                        <h3 class="sanua-achievement-card__title">
                            {{ $achievement->achievement->name ?? __('student.achievement_default') }}
                        </h3>
                        @if($achievement->points_earned)
                            <span class="sanua-achievement-card__points">
                                <i class="fas fa-bolt"></i>
                                +{{ $achievement->points_earned }} {{ __('student.points_earned') }}
                            </span>
                        @endif
                    </article>
                @endforeach
            </div>
        @endif
    </section>
</div>
@endsection
