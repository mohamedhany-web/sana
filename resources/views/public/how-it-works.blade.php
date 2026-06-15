@php
    $brand = config('app.name', 'Sana');
    $tr = fn (string $key) => str_replace(':brand', $brand, __('sana_home.'.$key));
    $pub = fn (string $key) => str_replace(':brand', $brand, __('public.'.$key));
    $cta = \App\Support\PublicSiteCta::payload();
    $hasPublishedCourses = $hasPublishedCourses ?? \App\Support\PublicCourseCatalog::hasPublicCourses();
    $hasPublicInstructors = $hasPublicInstructors ?? \App\Support\PublicInstructorCatalog::hasPublicInstructors();
    $steps = [
        ['icon' => 'fa-clipboard-check', 'title' => __('public.how_it_works_step_1_title'), 'desc' => __('public.how_it_works_step_1_desc')],
        ['icon' => 'fa-user-check', 'title' => __('public.how_it_works_step_2_title'), 'desc' => __('public.how_it_works_step_2_desc')],
        ['icon' => 'fa-video', 'title' => __('public.how_it_works_step_3_title'), 'desc' => __('public.how_it_works_step_3_desc')],
        ['icon' => 'fa-chart-line', 'title' => __('public.how_it_works_step_4_title'), 'desc' => __('public.how_it_works_step_4_desc')],
    ];
@endphp
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
    <title>{{ __('public.how_it_works_page_title') }} — {{ $brand }}</title>
    <meta name="description" content="{{ $pub('how_it_works_meta') }}">
    <meta name="theme-color" content="#5B21B6">
    <link rel="canonical" href="{{ url('/how-it-works') }}">
    @include('partials.favicon-links')
    @include('partials.seo-jsonld', ['jsonldType' => 'website'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@800;900&family=Tajawal:wght@500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @include('partials.rtl-base')
    @include('landing.sana.theme')
    @include('landing.sana.courses-catalog-theme')
    @include('landing.sana.subpages-theme')
</head>
<body class="sana-home sana-courses-page">

<div id="sana-scroll-progress"></div>
@include('landing.sana.navbar')

<main class="sana-sub-page">

<section class="sana-sub-hero">
    <div class="sana-container">
        <div class="sana-sub-hero__grid sana-reveal">
            <div class="sana-sub-hero__content">
                <nav class="sana-sub-hero__breadcrumb" aria-label="مسار التنقل">
                    <a href="{{ route('home') }}">{{ $tr('nav.home') }}</a>
                    <i class="fas fa-chevron-left" style="font-size:10px;opacity:.5"></i>
                    <span>{{ __('public.how_it_works_page_title') }}</span>
                </nav>
                <span class="sana-sub-hero__eyebrow"><i class="fas fa-route"></i> {{ __('public.how_it_works_hero_badge') }}</span>
                <h1 class="sana-sub-hero__title">
                    {{ __('public.how_it_works_page_title') }}
                    <span class="hl">{{ $brand }}</span>
                </h1>
                <p class="sana-sub-hero__sub">{{ $pub('how_it_works_hero_sub') }}</p>
                <div class="sana-sub-hero__actions">
                    @include('landing.sana.partials.site-cta-buttons')
                </div>
            </div>
        </div>
    </div>
</section>

<section class="sana-section sana-section--soft">
    <div class="sana-container">
        <div class="sana-journey-m sana-journey-m--detailed sana-reveal">
            <div class="sana-journey-m__line" aria-hidden="true"></div>
            @foreach($steps as $i => $step)
            <article class="sana-journey-m__step sana-journey-m__step--detail">
                <div class="sana-journey-m__icon"><i class="fas {{ $step['icon'] }}"></i></div>
                <div>
                    <span class="sana-journey-m__num">{{ $i + 1 }}</span>
                    <strong>{{ $step['title'] }}</strong>
                    <p>{{ $step['desc'] }}</p>
                </div>
            </article>
            @endforeach
        </div>
    </div>
</section>

<section class="sana-section">
    <div class="sana-container">
        <div class="sana-head sana-head--center sana-reveal" style="margin-bottom:28px">
            <h2 class="sana-head__title">{{ __('public.how_it_works_packages_title') }}</h2>
            <span class="sana-head__line"></span>
            <p class="sana-head__sub">{{ __('public.how_it_works_packages_desc') }}</p>
        </div>
        <div class="sana-reveal" style="display:flex;flex-wrap:wrap;gap:12px;justify-content:center;margin-bottom:16px">
            <a href="{{ route('public.pricing') }}" class="sana-btn sana-btn--yellow">
                <i class="fas fa-layer-group"></i> {{ __('public.how_it_works_packages_cta') }}
            </a>
            @if($hasPublishedCourses)
            <a href="{{ route('public.courses') }}" class="sana-btn sana-btn--outline-purple">
                <i class="fas fa-book-open"></i> {{ $tr('hero.cta_courses') }}
            </a>
            @endif
            @if($hasPublicInstructors)
            <a href="{{ route('public.instructors.index') }}" class="sana-btn sana-btn--outline-purple">
                <i class="fas fa-chalkboard-user"></i> {{ __('public.courses_launch_cta_tutors') }}
            </a>
            @endif
        </div>
        @unless($hasPublicInstructors)
        <p class="sana-reveal" style="text-align:center;color:var(--muted);font-size:0.88rem;max-width:48ch;margin:0 auto">
            <i class="fas fa-info-circle"></i> {{ __('public.how_it_works_teachers_note') }}
        </p>
        @endunless
    </div>
</section>

<section class="sana-section sana-section--soft">
    <div class="sana-container sana-reveal" style="text-align:center">
        @include('landing.sana.partials.site-cta-buttons', ['class' => 'sana-site-cta--center'])
        <p style="margin-top:20px;font-size:0.88rem;color:var(--muted)">
            <a href="{{ route('public.faq') }}">{{ __('public.how_it_works_faq_link') }}</a>
            ·
            <a href="{{ route('public.help') }}">{{ __('public.how_it_works_help_link') }}</a>
        </p>
    </div>
</section>

</main>

@include('landing.sana.footer')
@include('landing.sana.scripts')
@include('partials.pwa-service-worker')
<style>
.sana-journey-m--detailed .sana-journey-m__step--detail { align-items: flex-start; text-align: start; gap: 16px; }
.sana-journey-m--detailed .sana-journey-m__step--detail p { margin: 6px 0 0; color: var(--muted); font-size: 0.9rem; line-height: 1.65; max-width: 42ch; }
.sana-journey-m--detailed .sana-journey-m__step--detail strong { display: block; font-size: 1.05rem; }
.sana-journey-m__num { display: inline-block; font-size: 0.72rem; font-weight: 800; color: var(--p); margin-bottom: 4px; }
.sana-site-cta--center { justify-content: center; }
</style>
</body>
</html>
