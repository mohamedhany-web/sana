@php
    $brand = config('app.name', 'Sana');
    $bc = config('brand.colors');
    $tr = fn (string $key) => str_replace(':brand', $brand, __('sana_home.'.$key));
    $p = fn (string $key) => str_replace(':brand', $brand, __('sana_pricing.'.$key));
    $packages = __('sana_pricing.packages');
    $packageOrder = ['individual', 'group', 'live', 'recorded'];
    $packageNames = __('sana_pricing.package_names');
    $compareRows = __('sana_pricing.compare_rows');
    $ctaRoutes = [
        'individual' => route('register'),
        'group' => route('register'),
        'live' => route('public.courses'),
        'recorded' => route('public.courses'),
    ];
@endphp
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
    <title>{{ $p('meta_title') }} - {{ $brand }}</title>
    <meta name="description" content="{{ $p('meta_description') }}">
    <meta name="theme-color" content="{{ $bc['blue'] }}">
    <link rel="canonical" href="{{ url('/pricing') }}">
    @include('partials.favicon-links')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @include('landing.eduvalt.theme')
    @include('landing.eduvalt.courses-page')
    @include('landing.eduvalt.pricing-page')
    @include('partials.rtl-base')
</head>
<body class="antialiased bg-white">
<div id="edu-preloader" aria-hidden="true"><div class="edu-preloader-spinner"></div></div>
<div id="scroll-progress"></div>

@include('landing.eduvalt.navbar')

<main class="pt-[76px] lg:pt-[84px]">

{{-- Hero --}}
<section class="edu-courses-page-hero py-8 lg:py-12">
    <div class="edu-container-full">
        <div class="edu-courses-inner text-center reveal">
            <nav class="edu-breadcrumb justify-center mb-4" aria-label="مسار التنقل">
                <a href="{{ route('home') }}">{{ $tr('nav.home') }}</a>
                <i class="fas fa-chevron-left text-[10px] opacity-50"></i>
                <span class="text-slate-800 font-semibold">{{ $p('meta_title') }}</span>
            </nav>
            <div class="edu-pricing-types-strip mb-5">
                @foreach(__('sana_pricing.types_strip') as $typeLabel)
                    <span class="edu-pricing-type-pill">{{ $typeLabel }}</span>
                @endforeach
            </div>
            <span class="edu-badge mb-4"><i class="fas fa-layer-group"></i> {{ $p('hero_badge') }}</span>
            <h1 class="edu-section-title text-slate-900 max-w-3xl mx-auto">
                {{ $p('hero_title') }}
                @include('landing.eduvalt.partials.title-mark', ['text' => $p('hero_highlight')])
            </h1>
            <p class="text-slate-600 leading-8 mt-4 max-w-3xl mx-auto text-sm lg:text-base">{{ $p('hero_sub') }}</p>
            <p class="text-slate-800 font-semibold leading-8 mt-5 max-w-3xl mx-auto text-sm lg:text-base">{{ $p('hero_tagline') }}</p>
            <p class="text-slate-500 text-xs sm:text-sm mt-4 max-w-2xl mx-auto">{{ $p('hero_note') }}</p>
            <div class="flex flex-wrap justify-center gap-3 mt-8">
                <a href="#packages" class="edu-btn-primary text-sm">{{ $p('cta_book') }}</a>
                <a href="#compare" class="edu-btn-outline text-sm">ملخّص الباقات</a>
            </div>
        </div>
    </div>
</section>

