@extends('layouts.app')

@section('title', __('student.achievements_title'))

@push('styles')
@include('dashboard.partials.sanua-theme')
@endpush

@section('content')
<div class="sanua-dash">

    <header class="sanua-page-head">
        <div>
            <h1 class="sanua-page-head__title">{{ __('student.achievements_title') }}</h1>
            <p class="sanua-page-head__sub">{{ __('student.total_points') }} · {{ __('student.achievements_title') }}</p>
        </div>
        <div class="sanua-page-head__actions">
            <a href="{{ route('my-courses.index') }}" class="sanua-page-head__btn">
                <i class="fas fa-book-open"></i>
                {{ __('student.my_courses_link') }}
            </a>
        </div>
    </header>

    @if(isset($stats))
        <div class="sanua-stats-row">
            <div class="sanua-stat-pill">
                <span class="sanua-stat-pill__icon sanua-stat-pill__icon--purple" aria-hidden="true">
                    <i class="fas fa-trophy"></i>
                </span>
                <div class="sanua-stat-pill__body">
                    <strong>{{ $stats['total'] ?? 0 }}</strong>
                    <span>إجمالي الإنجازات</span>
                </div>
            </div>
            <div class="sanua-stat-pill">
                <span class="sanua-stat-pill__icon sanua-stat-pill__icon--gold" aria-hidden="true">
                    <i class="fas fa-star"></i>
                </span>
                <div class="sanua-stat-pill__body">
                    <strong>{{ number_format($stats['total_points'] ?? 0) }}</strong>
                    <span>{{ __('student.total_points') }}</span>
                </div>
            </div>
            <div class="sanua-stat-pill">
                <span class="sanua-stat-pill__icon sanua-stat-pill__icon--green" aria-hidden="true">
                    <i class="fas fa-medal"></i>
                </span>
                <div class="sanua-stat-pill__body">
                    <strong>{{ $achievements->count() }}</strong>
                    <span>في هذه الصفحة</span>
                </div>
            </div>
            <div class="sanua-stat-pill">
                <span class="sanua-stat-pill__icon sanua-stat-pill__icon--amber" aria-hidden="true">
                    <i class="fas fa-layer-group"></i>
                </span>
                <div class="sanua-stat-pill__body">
                    <strong>{{ $achievements->currentPage() }}/{{ max(1, $achievements->lastPage()) }}</strong>
                    <span>صفحة العرض</span>
                </div>
            </div>
        </div>
    @endif

    @if(isset($achievements) && $achievements->count() > 0)
        <section class="sanua-section">
            <h2 class="sanua-section-title">🏆 {{ __('student.achievements_title') }}</h2>
            <div class="sanua-achievements-grid">
                @foreach($achievements as $achievement)
                    <article class="sanua-achievement-card">
                        @if($achievement->achievement && $achievement->achievement->icon)
                            <div class="sanua-achievement-card__icon">
                                <i class="{{ $achievement->achievement->icon }}"></i>
                            </div>
                        @else
                            <div class="sanua-achievement-card__icon">
                                <i class="fas fa-trophy"></i>
                            </div>
                        @endif
                        <h3 class="sanua-achievement-card__title">
                            {{ $achievement->achievement->name ?? __('student.achievement_default') }}
                        </h3>
                        @if($achievement->achievement->description ?? null)
                            <p class="sanua-achievement-card__desc">{{ $achievement->achievement->description }}</p>
                        @endif
                        @if($achievement->points_earned)
                            <span class="sanua-achievement-card__points">
                                <i class="fas fa-bolt"></i>
                                +{{ $achievement->points_earned }} {{ __('student.points_earned') }}
                            </span>
                        @endif
                    </article>
                @endforeach
            </div>
        </section>

        @if($achievements->hasPages())
            <div class="sanua-pagination">
                {{ $achievements->links() }}
            </div>
        @endif
    @else
        <div class="sanua-empty">
            <div class="sanua-empty__icon">
                <i class="fas fa-trophy"></i>
            </div>
            <h3>{{ __('student.no_achievements') }}</h3>
            <p>استمر في التعلّم لفتح إنجازات جديدة</p>
            <a href="{{ route('my-courses.index') }}" class="sanua-empty__btn">
                <i class="fas fa-book-open"></i>
                {{ __('student.view_my_courses') }}
            </a>
        </div>
    @endif
</div>
@endsection
