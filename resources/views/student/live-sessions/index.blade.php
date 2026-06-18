@extends('layouts.app')

@section('title', 'جلسات البث المباشر')

@push('styles')
@include('dashboard.partials.sanua-theme')
@endpush

@section('content')
@php
    $statusFilter = request('status');
    $liveCount = $liveSessions->count();
    $listScheduled = $sessions->getCollection()->where('status', 'scheduled');
    $scheduledOnPage = $listScheduled->count();
@endphp

<div class="sanua-dash">

    <header class="sanua-page-head">
        <div>
            <h1 class="sanua-page-head__title">جلسات البث المباشر</h1>
            <p class="sanua-page-head__sub">الجلسات المباشرة والمجدولة المتاحة لك وفق كورساتك</p>
        </div>
        <div class="sanua-page-head__actions">
            @if(Route::has('student.live-recordings.index'))
                <a href="{{ route('student.live-recordings.index') }}" class="sanua-page-head__btn sanua-page-head__btn--ghost">
                    <i class="fas fa-play-circle"></i>
                    التسجيلات
                </a>
            @endif
            <a href="{{ route('my-courses.index') }}" class="sanua-page-head__btn">
                <i class="fas fa-book-open"></i>
                كورساتي
            </a>
        </div>
    </header>

    <div class="sanua-stats-row">
        <div class="sanua-stat-pill">
            <span class="sanua-stat-pill__icon sanua-stat-pill__icon--purple" aria-hidden="true">
                <i class="fas fa-broadcast-tower"></i>
            </span>
            <div class="sanua-stat-pill__body">
                <strong>{{ $sessions->total() }}</strong>
                <span>إجمالي الجلسات</span>
            </div>
        </div>
        <div class="sanua-stat-pill">
            <span class="sanua-stat-pill__icon sanua-stat-pill__icon--red" aria-hidden="true">
                <i class="fas fa-circle"></i>
            </span>
            <div class="sanua-stat-pill__body">
                <strong>{{ $liveCount }}</strong>
                <span>مباشر الآن</span>
            </div>
        </div>
        <div class="sanua-stat-pill">
            <span class="sanua-stat-pill__icon sanua-stat-pill__icon--gold" aria-hidden="true">
                <i class="fas fa-calendar-alt"></i>
            </span>
            <div class="sanua-stat-pill__body">
                <strong>{{ $scheduledOnPage }}</strong>
                <span>مجدولة (هذه الصفحة)</span>
            </div>
        </div>
        <div class="sanua-stat-pill">
            <span class="sanua-stat-pill__icon sanua-stat-pill__icon--amber" aria-hidden="true">
                <i class="fas fa-video"></i>
            </span>
            <div class="sanua-stat-pill__body">
                <strong>{{ $sessions->currentPage() }}/{{ max(1, $sessions->lastPage()) }}</strong>
                <span>صفحة العرض</span>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="sanua-flash sanua-flash--success" role="alert">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="sanua-flash sanua-flash--error" role="alert">{{ session('error') }}</div>
    @endif

    <div class="sanua-filter-tabs">
        <a href="{{ route('student.live-sessions.index') }}"
           class="sanua-filter-tab {{ ! $statusFilter ? 'is-active' : '' }}">الكل</a>
        <a href="{{ route('student.live-sessions.index', ['status' => 'live']) }}"
           class="sanua-filter-tab is-live {{ $statusFilter === 'live' ? 'is-active' : '' }}">
            <span class="sanua-filter-tab__dot"></span>
            مباشر
        </a>
        <a href="{{ route('student.live-sessions.index', ['status' => 'scheduled']) }}"
           class="sanua-filter-tab {{ $statusFilter === 'scheduled' ? 'is-active' : '' }}">مجدولة</a>
    </div>

    @if($liveSessions->count() > 0 && (! $statusFilter || $statusFilter === 'live'))
        <section class="sanua-section">
            <h2 class="sanua-section-label">
                <span class="sanua-section-label__pulse"></span>
                مباشر الآن
            </h2>

            @foreach($liveSessions as $live)
                <div class="sanua-live-card">
                    <div class="sanua-live-card__main">
                        <div class="sanua-live-card__badges">
                            <span class="sanua-badge sanua-badge--live">
                                <span class="sanua-badge__dot"></span>
                                مباشر
                            </span>
                            @if($live->course)
                                <span class="sanua-badge sanua-badge--course">{{ $live->course->title }}</span>
                            @endif
                        </div>
                        <h3 class="sanua-live-card__title">{{ $live->title }}</h3>
                        <p class="sanua-live-card__meta">
                            <i class="fas fa-chalkboard-teacher"></i>
                            {{ $live->instructor?->name ?? '—' }}
                            @if($live->started_at)
                                · بدأ {{ $live->started_at->diffForHumans() }}
                            @endif
                        </p>
                    </div>
                    <form method="POST" action="{{ route('student.live-sessions.join', $live) }}" class="shrink-0">
                        @csrf
                        <button type="submit" class="sanua-btn sanua-btn--live">
                            <i class="fas fa-video"></i>
                            انضم الآن
                        </button>
                    </form>
                </div>
            @endforeach
        </section>
    @endif

    @if($statusFilter !== 'live')
        <section class="sanua-section">
            <h2 class="sanua-section-title">
                📅 {{ $statusFilter === 'scheduled' ? 'الجلسات المجدولة' : 'الجلسات القادمة' }}
            </h2>

            @php
                $listSessions = $sessions->filter(fn ($s) => $s->status !== 'live');
            @endphp

            @if($listSessions->isEmpty())
                <div class="sanua-empty">
                    <div class="sanua-empty__icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <h3>لا توجد جلسات مجدولة {{ $statusFilter === 'scheduled' ? 'حالياً' : 'في هذه الصفحة' }}</h3>
                    <p>ستُعرض الجلسات عند جدولتها من قبل المدرب</p>
                    <a href="{{ route('my-courses.index') }}" class="sanua-empty__btn">
                        <i class="fas fa-book-open"></i>
                        تصفح كورساتي
                    </a>
                </div>
            @else
                <div class="sanua-session-list">
                    @foreach($sessions as $session)
                        @if($session->status !== 'live')
                            <a href="{{ route('student.live-sessions.show', $session) }}" class="sanua-session-card">
                                <div class="sanua-session-card__row">
                                    <div class="sanua-session-card__main">
                                        <div class="sanua-live-card__badges">
                                            <span class="sanua-badge sanua-badge--scheduled">مجدولة</span>
                                            @if($session->course)
                                                <span class="sanua-badge sanua-badge--course">{{ $session->course->title }}</span>
                                            @endif
                                        </div>
                                        <h3 class="sanua-session-card__title">{{ $session->title }}</h3>
                                        <div class="sanua-session-card__details">
                                            <span><i class="fas fa-chalkboard-teacher"></i>{{ $session->instructor?->name ?? '—' }}</span>
                                            <span><i class="fas fa-calendar"></i>{{ $session->scheduled_at?->format('Y/m/d') }}</span>
                                            <span><i class="fas fa-clock"></i>{{ $session->scheduled_at?->format('H:i') }}</span>
                                        </div>
                                        @if($session->description)
                                            <p class="sanua-session-card__desc">{{ Str::limit($session->description, 120) }}</p>
                                        @endif
                                    </div>
                                    <span class="sanua-session-card__action">
                                        التفاصيل
                                        <i class="fas fa-chevron-left"></i>
                                    </span>
                                </div>
                            </a>
                        @endif
                    @endforeach
                </div>
            @endif
        </section>
    @elseif($liveSessions->isEmpty())
        <div class="sanua-empty">
            <div class="sanua-empty__icon">
                <i class="fas fa-broadcast-tower"></i>
            </div>
            <h3>لا توجد جلسات مباشرة حالياً</h3>
            <p>عند بدء المدرب للبث ستظهر الجلسة في أعلى الصفحة</p>
            <a href="{{ route('my-courses.index') }}" class="sanua-empty__btn">
                <i class="fas fa-book-open"></i>
                كورساتي
            </a>
        </div>
    @endif

    @if($sessions->hasPages())
        <div class="sanua-pagination">
            {{ $sessions->links() }}
        </div>
    @endif
</div>
@endsection
