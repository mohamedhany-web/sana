@extends('layouts.app')

@section('title', __('student.my_courses_active_title'))

@push('styles')
@include('dashboard.partials.sanua-theme')
@endpush

@section('content')
<div class="sanua-dash">

    <header class="sanua-page-head">
        <div>
            <h1 class="sanua-page-head__title">{{ __('student.my_courses_active_title') }}</h1>
            <p class="sanua-page-head__sub">{{ __('student.my_courses_subtitle') }}</p>
        </div>
        <div class="sanua-page-head__actions">
            <a href="{{ route('public.courses') }}" class="sanua-page-head__btn">
                <i class="fas fa-th-large"></i>
                {{ __('student.browse_courses') }}
            </a>
        </div>
    </header>

    <div class="sanua-stats-row">
        <div class="sanua-stat-pill">
            <span class="sanua-stat-pill__icon sanua-stat-pill__icon--purple" aria-hidden="true">
                <i class="fas fa-book-open"></i>
            </span>
            <div class="sanua-stat-pill__body">
                <strong>{{ $stats['total_active'] }}</strong>
                <span>{{ __('student.active_label') }}</span>
            </div>
        </div>
        <div class="sanua-stat-pill">
            <span class="sanua-stat-pill__icon sanua-stat-pill__icon--green" aria-hidden="true">
                <i class="fas fa-check-circle"></i>
            </span>
            <div class="sanua-stat-pill__body">
                <strong>{{ $stats['total_completed'] }}</strong>
                <span>{{ __('student.completed') }}</span>
            </div>
        </div>
        <div class="sanua-stat-pill">
            <span class="sanua-stat-pill__icon sanua-stat-pill__icon--gold" aria-hidden="true">
                <i class="fas fa-clock"></i>
            </span>
            <div class="sanua-stat-pill__body">
                <strong>{{ $stats['total_hours'] }}</strong>
                <span>{{ __('student.hours_label') }}</span>
            </div>
        </div>
        <div class="sanua-stat-pill">
            <span class="sanua-stat-pill__icon sanua-stat-pill__icon--amber" aria-hidden="true">
                <i class="fas fa-chart-line"></i>
            </span>
            <div class="sanua-stat-pill__body">
                <strong>{{ $stats['avg_progress'] }}%</strong>
                <span>{{ __('student.avg_progress_label') }}</span>
            </div>
        </div>
    </div>

    @if($activeCourses->count() > 0)
        <section class="sanua-section">
            <h2 class="sanua-section-title">📚 {{ __('student.my_courses_active_title') }}</h2>

            <div class="sanua-courses-grid">
                @foreach($activeCourses as $course)
                    @php
                        $progress = (int) ($course->pivot->progress ?? 0);
                        $isCompleted = $progress >= 100;
                    @endphp
                    <a href="{{ route('my-courses.show', $course) }}" class="sanua-course-card">
                        <div class="sanua-course-card__thumb">
                            @if($course->thumbnail)
                                <img src="{{ public_storage_url($course->thumbnail) }}" alt="{{ $course->title }}" loading="lazy">
                            @else
                                <div class="sanua-course-card__thumb-fallback">
                                    <i class="fas fa-graduation-cap"></i>
                                    <span>{{ $course->academicSubject->name ?? __('student.course_fallback') }}</span>
                                </div>
                            @endif
                            <span class="sanua-course-card__badge {{ $isCompleted ? 'sanua-course-card__badge--done' : 'sanua-course-card__badge--active' }}">
                                <i class="fas {{ $isCompleted ? 'fa-check-circle' : 'fa-play-circle' }}"></i>
                                {{ $isCompleted ? __('student.completed_badge') : __('student.active_badge') }}
                            </span>
                        </div>

                        <div class="sanua-course-card__body">
                            <h3 class="sanua-course-card__title">{{ $course->title }}</h3>
                            <p class="sanua-course-card__meta">
                                {{ $course->academicSubject->name ?? '—' }}
                                · {{ $course->teacher->name ?? '—' }}
                                · {{ $course->lessons->count() }} {{ __('student.lesson_singular') }}
                            </p>

                            <div class="sanua-course-card__stats">
                                <span>{{ __('student.progress') }} <strong>{{ $progress }}%</strong></span>
                                <span class="points">
                                    <i class="fas fa-star"></i>
                                    {{ number_format((float) ($course->student_points ?? 0), 0) }}
                                </span>
                            </div>

                            <div class="sanua-course-card__bar" role="progressbar" aria-valuenow="{{ min($progress, 100) }}" aria-valuemin="0" aria-valuemax="100">
                                <div class="sanua-course-card__bar-fill" style="width: {{ min($progress, 100) }}%;"></div>
                            </div>

                            <span class="sanua-course-card__cta">
                                <i class="fas fa-play"></i>
                                {{ __('student.continue_learning') }}
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>

            @if($activeCourses->hasPages())
                <div class="sanua-pagination">
                    {{ $activeCourses->links() }}
                </div>
            @endif
        </section>
    @else
        <div class="sanua-empty">
            <div class="sanua-empty__icon">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <h3>{{ __('student.no_active_courses_my') }}</h3>
            <p>{{ __('student.no_active_courses_desc') }}</p>
            <a href="{{ route('public.courses') }}" class="sanua-empty__btn">
                <i class="fas fa-search"></i>
                {{ __('student.browse_courses_btn') }}
            </a>
        </div>
    @endif
</div>
@endsection
