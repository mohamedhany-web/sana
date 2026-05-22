@php
    $brand = config('brand.name', config('app.name', 'Sana'));
    $bc = config('brand.colors');
    $tr = fn (string $key) => str_replace(':brand', $brand, __('sana_home.'.$key));
    $hasImage = (bool) $siteService->publicImageUrl();
    $bodyParagraphs = collect(preg_split('/\r\n|\r|\n/', trim((string) $siteService->body)))->filter();
@endphp
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
    <title>{{ $siteService->name }} - {{ __('public.services_page_title') }} | {{ $brand }}</title>
    <meta name="description" content="{{ Str::limit(strip_tags($siteService->summary ?: $siteService->body), 160) }}">
    <meta name="theme-color" content="{{ $bc['blue'] }}">
    <link rel="canonical" href="{{ route('public.services.show', $siteService) }}">
    <meta property="og:title" content="{{ $siteService->name }} - {{ $brand }}">
    <meta property="og:description" content="{{ Str::limit(strip_tags($siteService->summary ?: $siteService->body), 160) }}">
    @if($hasImage)
        <meta property="og:image" content="{{ $siteService->publicImageUrl() }}">
    @endif
    @include('partials.favicon-links')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @include('landing.eduvalt.theme')
    @include('landing.eduvalt.courses-page')
    @include('landing.eduvalt.services-page')
    @include('partials.rtl-base')
</head>
<body class="antialiased bg-white">
<div id="edu-preloader" aria-hidden="true"><div class="edu-preloader-spinner"></div></div>
<div id="scroll-progress"></div>

@include('landing.eduvalt.navbar')

<main class="pt-[76px] lg:pt-[84px]">

{{-- هيرو بعرض الصفحة --}}
<section class="edu-service-show-hero py-8 lg:py-12">
    <div class="edu-services-hero__blob edu-services-hero__blob--1" aria-hidden="true"></div>
    <div class="edu-services-hero__blob edu-services-hero__blob--2" aria-hidden="true"></div>
    <div class="edu-services-hero__blob edu-services-hero__blob--3" aria-hidden="true"></div>

    <div class="edu-container-full relative z-10">
        <div class="edu-courses-inner">
            <nav class="edu-breadcrumb mb-6 reveal" aria-label="مسار التنقل">
                <a href="{{ route('home') }}">{{ $tr('nav.home') }}</a>
                <i class="fas fa-chevron-left text-[10px] opacity-50"></i>
                <a href="{{ route('public.services.index') }}">{{ __('public.services_page_title') }}</a>
                <i class="fas fa-chevron-left text-[10px] opacity-50"></i>
                <span class="text-slate-800 font-semibold">{{ Str::limit($siteService->name, 56) }}</span>
            </nav>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 items-center">
                <div class="reveal order-2 lg:order-1">
                    <span class="edu-badge mb-4"><i class="fas fa-concierge-bell"></i> {{ __('public.services_page_title') }}</span>
                    <h1 class="text-3xl sm:text-4xl lg:text-[2.5rem] font-extrabold text-slate-900 leading-tight mb-4">
                        {{ $siteService->name }}
                    </h1>
                    @if($siteService->summary)
                        <p class="text-slate-600 text-base sm:text-lg leading-8 mb-6 max-w-xl">{{ $siteService->summary }}</p>
                    @endif

                    <div class="flex flex-wrap gap-2 mb-6">
                        <span class="edu-service-show-chip">
                            <i class="fas fa-check-circle text-[var(--edu-primary)]"></i>
                            {{ $brand }}
                        </span>
                        <span class="edu-service-show-chip">
                            <i class="fas fa-headset text-[var(--edu-purple)]"></i>
                            {{ __('public.services_support_hint') }}
                        </span>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('public.contact') }}" class="edu-btn-primary">
                            {{ __('public.contact_us') }}
                            <i class="fas fa-arrow-left text-sm"></i>
                        </a>
                        <a href="{{ route('public.services.index') }}" class="edu-btn-outline">
                            <i class="fas fa-arrow-right text-sm"></i>
                            {{ __('public.services_back_to_list') }}
                        </a>
                    </div>
                </div>

                <div class="reveal order-1 lg:order-2">
                    <div class="edu-service-show-media">
                        @if($hasImage)
                            <img src="{{ $siteService->publicImageUrl() }}" alt="{{ $siteService->name }}" loading="eager">
                        @else
                            <div class="edu-service-show-media__placeholder">
                                <i class="fas fa-layer-group"></i>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- المحتوى + الشريط الجانبي — عرض كامل --}}
