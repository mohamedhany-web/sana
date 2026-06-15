@php
    $brand = config('app.name', 'Sana');
    $tr = fn (string $key) => str_replace(':brand', $brand, __('sana_home.'.$key));
    $pub = fn (string $key) => str_replace(':brand', $brand, __('public.'.$key));
@endphp
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
    <title>{{ __('public.certificates_page_title') }} — {{ $brand }}</title>
    <meta name="description" content="{{ $pub('certificates_meta_description') }}">
    <meta name="theme-color" content="#5B21B6">
    <link rel="canonical" href="{{ url('/certificates') }}">
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
                    <span>{{ __('public.certificates_page_title') }}</span>
                </nav>
                <span class="sana-sub-hero__eyebrow"><i class="fas fa-certificate"></i> {{ __('public.certificates_hero_badge') }}</span>
                <h1 class="sana-sub-hero__title">
                    {{ __('public.certificates_hero_title') }}
                    <span class="hl">{{ __('public.certificates_hero_highlight') }}</span>
                </h1>
                <p class="sana-sub-hero__sub">{{ $pub('certificates_hero_sub') }}</p>
                <div class="sana-sub-hero__actions">
                    <a href="{{ route('public.certificates.verify') }}" class="sana-btn sana-btn--yellow">
                        <i class="fas fa-shield-halved"></i> {{ __('public.certificates_verify_cta') }}
                    </a>
                    <a href="{{ route('public.courses') }}" class="sana-btn sana-btn--white-outline">
                        <i class="fas fa-book-open"></i> {{ __('public.certificates_browse_courses') }}
                    </a>
                </div>
            </div>
            <svg class="sana-sub-hero__illus" viewBox="0 0 160 160" fill="none" aria-hidden="true">
                <circle cx="80" cy="80" r="70" stroke="rgba(255,255,255,0.15)" stroke-width="2" stroke-dasharray="6 8"/>
                <rect x="32" y="36" width="96" height="72" rx="12" fill="rgba(255,255,255,0.12)" stroke="rgba(255,255,255,0.3)" stroke-width="1.5"/>
                <circle cx="80" cy="58" r="14" fill="#FBBF24"/>
                <path d="M74 58 L78 62 L86 54" stroke="#92400E" stroke-width="2" stroke-linecap="round"/>
                <path d="M48 78 H112 M48 88 H96 M48 98 H88" stroke="rgba(255,255,255,0.45)" stroke-width="2" stroke-linecap="round"/>
                <path d="M56 120 L80 132 L104 120 V108 H56 Z" fill="rgba(251,191,36,0.35)" stroke="#FBBF24"/>
            </svg>
        </div>
    </div>
</section>

<section class="sana-section">
    <div class="sana-container">
        <div class="sana-cert-grid sana-reveal">
            <article class="sana-cert-card">
                <div class="sana-cert-card__icon"><i class="fas fa-certificate"></i></div>
                <h3>{{ __('public.certificates_type_completion_title') }}</h3>
                <p>{{ __('public.certificates_type_completion_desc') }}</p>
                <ul>
                    <li><i class="fas fa-check-circle"></i>{{ __('public.certificates_type_completion_f1') }}</li>
                    <li><i class="fas fa-check-circle"></i>{{ __('public.certificates_type_completion_f2') }}</li>
                    <li><i class="fas fa-check-circle"></i>{{ __('public.certificates_type_completion_f3') }}</li>
                </ul>
            </article>
            <article class="sana-cert-card">
                <div class="sana-cert-card__icon sana-cert-card__icon--gold"><i class="fas fa-medal"></i></div>
                <h3>{{ __('public.certificates_type_pro_title') }}</h3>
                <p>{{ __('public.certificates_type_pro_desc') }}</p>
                <ul>
                    <li><i class="fas fa-check-circle"></i>{{ __('public.certificates_type_pro_f1') }}</li>
                    <li><i class="fas fa-check-circle"></i>{{ __('public.certificates_type_pro_f2') }}</li>
                    <li><i class="fas fa-check-circle"></i>{{ __('public.certificates_type_pro_f3') }}</li>
                </ul>
            </article>
        </div>
    </div>
</section>

<section class="sana-section sana-section--soft">
    <div class="sana-container sana-reveal">
        <div class="sana-cert-disclaimer">
            <span class="sana-cert-disclaimer__icon"><i class="fas fa-circle-info"></i></span>
            <div>
                <h2>{{ __('public.certificates_disclaimer_title') }}</h2>
                <p>{{ $pub('certificates_disclaimer_body') }}</p>
            </div>
        </div>
    </div>
</section>

<section class="sana-section">
    <div class="sana-container">
        <div class="sana-head sana-head--center sana-reveal" style="margin-bottom:36px">
            <h2 class="sana-head__title">{{ __('public.certificates_how_title') }}</h2>
            <span class="sana-head__line"></span>
            <p class="sana-head__sub">{{ __('public.certificates_how_sub') }}</p>
        </div>
        <div class="sana-cert-steps">
            @foreach([
                ['01', 'user-graduate', 'certificates_step1_title', 'certificates_step1_desc'],
                ['02', 'clipboard-check', 'certificates_step2_title', 'certificates_step2_desc'],
                ['03', 'file-certificate', 'certificates_step3_title', 'certificates_step3_desc'],
            ] as $step)
            <div class="sana-cert-step sana-reveal">
                <span class="sana-cert-step__num">{{ $step[0] }}</span>
                <span class="sana-cert-step__icon"><i class="fas fa-{{ $step[1] }}"></i></span>
                <strong>{{ __('public.'.$step[2]) }}</strong>
                <p>{{ __('public.'.$step[3]) }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

<section class="sana-section">
    <div class="sana-container sana-reveal">
        <div class="sana-cert-verify-box">
            <span class="sana-head__eyebrow"><i class="fas fa-shield-halved"></i> {{ __('public.certificates_panel_title') }}</span>
            <h2 class="sana-head__title" style="margin:12px 0">{{ $pub('certificates_panel_desc') }}</h2>
            <a href="{{ route('public.certificates.verify') }}" class="sana-btn sana-btn--purple" style="margin-top:8px">
                <i class="fas fa-magnifying-glass"></i> {{ __('public.certificates_panel_btn') }}
            </a>
        </div>
    </div>
</section>

<section class="sana-sub-final">
    <div class="sana-container sana-reveal">
        <div class="sana-sub-final__box">
            <h2>{{ __('public.certificates_cta_title') }}</h2>
            <p>{{ $pub('certificates_cta_desc') }}</p>
            <div class="sana-sub-final__actions">
                <a href="{{ route('register') }}" class="sana-btn sana-btn--yellow"><i class="fas fa-user-plus"></i> {{ $tr('nav.get_started') }}</a>
                <a href="{{ route('public.help') }}" class="sana-btn sana-btn--ghost-light"><i class="fas fa-circle-question"></i> {{ __('public.help_page_title') }}</a>
            </div>
        </div>
    </div>
</section>

</main>

@include('landing.sana.footer')
@include('landing.sana.scripts')
@include('partials.pwa-service-worker')
</body>
</html>
