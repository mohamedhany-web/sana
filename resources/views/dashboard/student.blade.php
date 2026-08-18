@extends('layouts.app')

@section('title', 'SANUA — ' . __('student.dashboard_title'))

@push('styles')
@include('dashboard.partials.sanua-theme')
@endpush

@section('content')
@php
    $user = auth()->user();
    $coursesEnabled = (bool) config('student.courses_enabled');
    $firstName = explode(' ', trim($user->name))[0];
    $progress = min((int) ($stats['total_progress'] ?? 0), 100);
    $level = max(1, (int) floor($progress / 15) + 1);
    $xp = (int) ($progress * 48 + ($achievementsCount ?? 0) * 150 + ($stats['completed_exams'] ?? 0) * 80);
    $coins = ($stats['completed_courses'] ?? 0) * 50 + ($achievementsCount ?? 0) * 30;
    $streak = min(14, max(1, (int) ($recentActivity ?? collect())->filter(fn ($a) => isset($a['at']) && $a['at']->gte(now()->subDays(7)))->count() + 1));
    $rank = max(1, 100 - $level * 3);

    $subjectTemplates = [
        ['key' => 'math', 'title' => 'الرياضيات', 'emoji' => '🔢', 'theme' => 'math', 'keywords' => ['رياض', 'math', 'حساب']],
        ['key' => 'english', 'title' => 'الإنجليزية', 'emoji' => '🌍', 'theme' => 'english', 'keywords' => ['انجل', 'english', 'إنجل']],
        ['key' => 'science', 'title' => 'العلوم', 'emoji' => '🔬', 'theme' => 'science', 'keywords' => ['علوم', 'science', 'فيز']],
    ];
    $subjectCards = $coursesEnabled
        ? collect($subjectTemplates)->map(function ($tpl) use ($activeCourses) {
            $course = ($activeCourses ?? collect())->first(function ($c) use ($tpl) {
                $title = mb_strtolower((string) ($c->title ?? ''));
                $subject = mb_strtolower((string) ($c->academicSubject?->name ?? ''));
                foreach ($tpl['keywords'] as $kw) {
                    if (str_contains($title, $kw) || str_contains($subject, $kw)) {
                        return true;
                    }
                }
                return false;
            });
            $prog = $course ? (float) ($course->pivot->progress ?? optional($course->enrollment ?? null)->progress ?? 0) : 0;
            $lessons = $course ? (int) ($course->lessons_count ?? 0) : 12;
            $fallbackProgress = ['math' => 28, 'english' => 35, 'science' => 22][$tpl['key']] ?? 25;

            return array_merge($tpl, [
                'progress' => $prog > 0 ? $prog : $fallbackProgress,
                'lessons' => $lessons,
                'url' => $course ? route('my-courses.show', $course->id) : route('my-courses.index'),
            ]);
        })
        : collect();

    $firstExistingRoute = static function (array $names): ?string {
        foreach ($names as $name) {
            if (is_string($name) && $name !== '' && \Illuminate\Support\Facades\Route::has($name)) {
                return route($name);
            }
        }

        return null;
    };

    $lessonsUrl = $coursesEnabled
        ? ($firstExistingRoute(['my-courses.index', 'student.tutor-lessons.hub']) ?? route('dashboard'))
        : ($firstExistingRoute(['student.tutor-lessons.hub', 'my-courses.index']) ?? route('dashboard'));

    $playTiles = array_filter([
        ['emoji' => '📝', 'label' => 'واجبات', 'url' => $firstExistingRoute(['student.assignments.index'])],
        ['emoji' => '🎮', 'label' => 'أنشطة', 'url' => $firstExistingRoute(['student.play.activities'])],
        ['emoji' => '🎬', 'label' => 'فيديو', 'url' => $coursesEnabled ? $firstExistingRoute(['my-courses.index']) : null],
        ['emoji' => '📖', 'label' => 'قصص', 'url' => $coursesEnabled ? $firstExistingRoute(['public.courses']) : null],
        ['emoji' => '🏆', 'label' => 'تحديات', 'url' => $firstExistingRoute(['student.play.challenges'])],
        ['emoji' => '✅', 'label' => 'امتحانات', 'url' => $firstExistingRoute(['student.exams.index'])],
        ['emoji' => '🎓', 'label' => 'شهادات', 'url' => $firstExistingRoute(['student.certificates.index'])],
        ['emoji' => '🎁', 'label' => 'مكافآت', 'url' => $firstExistingRoute(['student.play.rewards'])],
    ], fn ($t) => ! empty($t['url']));

    if ($coursesEnabled && $activeCourses->isNotEmpty()) {
        $ctaUrl = route('my-courses.learn', $activeCourses->first()->id);
        $ctaLabel = 'ابدأ التعلّم الآن';
    } elseif ($coursesEnabled) {
        $ctaUrl = route('public.courses');
        $ctaLabel = 'استكشف الكورسات';
    } elseif (Route::has('student.tutor-lessons.hub')) {
        $ctaUrl = route('student.tutor-lessons.hub');
        $ctaLabel = 'استكشف الدروس';
    } elseif (Route::has('student.live-sessions.index')) {
        $ctaUrl = route('student.live-sessions.index');
        $ctaLabel = 'البث المباشر';
    } else {
        $ctaUrl = route('dashboard');
        $ctaLabel = 'ابدأ الآن';
    }

    $heroBoy = public_static_exists('img/sanua/hero-boy.png')
        ? public_static_url('img/sanua/hero-boy.png')
        : (public_static_exists('img/sanua/hero-character.png')
            ? public_static_url('img/sanua/hero-character.png')
            : null);

    $challengeUrl = $firstExistingRoute(['student.play.challenges']) ?? $lessonsUrl;
