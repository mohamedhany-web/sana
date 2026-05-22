@php
    $brand = config('app.name', 'Sana');
    $bc = config('brand.colors');
    $tr = fn (string $key) => str_replace(':brand', $brand, __('sana_home.'.$key));
    $pub = fn (string $key) => str_replace(':brand', $brand, __('public.'.$key));
    $privacyIcons = ['database', 'file-lines', 'lock', 'share-nodes', 'user-check', 'cookie-bite', 'arrows-rotate'];
@endphp
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
    <title>{{ __('public.privacy_page_title') }} - {{ $brand }}</title>
    <meta name="description" content="{{ $pub('legal_privacy_meta') }}">
    <meta name="keywords" content="{{ $pub('legal_privacy_keywords') }}">
    <meta name="theme-color" content="{{ $bc['blue'] }}">
    <link rel="canonical" href="{{ url('/privacy') }}">
    <meta property="og:title" content="{{ __('public.privacy_page_title') }} - {{ $brand }}">
    <meta property="og:description" content="{{ $pub('legal_privacy_meta') }}">
    <meta property="og:image" content="{{ asset('images/og-image.jpg') }}">
    @include('partials.favicon-links')
    @include('partials.seo-jsonld', ['jsonldType' => 'website'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config={theme:{extend:{colors:{edu:{primary:'{{ $bc['blue'] }}',purple:'{{ $bc['purple'] }}',accent:'{{ $bc['yellow'] }}'}}}}}</script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @include('landing.eduvalt.theme')
    @include('landing.eduvalt.courses-page')
    @include('landing.eduvalt.support-pages')
    @include('partials.rtl-base')
</head>
<body class="antialiased bg-white">
<div id="edu-preloader" aria-hidden="true"><div class="edu-preloader-spinner"></div></div>
<div id="scroll-progress"></div>

@include('landing.eduvalt.navbar')

<main class="pt-[76px] lg:pt-[84px]">

<section class="edu-support-hero relative overflow-hidden py-10 lg:py-14">
    <div class="absolute top-16 start-0 w-64 h-64 rounded-full bg-sky-200/40 blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-0 end-0 w-80 h-80 rounded-full bg-blue-200/30 blur-3xl pointer-events-none"></div>
    <div class="edu-container-full relative z-10">
        <div class="edu-courses-inner">
            <div class="max-w-3xl mx-auto text-center reveal">
                <nav class="edu-breadcrumb justify-center mb-4" aria-label="مسار التنقل">
                    <a href="{{ route('home') }}">{{ $tr('nav.home') }}</a>
                    <i class="fas fa-chevron-left text-[10px] opacity-50"></i>
                    <span class="text-slate-800 font-semibold">{{ __('public.privacy_page_title') }}</span>
                </nav>
                <span class="edu-badge mb-4"><i class="fas fa-shield-halved"></i> {{ __('public.privacy_page_title') }}</span>
                <h1 class="edu-section-title text-slate-900">
                    {{ __('public.privacy_short') }}
                    @include('landing.eduvalt.partials.title-mark', ['text' => $brand])
                </h1>
                <p class="text-slate-600 leading-8 mt-3 text-sm lg:text-base max-w-2xl mx-auto">
                    {{ $pub('legal_privacy_hero_sub') }}
                </p>
                <div class="edu-hero-actions mt-6 justify-center">
                    <a href="{{ route('public.contact') }}" class="edu-btn-primary">
                        <i class="fas fa-envelope"></i>
                        {{ __('public.contact_page_title') }}
                    </a>
                    <a href="{{ route('public.terms') }}" class="edu-btn-outline">
                        <i class="fas fa-file-contract"></i>
                        {{ __('public.terms_page_title') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-10 lg:py-12 bg-white border-t border-slate-100">
    <div class="edu-container-full">
        <div class="edu-courses-inner max-w-4xl mx-auto">
            <div class="edu-card p-6 sm:p-8 flex flex-col sm:flex-row gap-5 items-start reveal">
                <span class="w-14 h-14 rounded-2xl flex items-center justify-center text-white text-xl shrink-0" style="background:linear-gradient(135deg,var(--edu-primary),var(--edu-purple))">
                    <i class="fas fa-lock"></i>
                </span>
                <p class="text-slate-700 leading-[1.9] text-sm lg:text-base flex-1">
                    {!! nl2br(e($pub('legal_privacy_intro'))) !!}
                </p>
            </div>
        </div>
    </div>
</section>

<section class="py-12 lg:py-16 bg-slate-50 border-t border-slate-100">
    <div class="edu-container-full">
        <div class="edu-courses-inner max-w-5xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 lg:gap-5">
                @foreach(range(1, 7) as $i)
                <article class="edu-legal-card reveal @if($i === 7) is-wide @endif @if($i > 1) s{{ min($i - 1, 3) }} @endif">
                    <div class="flex items-start gap-3 mb-3">
                        <span class="w-11 h-11 rounded-xl flex items-center justify-center text-white text-base shrink-0" style="background:{{ $i % 2 === 0 ? 'var(--edu-accent-dark)' : 'var(--edu-primary)' }}">
                            <i class="fas fa-{{ $privacyIcons[$i - 1] }}"></i>
                        </span>
                        <h2 class="text-base sm:text-lg font-extrabold text-slate-900 leading-snug pt-1 flex-1">
                            {{ __('public.legal_privacy_s'.$i.'_title') }}
                        </h2>
                    </div>
                    <p class="text-slate-600 leading-relaxed text-sm sm:text-[0.9375rem]">
                        {!! nl2br(e($pub('legal_privacy_s'.$i.'_body'))) !!}
                    </p>
                </article>
                @endforeach
            </div>
        </div>
    </div>
</section>

<section class="py-12 lg:py-16 bg-white">
    <div class="edu-container-full">
        <div class="edu-courses-inner">
            <div class="edu-card p-8 sm:p-10 text-center max-w-3xl mx-auto reveal">
                <span class="edu-badge mb-4"><i class="fas fa-headset"></i> {{ __('public.support') }}</span>
                <h2 class="edu-section-title text-slate-900 mb-3">{{ __('public.legal_privacy_cta_title') }}</h2>
                <p class="text-slate-600 leading-8 mb-8 max-w-xl mx-auto">{{ $pub('legal_privacy_cta_desc') }}</p>
                <div class="edu-hero-actions justify-center">
                    <a href="{{ route('public.contact') }}" class="edu-btn-primary">
                        <i class="fas fa-paper-plane"></i>
                        {{ __('public.contact_page_title') }}
                    </a>
                    <a href="{{ route('home') }}" class="edu-btn-outline">
                        <i class="fas fa-home"></i>
                        {{ __('public.home') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

</main>

@include('landing.eduvalt.footer')

<script>
(function () {
    var nav = document.getElementById('edu-nav');
    function onScroll() {
        var y = window.scrollY || document.documentElement.scrollTop;
        if (nav) nav.classList.toggle('is-scrolled', y > 20);
        var bar = document.getElementById('scroll-progress');
        var h = document.documentElement.scrollHeight - window.innerHeight;
        if (bar) bar.style.width = (h > 0 ? (y / h) * 100 : 0) + '%';
    }
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
    window.addEventListener('load', function () {
        document.getElementById('edu-preloader')?.classList.add('is-done');
    });
    setTimeout(function () {
        document.getElementById('edu-preloader')?.classList.add('is-done');
    }, 2000);
    document.getElementById('edu-mobile-toggle')?.addEventListener('click', function () {
        document.getElementById('edu-mobile-menu')?.classList.toggle('hidden');
    });
    var reveals = document.querySelectorAll('.reveal');
    if ('IntersectionObserver' in window) {
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (e) {
                if (e.isIntersecting) { e.target.classList.add('revealed'); io.unobserve(e.target); }
            });
        }, { threshold: 0.06, rootMargin: '0px 0px -40px 0px' });
        reveals.forEach(function (el) { io.observe(el); });
    } else {
        reveals.forEach(function (el) { el.classList.add('revealed'); });
    }
})();
</script>
@include('partials.pwa-service-worker')
</body>
</html>
