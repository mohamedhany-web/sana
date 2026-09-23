@extends('layouts.app')

@section('title', __('واجباتي'))

@push('styles')
@include('dashboard.partials.sanua-theme')
@endpush

@section('content')
@php
    $pendingCount = $assignments->filter(fn ($a) => ! ($a->my_submission ?? null))->count();
    $submittedCount = $assignments->filter(fn ($a) => ($a->my_submission->status ?? null) === 'submitted')->count();
    $gradedCount = $assignments->filter(fn ($a) => ($a->my_submission->status ?? null) === 'graded')->count();
    $returnedCount = $assignments->filter(fn ($a) => ($a->my_submission->status ?? null) === 'returned')->count();
@endphp

<div class="sanua-dash">

    <header class="sanua-page-head">
        <div>
            <h1 class="sanua-page-head__title">{{ __('واجباتي') }}</h1>
            <p class="sanua-page-head__sub">{{ __('الواجبات المنشورة في كورساتك المسجّل بها') }}</p>
        </div>
        <div class="sanua-page-head__actions">
            <a href="{{ route('my-courses.index') }}" class="sanua-page-head__btn">
                <i class="fas fa-book-open"></i>
                كورساتي
            </a>
        </div>
    </header>

    <div class="sanua-stats-row">
        <div class="sanua-stat-pill">
            <span class="sanua-stat-pill__icon sanua-stat-pill__icon--purple" aria-hidden="true">
                <i class="fas fa-tasks"></i>
            </span>
            <div class="sanua-stat-pill__body">
                <strong>{{ $assignments->count() }}</strong>
                <span>{{ __('إجمالي الواجبات') }}</span>
            </div>
        </div>
        <div class="sanua-stat-pill">
            <span class="sanua-stat-pill__icon sanua-stat-pill__icon--amber" aria-hidden="true">
                <i class="fas fa-hourglass-half"></i>
            </span>
            <div class="sanua-stat-pill__body">
                <strong>{{ $pendingCount }}</strong>
                <span>{{ __('لم يُسلَّم') }}</span>
            </div>
        </div>
        <div class="sanua-stat-pill">
            <span class="sanua-stat-pill__icon sanua-stat-pill__icon--gold" aria-hidden="true">
                <i class="fas fa-paper-plane"></i>
            </span>
            <div class="sanua-stat-pill__body">
                <strong>{{ $submittedCount }}</strong>
                <span>{{ __('قيد التصحيح') }}</span>
            </div>
        </div>
        <div class="sanua-stat-pill">
            <span class="sanua-stat-pill__icon sanua-stat-pill__icon--green" aria-hidden="true">
                <i class="fas fa-check-circle"></i>
            </span>
            <div class="sanua-stat-pill__body">
                <strong>{{ $gradedCount + $returnedCount }}</strong>
                <span>{{ __('مُقيَّم / مُعاد') }}</span>
            </div>
        </div>
    </div>

    @if($assignments->isEmpty())
        <div class="sanua-empty">
            <div class="sanua-empty__icon">
                <i class="fas fa-tasks"></i>
            </div>
            <h3>{{ __('لا توجد واجبات متاحة حالياً') }}</h3>
            <p>{{ __('ستظهر الواجبات هنا عند نشرها في كورساتك') }}</p>
            <a href="{{ route('my-courses.index') }}" class="sanua-empty__btn">
                <i class="fas fa-book-open"></i>
                كورساتي
            </a>
        </div>
    @else
        <section class="sanua-section">
            <h2 class="sanua-section-title">📝 قائمة الواجبات</h2>
            <div class="sanua-session-list">
                @foreach($assignments as $assignment)
                    @php
                        $sub = $assignment->my_submission ?? null;
                        $courseTitle = $assignment->course->title ?? __('كورس');
                    @endphp
                    <a href="{{ route('student.assignments.show', $assignment) }}" class="sanua-session-card">
                        <div class="sanua-session-card__row">
                            <div class="sanua-session-card__main">
                                <div class="sanua-live-card__badges">
                                    @if(! $sub)
                                        <span class="sanua-badge sanua-badge--pending">{{ __('لم يُسلَّم') }}</span>
                                    @elseif($sub->status === 'submitted')
                                        <span class="sanua-badge sanua-badge--submitted">{{ __('قيد التصحيح') }}</span>
                                    @elseif($sub->status === 'graded')
                                        <span class="sanua-badge sanua-badge--graded">
 {{ __('مُقيَّم') }}{{ $sub->score !== null ? ' — '.$sub->score.'/'.$assignment->max_score : '' }}
                                        </span>
                                    @elseif($sub->status === 'returned')
                                        <span class="sanua-badge sanua-badge--returned">{{ __('مُعاد للتعديل') }}</span>
                                    @endif
                                    <span class="sanua-badge sanua-badge--course">{{ $courseTitle }}</span>
                                </div>
                                <h3 class="sanua-session-card__title">{{ $assignment->title }}</h3>
                                @if($assignment->due_date)
                                    <div class="sanua-session-card__details">
                                        <span>
                                            <i class="fas fa-clock"></i>
 {{ __('التسليم:') }} {{ $assignment->due_date->timezone(config('app.timezone'))->format('Y-m-d H:i') }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                            <span class="sanua-session-card__action">
                                فتح الواجب
                                <i class="fas fa-chevron-left"></i>
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif
</div>
@endsection