<section class="edu-service-show-body py-10 lg:py-14">
    <div class="edu-container-full">
        <div class="edu-courses-inner">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10 items-start">
                <article class="lg:col-span-8 reveal">
                    <div class="edu-service-show-article">
                        <h2 class="text-lg font-bold text-slate-900 mb-5 flex items-center gap-2">
                            <span class="w-9 h-9 rounded-lg flex items-center justify-center text-sm" style="background:var(--edu-primary-light);color:var(--edu-primary)">
                                <i class="fas fa-align-right"></i>
                            </span>
                            {{ __('public.services_read_more') }}
                        </h2>
                        <div class="edu-service-show-prose">
                            @if($bodyParagraphs->isNotEmpty())
                                @foreach($bodyParagraphs as $para)
                                    <p>{{ $para }}</p>
                                @endforeach
                            @else
                                <p class="text-slate-400">{{ __('public.services_empty_desc') }}</p>
                            @endif
                        </div>
                    </div>
                </article>

                <aside class="lg:col-span-4 reveal">
                    <div class="edu-service-show-sidebar space-y-4">
                        <div class="edu-service-show-cta-card">
                            <div class="edu-service-show-cta-head">
                                <p class="text-xs font-bold uppercase tracking-wide opacity-90 mb-1">{{ __('public.services_cta_badge') }}</p>
                                <p class="text-lg font-extrabold">{{ __('public.services_cta_title') }}</p>
                            </div>
                            <div class="edu-service-show-cta-body">
                                <p class="text-sm text-slate-600 leading-7 mb-4">{{ __('public.services_cta_text') }}</p>
                                <a href="{{ route('public.contact') }}" class="edu-btn-primary w-full justify-center text-sm mb-3">
                                    {{ __('public.contact_us') }}
                                    <i class="fas fa-arrow-left text-xs"></i>
                                </a>
                                <a href="{{ route('public.pricing') }}" class="edu-btn-outline w-full justify-center text-sm">
                                    {{ $tr('platform.cta_services') }}
                                    <i class="fas fa-arrow-left text-xs"></i>
                                </a>
                            </div>
                        </div>

                        <a href="{{ route('public.courses') }}" class="edu-card flex items-center gap-3 p-4 hover:border-[var(--edu-primary)] transition-colors group">
                            <span class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0" style="background:var(--edu-accent-light);color:var(--edu-accent-dark)">
                                <i class="fas fa-graduation-cap"></i>
                            </span>
                            <span class="flex-1 min-w-0">
                                <span class="block font-bold text-slate-900 text-sm group-hover:text-[var(--edu-primary)]">{{ __('public.browse_courses') }}</span>
                                <span class="block text-xs text-slate-500 mt-0.5">{{ $tr('courses.subtitle') }}</span>
                            </span>
                            <i class="fas fa-arrow-left text-[var(--edu-primary)] text-sm opacity-60 group-hover:opacity-100"></i>
                        </a>
                    </div>
                </aside>
            </div>
        </div>
    </div>
</section>

@if($others->count() > 0)
<section class="py-10 lg:py-14 bg-white border-t border-slate-100">
    <div class="edu-container-full">
        <div class="edu-courses-inner">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-3 mb-6 reveal">
                <div>
                    <span class="edu-sub-title">{{ __('public.services_page_title') }}</span>
                    <h2 class="edu-section-title text-slate-900 text-xl">{{ __('public.services_more_title') }}</h2>
                </div>
                <a href="{{ route('public.services.index') }}" class="edu-btn-outline text-sm shrink-0">
                    {{ __('public.services_back_to_list') }}
                    <i class="fas fa-arrow-left text-xs"></i>
                </a>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 reveal">
                @foreach($others as $o)
                    <a href="{{ route('public.services.show', $o) }}" class="edu-service-related-card group">
                        <span class="edu-service-related-thumb">
                            @if($o->publicImageUrl())
                                <img src="{{ $o->publicImageUrl() }}" alt="" loading="lazy">
                            @else
                                <i class="fas fa-layer-group"></i>
                            @endif
                        </span>
                        <span class="flex-1 min-w-0">
                            <span class="block font-bold text-slate-900 text-sm mb-1 group-hover:text-[var(--edu-primary)] transition-colors line-clamp-2">{{ $o->name }}</span>
                            <span class="block text-xs text-slate-500 line-clamp-2 leading-6">{{ Str::limit(strip_tags($o->summary ?: $o->body), 64) }}</span>
                        </span>
                        <i class="fas fa-arrow-left text-[var(--edu-primary)] text-sm shrink-0 opacity-50 group-hover:opacity-100"></i>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif

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
    var io = new IntersectionObserver(function (entries) {
        entries.forEach(function (e) {
            if (e.isIntersecting) { e.target.classList.add('revealed'); io.unobserve(e.target); }
        });
    }, { threshold: 0.06, rootMargin: '0px 0px -32px 0px' });
    document.querySelectorAll('.reveal').forEach(function (el) { io.observe(el); });
    var pre = document.getElementById('edu-preloader');
    if (pre) window.addEventListener('load', function () { pre.style.opacity = '0'; setTimeout(function () { pre.remove(); }, 400); });
})();
</script>
</body>
</html>
