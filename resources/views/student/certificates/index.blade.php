@extends('layouts.app')

@section('title', __('student.my_certificates_title'))

@push('styles')
@include('dashboard.partials.sanua-theme')
@endpush

@section('content')
@php $isRtl = app()->getLocale() === 'ar'; @endphp

<div class="sanua-dash">

    <header class="sanua-page-head">
        <div>
            <h1 class="sanua-page-head__title">{{ __('student.my_certificates_title') }}</h1>
            <p class="sanua-page-head__sub">{{ __('student.certificates_subtitle') }}</p>
        </div>
        <div class="sanua-page-head__actions">
            <a href="{{ route('my-courses.index') }}" class="sanua-page-head__btn">
                <i class="fas fa-book-open"></i>
                {{ __('student.view_my_courses') }}
            </a>
        </div>
    </header>

    @if(isset($stats))
        <div class="sanua-stats-row">
            <div class="sanua-stat-pill">
                <span class="sanua-stat-pill__icon sanua-stat-pill__icon--purple" aria-hidden="true">
                    <i class="fas fa-certificate"></i>
                </span>
                <div class="sanua-stat-pill__body">
                    <strong>{{ $stats['total'] ?? 0 }}</strong>
                    <span>{{ __('student.total_certificates') }}</span>
                </div>
            </div>
            <div class="sanua-stat-pill">
                <span class="sanua-stat-pill__icon sanua-stat-pill__icon--green" aria-hidden="true">
                    <i class="fas fa-check-circle"></i>
                </span>
                <div class="sanua-stat-pill__body">
                    <strong>{{ $stats['issued'] ?? 0 }}</strong>
                    <span>{{ __('student.issued_label') }}</span>
                </div>
            </div>
            <div class="sanua-stat-pill">
                <span class="sanua-stat-pill__icon sanua-stat-pill__icon--gold" aria-hidden="true">
                    <i class="fas fa-award"></i>
                </span>
                <div class="sanua-stat-pill__body">
                    <strong>{{ $certificates->count() }}</strong>
                    <span>في هذه الصفحة</span>
                </div>
            </div>
            <div class="sanua-stat-pill">
                <span class="sanua-stat-pill__icon sanua-stat-pill__icon--amber" aria-hidden="true">
                    <i class="fas fa-layer-group"></i>
                </span>
                <div class="sanua-stat-pill__body">
                    <strong>{{ $certificates->currentPage() }}/{{ max(1, $certificates->lastPage()) }}</strong>
                    <span>صفحة العرض</span>
                </div>
            </div>
        </div>
    @endif

    @if(isset($certificates) && $certificates->count() > 0)
        <section class="sanua-section">
            <h2 class="sanua-section-title">🎓 {{ __('student.my_certificates_title') }}</h2>
            <div class="sanua-courses-grid">
                @foreach($certificates as $certificate)
                    <a href="{{ route('student.certificates.show', $certificate) }}" class="sanua-cert-card">
                        <div class="sanua-cert-card__hero">
                            <i class="fas fa-certificate"></i>
                        </div>
                        <div class="sanua-cert-card__body">
                            <h3 class="sanua-cert-card__title">
                                {{ $certificate->title ?? $certificate->course_name ?? __('student.completion_certificate') }}
                            </h3>
                            @if($certificate->course)
                                <p class="sanua-cert-card__course">{{ $certificate->course->title }}</p>
                            @endif
                            <div class="sanua-cert-card__meta">
                                <span>
                                    <i class="fas fa-calendar"></i>
                                    {{ ($certificate->issued_at ? $certificate->issued_at->format('Y-m-d') : ($certificate->issue_date ? $certificate->issue_date->format('Y-m-d') : '—')) }}
                                </span>
                                @if($certificate->certificate_number)
                                    <span class="sanua-cert-card__num">#{{ substr($certificate->certificate_number, -6) }}</span>
                                @endif
                            </div>
                            <span class="sanua-cert-card__cta">
                                {{ __('student.view_certificate') }}
                                <i class="fas fa-arrow-{{ $isRtl ? 'left' : 'right' }}"></i>
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>

        @if($certificates->hasPages())
            <div class="sanua-pagination">
                {{ $certificates->links() }}
            </div>
        @endif
    @else
        <div class="sanua-empty">
            <div class="sanua-empty__icon">
                <i class="fas fa-certificate"></i>
            </div>
            <h3>{{ __('student.no_certificates') }}</h3>
            <p>{{ __('student.no_certificates_desc') }}</p>
            <a href="{{ route('my-courses.index') }}" class="sanua-empty__btn">
                <i class="fas fa-book-open"></i>
                {{ __('student.view_my_courses') }}
            </a>
        </div>
    @endif
</div>
@endsection