{{-- Pillars --}}
<section class="py-10 lg:py-12 bg-white border-t border-slate-100">
    <div class="edu-container">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 reveal">
            @foreach(__('sana_pricing.pillars') as $i => $pillar)
            <div class="edu-pricing-pillar @if($i > 0) s{{ min($i, 3) }} @endif">
                <div class="edu-pricing-pillar-icon" style="background:linear-gradient(135deg,var(--edu-primary),var(--edu-purple))">
                    <i class="fas {{ $pillar['icon'] }}"></i>
                </div>
                <p class="font-extrabold text-slate-900 text-sm mb-1">{{ $pillar['title'] }}</p>
                <p class="text-xs text-slate-500 leading-6">{{ $pillar['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Idea --}}
<section class="py-12 lg:py-14 bg-slate-50">
    <div class="edu-container">
        <div class="grid lg:grid-cols-2 gap-8 lg:gap-12 items-start reveal">
            <div>
                <span class="edu-sub-title">{{ $brand }}</span>
                <h2 class="edu-section-title text-slate-900 mt-1">{{ $p('idea_title') }}</h2>
                <p class="text-slate-600 leading-8 mt-4 text-sm lg:text-base">{{ $p('idea_intro') }}</p>
            </div>
            <ul class="space-y-3">
                @foreach(__('sana_pricing.idea_points') as $point)
                <li class="flex items-start gap-3 text-sm text-slate-700 leading-7">
                    <i class="fas fa-check-circle text-[var(--edu-primary)] mt-1 shrink-0"></i>
                    <span>{{ $point }}</span>
                </li>
                @endforeach
            </ul>
        </div>
    </div>
</section>

{{-- Comparison table --}}
<section class="py-12 lg:py-14 bg-white" id="compare">
    <div class="edu-container reveal">
        <div class="text-center mb-8">
            <h2 class="edu-section-title text-slate-900">{{ $p('compare_title') }}</h2>
        </div>
        <div class="edu-pricing-compare-wrap">
            <table class="edu-pricing-compare">
                <thead>
                    <tr>
                        @foreach(__('sana_pricing.compare_cols') as $col)
                        <th>{{ $col }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($compareRows as $row)
                    <tr>
                        <td class="pkg-name"><a href="#{{ $row[0] }}" class="text-[var(--edu-primary)] hover:underline">{{ $packageNames[$row[0]] ?? $row[0] }}</a></td>
                        <td>{{ $row[1] }}</td>
                        <td>{{ $row[2] }}</td>
                        <td>{{ $row[3] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>

{{-- Four packages --}}
<section class="py-12 lg:py-16 bg-[var(--edu-bg)]" id="packages">
    <div class="edu-container">
        <div class="text-center mb-10 reveal">
            <span class="edu-sub-title">SANA | {{ $brand }}</span>
            <h2 class="edu-section-title text-slate-900">باقات منصة تعليمية أونلاين</h2>
        </div>
        <div class="grid md:grid-cols-2 gap-6 lg:gap-8">
            @foreach($packageOrder as $i => $key)
            @php
                $pkg = $packages[$key];
                $headClass = match ($pkg['color'] ?? 'primary') {
                    'purple' => 'is-purple',
                    'accent' => 'is-accent',
                    default => 'is-primary',
                };
                $cardClass = match ($pkg['color'] ?? 'primary') {
                    'purple' => 'is-purple',
                    'accent' => 'is-accent',
                    default => '',
                };
            @endphp
            <article id="{{ $key }}" class="edu-pricing-package-card {{ $cardClass }} reveal @if($i > 0) s{{ min($i, 3) }} @endif">
                <div class="edu-pricing-package-head {{ $headClass }}">
                    <span class="text-xs font-bold opacity-90 block mb-1">{{ $pkg['badge'] }}</span>
                    <div class="flex items-start gap-3">
                        <span class="w-11 h-11 rounded-xl bg-white/20 flex items-center justify-center text-lg shrink-0">
                            <i class="fas {{ $pkg['icon'] }}"></i>
                        </span>
                        <h3 class="text-lg sm:text-xl font-extrabold leading-snug">{{ $pkg['title'] }}</h3>
                    </div>
                </div>
                <div class="edu-pricing-package-body">
                    <p class="text-xs font-bold text-slate-500 mb-1">{{ $pkg['best_for_title'] }}</p>
                    <p class="text-sm text-slate-700 leading-7 mb-4">{{ $pkg['best_for'] }}</p>
                    <p class="text-xs font-bold text-[var(--edu-primary)] mb-2">{{ $pkg['includes_title'] }}</p>
                    <ul class="space-y-2 mb-5 flex-1">
                        @foreach($pkg['includes'] as $item)
                        <li class="flex items-start gap-2 text-sm text-slate-600 leading-6">
                            <i class="fas fa-circle text-[6px] text-[var(--edu-primary)] mt-2 shrink-0"></i>
                            <span>{{ $item }}</span>
                        </li>
                        @endforeach
                    </ul>
                    <p class="edu-pricing-quote mb-5">{{ $pkg['marketing'] }}</p>
                    <a href="{{ $ctaRoutes[$key] }}" class="edu-btn-primary w-full justify-center text-sm">
                        {{ $pkg['cta'] }}
                        <i class="fas fa-arrow-left text-xs"></i>
                    </a>
                </div>
            </article>
            @endforeach
        </div>
    </div>
</section>

{{-- Sample offers --}}
<section class="py-12 lg:py-14 bg-white">
    <div class="edu-container reveal">
        <div class="text-center mb-8 max-w-2xl mx-auto">
            <h2 class="edu-section-title text-slate-900">{{ $p('offers_title') }}</h2>
            <p class="text-slate-600 text-sm mt-3 leading-7">{{ $p('offers_sub') }}</p>
        </div>
        <div class="edu-pricing-compare-wrap">
            <table class="edu-pricing-compare">
                <thead>
                    <tr>
                        @foreach(__('sana_pricing.offers_cols') as $col)
                        <th>{{ $col }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach(__('sana_pricing.offers') as $offer)
                    <tr class="edu-pricing-offer-row">
                        <td class="pkg-name">{{ $offer['name'] }}</td>
                        <td>{{ $offer['content'] }}</td>
                        <td>{{ $offer['for'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>

{{-- Tips --}}
<section class="py-12 lg:py-14 bg-slate-50">
    <div class="edu-container">
        <h2 class="edu-section-title text-slate-900 text-center mb-8 reveal">{{ $p('tips_title') }}</h2>
        <div class="grid sm:grid-cols-2 gap-4 max-w-4xl mx-auto">
            @foreach(__('sana_pricing.tips') as $i => $tip)
            <div class="edu-card p-5 flex items-start gap-4 reveal @if($i > 0) s{{ $i }} @endif">
                <span class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 text-white" style="background:var(--edu-primary)">
                    <i class="fas {{ $tip['icon'] }}"></i>
                </span>
                <p class="text-sm text-slate-700 leading-7 font-medium">{{ $tip['text'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="py-12 lg:py-14 bg-white">
    <div class="edu-container reveal">
        <div class="edu-cta-wrap px-5 sm:px-8 py-10 lg:py-12 text-center text-white">
            <h2 class="text-2xl sm:text-3xl font-extrabold mb-3">{{ $p('cta_title') }}</h2>
            <p class="text-white/90 text-sm sm:text-base max-w-2xl mx-auto mb-8 leading-8">{{ $p('cta_sub') }}</p>
            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <a href="{{ route('register') }}" class="edu-btn-white !text-[var(--edu-primary)]">
                    {{ $p('cta_book') }}
                    <i class="fas fa-arrow-left text-sm"></i>
                </a>
                <a href="{{ route('public.courses') }}" class="edu-btn-ghost-light">
                    {{ $p('cta_courses') }}
                    <i class="fas fa-arrow-left text-sm"></i>
                </a>
                <a href="{{ route('public.contact') }}" class="edu-btn-ghost-light">
                    {{ $p('cta_contact') }}
                    <i class="fas fa-arrow-left text-sm"></i>
                </a>
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
    }
})();
</script>
@include('partials.pwa-service-worker')
</body>
</html>
