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
            <p class="sanua-page-head__sub">مشاهدة تسجيلات حصصك مع المعلمين وتسجيلات الجلسات المنشورة</p>
        </div>
        <div class="sanua-page-head__actions">
            <a href="{{ route('student.live-sessions.index') }}" class="sanua-page-head__btn">
                <i class="fas fa-broadcast-tower"></i>
                جلسات البث
            </a>
        </div>
    </header>

    @php
        $lessonRecordings = $lessonRecordings ?? collect();
        $lessonReadyCount = $lessonRecordings->filter(fn ($r) => $r->isReady())->count();
        $totalVisible = $recordings->total() + $lessonRecordings->count();
        $pageVisible = $recordings->count() + $lessonRecordings->count();
        $readyVisible = $recordings->count() + $lessonReadyCount;
    @endphp

    <div class="sanua-stats-row">
        <div class="sanua-stat-pill">
            <span class="sanua-stat-pill__icon sanua-stat-pill__icon--purple" aria-hidden="true">
                <i class="fas fa-film"></i>
            </span>
            <div class="sanua-stat-pill__body">
                <strong>{{ $totalVisible }}</strong>
                <span>إجمالي التسجيلات</span>
            </div>
        </div>
        <div class="sanua-stat-pill">
            <span class="sanua-stat-pill__icon sanua-stat-pill__icon--gold" aria-hidden="true">
                <i class="fas fa-play-circle"></i>
            </span>
            <div class="sanua-stat-pill__body">
                <strong>{{ $pageVisible }}</strong>
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
                <strong>{{ $readyVisible }}</strong>
                <span>جاهزة للمشاهدة</span>
            </div>
        </div>
    </div>
    @if($recordings->isEmpty() && $lessonRecordings->isEmpty())
        <div class="sanua-empty">
            <div class="sanua-empty__icon">
                <i class="fas fa-film"></i>
            </div>
            <h3>لا توجد تسجيلات متاحة حالياً</h3>
            <p>ستظهر هنا تسجيلات حصصك مع المعلمين بعد انتهاء الحصة وتجهيز الملف</p>
            <a href="{{ route('student.live-sessions.index') }}" class="sanua-empty__btn">
                <i class="fas fa-broadcast-tower"></i>
                عودة لجلسات البث
            </a>
        </div>
    @else
        @if($lessonRecordings->isNotEmpty())
        <section class="sanua-section">
            <h2 class="sanua-section-title">حصصي مع المعلمين</h2>
            <div class="sanua-courses-grid">
                @foreach($lessonRecordings as $rec)
                    @if($rec->isReady())
                    <a href="{{ route('student.live-recordings.lesson', $rec) }}" class="sanua-recording-card">
                    @else
                    <div class="sanua-recording-card" style="opacity:.85;cursor:default">
                    @endif
                        <span class="sanua-recording-card__icon"><i class="fas fa-chalkboard-user"></i></span>
                        <h3 class="sanua-recording-card__title">{{ $rec->title }}</h3>
                        <p class="sanua-recording-card__sub">المعلم: {{ $rec->instructor?->name ?? '—' }}</p>
                        <div class="sanua-recording-card__meta">
                            @if($rec->isReady())
                                <span><i class="fas fa-clock"></i>{{ $rec->duration_for_humans }}</span>
                                <span><i class="fas fa-hdd"></i>{{ $rec->file_size_for_humans }}</span>
                            @else
                                <span><i class="fas fa-spinner"></i>جاري تجهيز التسجيل… حدّث الصفحة بعد قليل</span>
                            @endif
                        </div>
                    @if($rec->isReady())
                    </a>
                    @else
                    </div>
                    @endif
                @endforeach
            </div>
        </section>
        @endif
        @if($recordings->isNotEmpty())
        <section class="sanua-section">
            <h2 class="sanua-section-title">🎬 تسجيلات الجلسات</h2>
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
        @endif

        @if($recordings->hasPages())
            <div class="sanua-pagination">
                {{ $recordings->links() }}
            </div>
        @endif
    @endif
</div>
@endsection
