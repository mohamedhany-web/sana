@extends('layouts.app')

@section('title', 'تسجيلات جلسات البث')

@push('styles')
@include('dashboard.partials.sanua-theme')
@endpush

@section('content')
<div class="sanua-dash">

    <header class="sanua-page-head">
        <div>
            <h1 class="sanua-page-head__title">تسجيلات جلسات البث</h1>
            <p class="sanua-page-head__sub">مشاهدة تسجيلات الجلسات المنتهية المنشورة من الإدارة</p>
        </div>
        <div class="sanua-page-head__actions">
            <a href="{{ route('student.live-sessions.index') }}" class="sanua-page-head__btn">
                <i class="fas fa-broadcast-tower"></i>
                جلسات البث
            </a>
        </div>
    </header>

    <div class="sanua-stats-row">
        <div class="sanua-stat-pill">
            <span class="sanua-stat-pill__icon sanua-stat-pill__icon--purple" aria-hidden="true">
                <i class="fas fa-film"></i>
            </span>
            <div class="sanua-stat-pill__body">
                <strong>{{ $recordings->total() }}</strong>
                <span>إجمالي التسجيلات</span>
            </div>
        </div>
        <div class="sanua-stat-pill">
            <span class="sanua-stat-pill__icon sanua-stat-pill__icon--gold" aria-hidden="true">
                <i class="fas fa-play-circle"></i>
            </span>
            <div class="sanua-stat-pill__body">
                <strong>{{ $recordings->count() }}</strong>
                <span>في هذه الصفحة</span>
            </div>
        </div>
        <div class="sanua-stat-pill">
            <span class="sanua-stat-pill__icon sanua-stat-pill__icon--green" aria-hidden="true">
                <i class="fas fa-layer-group"></i>
            </span>
            <div class="sanua-stat-pill__body">
                <strong>{{ $recordings->currentPage() }}/{{ max(1, $recordings->lastPage()) }}</strong>
                <span>صفحة العرض</span>
            </div>
        </div>
        <div class="sanua-stat-pill">
            <span class="sanua-stat-pill__icon sanua-stat-pill__icon--amber" aria-hidden="true">
                <i class="fas fa-video"></i>
            </span>
            <div class="sanua-stat-pill__body">
                <strong>{{ $recordings->count() }}</strong>
                <span>جاهزة للمشاهدة</span>
            </div>
        </div>
    </div>

    @if($recordings->isEmpty())
        <div class="sanua-empty">
            <div class="sanua-empty__icon">
                <i class="fas fa-film"></i>
            </div>
            <h3>لا توجد تسجيلات متاحة حالياً</h3>
            <p>ستظهر هنا تسجيلات الجلسات بعد انتهائها ونشرها من الإدارة</p>
            <a href="{{ route('student.live-sessions.index') }}" class="sanua-empty__btn">
                <i class="fas fa-broadcast-tower"></i>
                عودة لجلسات البث
            </a>
        </div>
    @else
        <section class="sanua-section">
            <h2 class="sanua-section-title">🎬 التسجيلات المتاحة</h2>
            <div class="sanua-courses-grid">
                @foreach($recordings as $rec)
                    <a href="{{ route('student.live-recordings.show', $rec) }}" class="sanua-recording-card">
                        <span class="sanua-recording-card__icon"><i class="fas fa-play"></i></span>
                        <h3 class="sanua-recording-card__title">{{ $rec->title ?? 'تسجيل #' . $rec->id }}</h3>
                        <p class="sanua-recording-card__sub">{{ $rec->session?->title ?? '—' }}</p>
                        <div class="sanua-recording-card__meta">
                            <span><i class="fas fa-clock"></i>{{ $rec->duration_for_humans }}</span>
                            <span><i class="fas fa-hdd"></i>{{ $rec->file_size_for_humans }}</span>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>

        @if($recordings->hasPages())
            <div class="sanua-pagination">
                {{ $recordings->links() }}
            </div>
        @endif
    @endif
</div>
@endsection
