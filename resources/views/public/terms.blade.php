@php
    $brand = config('app.name', 'Sana');
    $tr = fn (string $key) => str_replace(':brand', $brand, __('sana_home.'.$key));
    $pub = fn (string $key) => str_replace(':brand', $brand, __('public.'.$key));
    $familyIcons = ['check-circle', 'chalkboard-user', 'user-lock', 'wallet', 'copyright', 'scale-balanced'];
    $teacherIcons = ['file-signature', 'video', 'shield-halved', 'handshake'];
    $attendanceIcons = ['calendar-check', 'calendar-xmark', 'wifi', 'users'];
    $refundIcons = ['list-check', 'paper-plane', 'ban', 'clock'];
    $relatedLinks = [
        ['route' => 'public.privacy', 'icon' => 'shield-halved', 'label' => 'legal_terms_link_privacy'],
        ['route' => 'tutor.policy', 'icon' => 'chalkboard-teacher', 'label' => 'legal_terms_link_teacher_policy'],
        ['route' => 'public.refund', 'icon' => 'rotate-left', 'label' => 'legal_terms_link_refund'],
        ['route' => 'public.certificates', 'icon' => 'certificate', 'label' => 'legal_terms_link_certificates'],
        ['route' => 'public.contact', 'icon' => 'envelope', 'label' => 'legal_terms_link_contact'],
    ];
