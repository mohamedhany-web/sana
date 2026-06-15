@php
    $brand = config('app.name', 'Sana');
    $tr = fn (string $key) => str_replace(':brand', $brand, __('sana_home.'.$key));
    $pub = fn (string $key) => str_replace(':brand', $brand, __('public.'.$key));
    $refundIcons = ['list-check', 'scale-balanced', 'paper-plane', 'clock'];
@endphp
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
    <title>{{ __('public.legal_refund_page_title') }} — {{ $brand }}</title>
    <meta name="description" content="{{ $pub('legal_refund_meta') }}">
    <meta name="theme-color" content="#5B21B6">
    <link rel="canonical" href="{{ url('/refund') }}">
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
                <nav class="sana-sub-hero__breadcrumb" aria-label="???? ??????">
                    <a href="{{ route('home') }}">{{ $tr('nav.home') }}</a>
                    <i class="fas fa-chevron-left" style="font-size:10px;opacity:.5"></i>
                    <a href="{{ route('public.terms') }}">{{ __('public.terms_page_title') }}</a>
                    <i class="fas fa-chevron-left" style="font-size:10px;opacity:.5"></i>
                    <span>{{ __('public.legal_refund_page_title') }}</span>
                </nav>
                <span class="sana-sub-hero__eyebrow"><i class="fas fa-rotate-left"></i> {{ __('public.legal_refund_page_title') }}</span>
                <h1 class="sana-sub-hero__title">
                    {{ __('public.legal_refund_page_title') }}
                    <span class="hl">{{ $brand }}</span>
                </h1>
                <p class="sana-sub-hero__sub">{{ $pub('legal_refund_hero_sub') }}</p>
                <div class="sana-sub-hero__actions">
                    <a href="{{ route('public.contact') }}" class="sana-btn sana-btn--yellow">
                        <i class="fas fa-envelope"></i> {{ __('public.contact_page_title') }}
                    </a>
                    <a href="{{ route('public.terms') }}" class="sana-btn sana-btn--white-outline">
                        <i class="fas fa-file-contract"></i> {{ __('public.terms_page_title') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="sana-section">
    <div class="sana-container">
        <div class="sana-legal-intro sana-reveal">
            <span class="sana-legal-intro__icon"><i class="fas fa-rotate-left"></i></span>
            <p>{!! nl2br(e($pub('legal_refund_intro'))) !!}</p>
        </div>
    </div>
</section>

<section class="sana-section sana-section--soft">
    <div class="sana-container">
        <div class="sana-legal-grid">
            @foreach(range(1, 4) as $i)
            <article class="sana-legal-card sana-reveal @if($i === 4) is-wide @endif">
                <div class="sana-legal-card__head">
                    <span class="sana-legal-card__icon @if($i % 2 === 0) sana-legal-card__icon--gold @endif">
                        <i class="fas fa-{{ $refundIcons[$i - 1] }}"></i>
                    </span>
                    <h2>{{ __('public.legal_refund_s'.$i.'_title') }}</h2>
                </div>
                <p>{!! nl2br(e($pub('legal_refund_s'.$i.'_body'))) !!}</p>
            </article>
            @endforeach
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
                <a href="{{ route('public.terms') }}" class="sana-btn sana-btn--ghost-light">
                    <i class="fas fa-file-contract"></i> {{ __('public.terms_page_title') }}
                </a>
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