@endphp

<div class="sanua-dash">

    {{-- HERO — شخصية + ديكور CSS + نص --}}
    <div class="sanua-hero-wrap">
        <section class="sanua-hero" aria-label="ترحيب">
            <div class="sanua-hero__deco" aria-hidden="true">
                <span class="sanua-hero__blob sanua-hero__blob--1"></span>
                <span class="sanua-hero__blob sanua-hero__blob--2"></span>
                <span class="sanua-hero__blob sanua-hero__blob--3"></span>
                <span class="sanua-hero__ring sanua-hero__ring--1"></span>
                <span class="sanua-hero__ring sanua-hero__ring--2"></span>
                <span class="sanua-hero__dot sanua-hero__dot--1"></span>
                <span class="sanua-hero__dot sanua-hero__dot--2"></span>
                <span class="sanua-hero__dot sanua-hero__dot--3"></span>
                <span class="sanua-hero__cross sanua-hero__cross--1"></span>
                <span class="sanua-hero__cross sanua-hero__cross--2"></span>
                <span class="sanua-hero__spark sanua-hero__spark--g1">✦</span>
                <span class="sanua-hero__spark sanua-hero__spark--g2">✦</span>
                <span class="sanua-hero__spark sanua-hero__spark--g3">✦</span>
                <span class="sanua-hero__star-icon sanua-hero__star-icon--g1">⭐</span>
            </div>

            @if($heroBoy)
            <div class="sanua-hero__visual">
                <div class="sanua-hero__visual-deco" aria-hidden="true">
                    <span class="sanua-hero__cloud sanua-hero__cloud--a"></span>
                    <span class="sanua-hero__cloud sanua-hero__cloud--b"></span>
                    <span class="sanua-hero__cloud sanua-hero__cloud--c"></span>
                    <span class="sanua-hero__cloud sanua-hero__cloud--d"></span>
                    <span class="sanua-hero__cloud sanua-hero__cloud--e"></span>
                    <span class="sanua-hero__spark sanua-hero__spark--a">✦</span>
                    <span class="sanua-hero__spark sanua-hero__spark--b">✦</span>
                    <span class="sanua-hero__spark sanua-hero__spark--c">✦</span>
                    <span class="sanua-hero__spark sanua-hero__spark--d">✦</span>
                    <span class="sanua-hero__spark sanua-hero__spark--e">✦</span>
                    <span class="sanua-hero__star-icon sanua-hero__star-icon--a">⭐</span>
                    <span class="sanua-hero__star-icon sanua-hero__star-icon--b">⭐</span>
                    <span class="sanua-hero__star-icon sanua-hero__star-icon--c">⭐</span>
                    <span class="sanua-hero__bulb">💡</span>
                    <span class="sanua-hero__mini-circle sanua-hero__mini-circle--1"></span>
                    <span class="sanua-hero__mini-circle sanua-hero__mini-circle--2"></span>
                </div>
                <span class="sanua-hero__char-glow"></span>
                <img
                    src="{{ $heroBoy }}?v=5"
                    alt=""
                    width="1536"
                    height="1024"
                    loading="eager"
                    fetchpriority="high"
                    decoding="async"
                >
            </div>
            @endif

            <div class="sanua-hero__content">
                <p class="sanua-hero__hello">مرحباً {{ $firstName }} 👋</p>
                <h1 class="sanua-hero__title">
                    <span class="line-white">تعلّم ممتع </span><span class="line-gold">يبدأ مع سنا</span>
                </h1>
                <p class="sanua-hero__sub">منصة تعليمية تفاعلية تلهم الأطفال وتبني مهاراتهم بثقة ومتعة.</p>
                <a href="{{ $ctaUrl }}" class="sanua-hero__cta">
                    {{ $ctaLabel }}
                    <i class="fas fa-arrow-left"></i>
                </a>
            </div>
        </section>
    </div>

    {{-- إحصائيات — خارج الـ hero --}}
    <div class="sanua-stats-row">
        <div class="sanua-stat-pill">
            <span class="sanua-stat-pill__icon sanua-stat-pill__icon--purple" aria-hidden="true">
                <i class="fas fa-shield-alt"></i>
            </span>
            <div class="sanua-stat-pill__body"><strong>Lv {{ $level }}</strong><span>المستوى</span></div>
        </div>
        <div class="sanua-stat-pill">
            <span class="sanua-stat-pill__icon sanua-stat-pill__icon--gold" aria-hidden="true">
                <i class="fas fa-bolt"></i>
            </span>
            <div class="sanua-stat-pill__body"><strong>{{ number_format($xp) }}</strong><span>نقاط XP</span></div>
        </div>
        <div class="sanua-stat-pill">
            <span class="sanua-stat-pill__icon sanua-stat-pill__icon--green" aria-hidden="true">
                <i class="fas fa-fire"></i>
            </span>
            <div class="sanua-stat-pill__body"><strong>{{ $streak }} يوم</strong><span>سلسلة يومية</span></div>
        </div>
        <div class="sanua-stat-pill">
            <span class="sanua-stat-pill__icon sanua-stat-pill__icon--amber" aria-hidden="true">
                <i class="fas fa-star"></i>
            </span>
            <div class="sanua-stat-pill__body"><strong>{{ $achievementsCount }}</strong><span>إنجاز</span></div>
        </div>
    </div>

    {{-- تحدي أسبوعي --}}
    <div class="sanua-challenge">
        <span class="sanua-challenge__icon">🏆</span>
        <div class="sanua-challenge__text">
            <strong>تحدي الأسبوع: أكمل 3 دروس!</strong>
            <span>شارك في التحدي واحصل على {{ $streak > 1 ? $streak * 10 : 50 }} XP إضافي</span>
        </div>
        <a href="{{ $challengeUrl }}" class="sanua-challenge__btn">ابدأ التحدي</a>
    </div>

    {{-- المواد --}}
    @if($coursesEnabled && $subjectCards->isNotEmpty())
    <section class="sanua-section">
        <h2 class="sanua-section-title">📚 موادك الدراسية</h2>
        <div class="sanua-subjects-grid">
            @foreach($subjectCards as $sub)
                <a href="{{ $sub['url'] }}" class="sanua-subject-card sanua-subject-card--{{ $sub['theme'] }}">
                    <div class="sanua-subject-card__illus">{{ $sub['emoji'] }}</div>
                    <div class="sanua-subject-card__info">
                        <h3>{{ $sub['title'] }}</h3>
                        <p class="meta">{{ $sub['lessons'] }} درس</p>
                    </div>
                    <div class="sanua-subject-card__progress">
                        <div class="sanua-subject-bar-track">
                            <div class="sanua-subject-bar-fill" style="width:{{ (int) $sub['progress'] }}%"></div>
                        </div>
                        <p class="sanua-subject-pct">{{ (int) $sub['progress'] }}% مكتمل</p>
                    </div>
                </a>
            @endforeach
        </div>
    </section>
    @endif

    {{-- العب وتعلّم --}}
    <section class="sanua-section">
        <h2 class="sanua-section-title">🎮 العب وتعلّم</h2>
        <div class="sanua-play-grid">
            @foreach($playTiles as $tile)
                <a href="{{ $tile['url'] }}" class="sanua-play-tile">
                    <span class="sanua-play-emoji">{{ $tile['emoji'] }}</span>
                    <span class="sanua-play-lbl">{{ $tile['label'] }}</span>
                </a>
            @endforeach
        </div>
    </section>

    @if(isset($activeSubscription) && $activeSubscription)
    <div class="sanua-sub-banner">
        <div class="flex items-center gap-2">
            <span class="text-xl">💎</span>
            <div>
                <p class="text-sm font-black text-violet-900 m-0">{{ $activeSubscription->plan_name ?: 'باقة SANUA' }}</p>
                <p class="text-xs font-bold text-violet-500 m-0">ينتهي {{ $activeSubscription->end_date?->format('Y-m-d') }}</p>
            </div>
        </div>
        <a href="{{ route('student.my-subscription') }}" class="sanua-challenge__btn">عرض الباقة</a>
    </div>
    @endif
</div>
@endsection