@endphp
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
    <title>{{ __('public.terms_page_title') }} — {{ $brand }}</title>
    <meta name="description" content="{{ $pub('legal_terms_meta') }}">
    <meta name="keywords" content="{{ $pub('legal_terms_keywords') }}">
    <meta name="theme-color" content="#5B21B6">
    <link rel="canonical" href="{{ url('/terms') }}">
    <meta property="og:title" content="{{ __('public.terms_page_title') }} — {{ $brand }}">
    <meta property="og:description" content="{{ $pub('legal_terms_meta') }}">
    @include('partials.favicon-links')
    @include('partials.seo-jsonld', ['jsonldType' => 'website'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@800;900&family=Tajawal:wght@500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @include('partials.rtl-base')
    @include('landing.sana.theme')
    @include('landing.sana.courses-catalog-theme')
    @include('landing.sana.subpages-theme')
</head>
<body class="sana-home sana-courses-page" x-data="{ tab: 'family' }">

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
                    <span>{{ __('public.terms_page_title') }}</span>
                </nav>
                <span class="sana-sub-hero__eyebrow"><i class="fas fa-gavel"></i> {{ __('public.terms_page_title') }}</span>
                <h1 class="sana-sub-hero__title">
                    {{ __('public.terms_short') }}
                    <span class="hl">{{ $brand }}</span>
                </h1>
                <p class="sana-sub-hero__sub">{{ $pub('legal_terms_hero_sub') }}</p>
                <div class="sana-sub-hero__actions">
                    <a href="{{ route('public.contact') }}" class="sana-btn sana-btn--yellow">
                        <i class="fas fa-envelope"></i> {{ __('public.contact_page_title') }}
                    </a>
                    <a href="{{ route('public.privacy') }}" class="sana-btn sana-btn--white-outline">
                        <i class="fas fa-shield-halved"></i> {{ __('public.privacy_page_title') }}
                    </a>
                </div>
            </div>
            <svg class="sana-sub-hero__illus" viewBox="0 0 160 160" fill="none" aria-hidden="true">
                <rect x="36" y="32" width="88" height="104" rx="12" fill="rgba(255,255,255,0.1)" stroke="rgba(255,255,255,0.35)" stroke-width="1.5"/>
                <path d="M52 56 H108 M52 72 H96 M52 88 H88 M52 104 H76" stroke="rgba(255,255,255,0.45)" stroke-width="2" stroke-linecap="round"/>
                <circle cx="112" cy="112" r="22" fill="rgba(251,191,36,0.35)" stroke="#FBBF24" stroke-width="1.5"/>
                <path d="M104 112 L110 118 L122 106" stroke="#92400E" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                <circle cx="48" cy="44" r="6" fill="rgba(255,255,255,0.35)"/>
            </svg>
        </div>
    </div>
</section>

<section class="sana-section">
    <div class="sana-container">
        <div class="sana-legal-intro sana-reveal">
            <span class="sana-legal-intro__icon"><i class="fas fa-file-contract"></i></span>
            <p>{!! nl2br(e($pub('legal_terms_intro'))) !!}</p>
        </div>
    </div>
</section>

<section class="sana-section sana-section--soft">
    <div class="sana-container">
        <div class="sana-help-audience sana-reveal">
            <div class="sana-help-audience__toggle sana-legal-tabs" role="tablist">
                <button type="button" role="tab" :class="{ 'is-active': tab === 'family' }" @click="tab = 'family'">
                    <i class="fas fa-user-graduate"></i> {{ __('public.legal_terms_tab_family') }}
                </button>
                <button type="button" role="tab" :class="{ 'is-active': tab === 'teacher' }" @click="tab = 'teacher'">
                    <i class="fas fa-chalkboard-teacher"></i> {{ __('public.legal_terms_tab_teacher') }}
                </button>
                <button type="button" role="tab" :class="{ 'is-active': tab === 'attendance' }" @click="tab = 'attendance'">
                    <i class="fas fa-calendar-check"></i> {{ __('public.legal_terms_tab_attendance') }}
                </button>
                <button type="button" role="tab" :class="{ 'is-active': tab === 'refund' }" @click="tab = 'refund'">
                    <i class="fas fa-rotate-left"></i> {{ __('public.legal_terms_tab_refund') }}
                </button>
            </div>

            <div x-show="tab === 'family'" x-cloak>
                <p class="sana-help-audience__intro">{{ $pub('legal_terms_family_intro') }}</p>
                <div class="sana-legal-grid">
                    @foreach(range(1, 6) as $i)
                    <article class="sana-legal-card sana-reveal @if($i === 6) is-wide @endif">
                        <div class="sana-legal-card__head">
                            <span class="sana-legal-card__icon @if($i % 2 === 0) sana-legal-card__icon--gold @endif">
                                <i class="fas fa-{{ $familyIcons[$i - 1] }}"></i>
                            </span>
                            <h2>{{ __('public.legal_terms_family_s'.$i.'_title') }}</h2>
                        </div>
                        <p>{!! nl2br(e($pub('legal_terms_family_s'.$i.'_body'))) !!}</p>
                    </article>
                    @endforeach
                </div>
            </div>

            <div x-show="tab === 'teacher'" x-cloak>
                <p class="sana-help-audience__intro">{{ $pub('legal_terms_teacher_intro') }}</p>
                <div class="sana-legal-grid">
                    @foreach(range(1, 4) as $i)
                    <article class="sana-legal-card sana-reveal">
                        <div class="sana-legal-card__head">
                            <span class="sana-legal-card__icon @if($i % 2 === 0) sana-legal-card__icon--gold @endif">
                                <i class="fas fa-{{ $teacherIcons[$i - 1] }}"></i>
                            </span>
                            <h2>{{ __('public.legal_terms_teacher_s'.$i.'_title') }}</h2>
                        </div>
                        <p>{!! nl2br(e($pub('legal_terms_teacher_s'.$i.'_body'))) !!}</p>
                    </article>
                    @endforeach
                </div>
                <p class="sana-legal-tab-note">
                    <a href="{{ route('tutor.policy') }}"><i class="fas fa-arrow-left"></i> {{ __('public.legal_terms_link_teacher_policy') }}</a>
                </p>
            </div>

            <div x-show="tab === 'attendance'" x-cloak>
                <p class="sana-help-audience__intro">{{ $pub('legal_terms_attendance_intro') }}</p>
                <div class="sana-legal-grid">
                    @foreach(range(1, 4) as $i)
                    <article class="sana-legal-card sana-reveal">
                        <div class="sana-legal-card__head">
                            <span class="sana-legal-card__icon @if($i % 2 === 0) sana-legal-card__icon--gold @endif">
                                <i class="fas fa-{{ $attendanceIcons[$i - 1] }}"></i>
                            </span>
                            <h2>{{ __('public.legal_terms_attendance_s'.$i.'_title') }}</h2>
                        </div>
                        <p>{!! nl2br(e($pub('legal_terms_attendance_s'.$i.'_body'))) !!}</p>
                    </article>
                    @endforeach
                </div>
            </div>

            <div x-show="tab === 'refund'" x-cloak>
                <p class="sana-help-audience__intro">{{ $pub('legal_terms_refund_intro') }}</p>
                <div class="sana-legal-grid">
                    @foreach(range(1, 4) as $i)
                    <article class="sana-legal-card sana-reveal">
                        <div class="sana-legal-card__head">
                            <span class="sana-legal-card__icon @if($i % 2 === 0) sana-legal-card__icon--gold @endif">
                                <i class="fas fa-{{ $refundIcons[$i - 1] }}"></i>
                            </span>
                            <h2>{{ __('public.legal_terms_refund_s'.$i.'_title') }}</h2>
                        </div>
                        <p>{!! nl2br(e($pub('legal_terms_refund_s'.$i.'_body'))) !!}</p>
                    </article>
                    @endforeach
                </div>
                <p class="sana-legal-tab-note">
                    <a href="{{ route('public.refund') }}"><i class="fas fa-arrow-left"></i> {{ __('public.legal_terms_refund_full_link') }}</a>
                </p>
            </div>
        </div>
    </div>
</section>

<section class="sana-section">
    <div class="sana-container">
        <div class="sana-legal-related sana-reveal">
            <div class="sana-head sana-head--center" style="margin-bottom:28px">
                <h2 class="sana-head__title">{{ __('public.legal_terms_related_title') }}</h2>
                <span class="sana-head__line"></span>
                <p class="sana-head__sub">{{ __('public.legal_terms_related_sub') }}</p>
            </div>
            <div class="sana-legal-related__grid">
                @foreach($relatedLinks as $link)
                <a href="{{ route($link['route']) }}" class="sana-legal-related-card">
                    <span class="sana-legal-related-card__icon"><i class="fas fa-{{ $link['icon'] }}"></i></span>
                    <strong>{{ __('public.'.$link['label']) }}</strong>
                    <span>{{ __('public.services_read_more') }} <i class="fas fa-arrow-left"></i></span>
                </a>
                @endforeach
            </div>
        </div>
    </div>
</section>

<section class="sana-sub-final">
    <div class="sana-container sana-reveal">
        <div class="sana-sub-final__box">
            <h2>{{ __('public.legal_cta_title') }}</h2>
            <p>{{ __('public.legal_cta_desc') }}</p>
            <div class="sana-sub-final__actions">
                <a href="{{ route('public.contact') }}" class="sana-btn sana-btn--yellow">
                    <i class="fas fa-paper-plane"></i> {{ __('public.contact_page_title') }}
                </a>
                <a href="{{ route('home') }}" class="sana-btn sana-btn--ghost-light">
                    <i class="fas fa-home"></i> {{ __('public.home') }}
                </a>
            </div>
        </div>
    </div>
</section>

</main>

@include('landing.sana.footer')
@include('landing.sana.scripts')
@include('partials.pwa-service-worker')
<style>[x-cloak]{display:none!important}</style>
</body>
</html>
