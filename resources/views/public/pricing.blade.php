@php
    $brand = config('app.name', 'Sana');
    $bc = config('brand.colors');
    $tr = fn (string $key) => str_replace(':brand', $brand, __('sana_home.'.$key));
    $planKeys = ['teacher_starter', 'teacher_pro'];
    $billingPhrases = [
        'monthly' => __('public.pricing_per_month'),
        'quarterly' => __('public.pricing_per_quarter'),
        'yearly' => __('public.pricing_per_year'),
    ];
@endphp
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
    <title>{{ __('public.pricing_page_title') }} - {{ $brand }}</title>
    <meta name="description" content="{{ str_replace(':brand', $brand, __('public.pricing_hero_sub')) }}">
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
<section class="edu-courses-page-hero py-8 lg:py-10">
    <div class="edu-container-full">
        <div class="edu-courses-inner text-center reveal">
            <nav class="edu-breadcrumb justify-center mb-4" aria-label="مسار التنقل">
                <a href="{{ route('home') }}">{{ $tr('nav.home') }}</a>
                <i class="fas fa-chevron-left text-[10px] opacity-50"></i>
                <span class="text-slate-800 font-semibold">{{ __('public.pricing_page_title') }}</span>
            </nav>
            <span class="edu-badge mb-4"><i class="fas fa-tags"></i> {{ __('public.pricing_page_title') }}</span>
            <h1 class="edu-section-title text-slate-900 max-w-3xl mx-auto">
                {{ __('public.pricing_hero_title') }}
                @include('landing.eduvalt.partials.title-mark', ['text' => __('public.pricing_hero_highlight')])
            </h1>
            <p class="text-slate-600 leading-8 mt-4 max-w-2xl mx-auto text-sm lg:text-base">
                {{ str_replace(':brand', $brand, __('public.pricing_hero_sub')) }}
            </p>
            <p class="text-slate-500 text-sm mt-3 max-w-xl mx-auto">{{ __('public.pricing_hero_note') }}</p>
            <div class="flex flex-wrap justify-center gap-3 mt-8">
                <a href="#teacher-plans" class="edu-btn-primary text-sm">{{ __('public.pricing_teachers_badge') }}</a>
                @if(isset($packages) && $packages->count() > 0)
                    <a href="#parent-packages" class="edu-btn-outline text-sm">{{ __('public.pricing_packages_badge') }}</a>
                @endif
            </div>
        </div>
    </div>
</section>

{{-- خطط المعلمين --}}
<section class="py-12 lg:py-16 bg-white" id="teacher-plans">
    <div class="edu-container">
        <div class="text-center mb-10 reveal">
            <span class="edu-sub-title">{{ __('public.pricing_teachers_badge') }}</span>
            <h2 class="edu-section-title text-slate-900">{{ __('public.pricing_teachers_title') }}</h2>
            <p class="text-slate-600 max-w-2xl mx-auto mt-3 leading-7 text-sm">{{ __('public.pricing_teachers_sub') }}</p>
        </div>

        <div class="grid md:grid-cols-2 gap-6 lg:gap-8 max-w-4xl mx-auto">
            @foreach($planKeys as $planKey)
                @php
                    $plan = $teacherPlans[$planKey] ?? null;
                    if (!$plan) continue;
                    $meta = [
                        'subtitle' => $plan['card_subtitle'] ?? '',
                        'badge' => trim((string) ($plan['card_badge'] ?? '')),
                        'priceHint' => $plan['card_price_hint'] ?? '',
                        'cta' => $plan['card_cta'] ?? 'ابدأ الآن',
                        'footer_note' => $plan['card_footer_note'] ?? '',
                    ];
                    $label = $plan['label'] ?? $planKey;
                    $price = (float) ($plan['price'] ?? 0);
                    $cycle = $plan['billing_cycle'] ?? 'monthly';
                    $cyclePhrase = $billingPhrases[$cycle] ?? __('public.currency');
                    $features = $plan['features'] ?? [];
                    $featureDescriptions = is_array($plan['feature_descriptions'] ?? null) ? $plan['feature_descriptions'] : [];
                    $isPro = $planKey === 'teacher_pro';
                @endphp
                <article class="edu-card edu-pricing-plan reveal {{ $isPro ? 'is-featured' : '' }} !p-6 sm:!p-8">
                    @if($meta['badge'] !== '')
                        <span class="edu-pricing-badge">{{ $meta['badge'] }}</span>
                    @elseif($isPro)
                        <span class="edu-pricing-badge">{{ __('public.pricing_popular') }}</span>
                    @endif
                    <div class="mb-4">
                        <h3 class="text-xl font-bold text-slate-900 mb-1">{{ $label }}</h3>
                        @if($meta['subtitle'] !== '')
                            <p class="text-sm font-semibold text-[var(--edu-primary)]">{{ $meta['subtitle'] }}</p>
                        @endif
                    </div>
                    <div class="mb-5">
                        <p class="text-3xl font-black text-[var(--edu-primary)] tabular-nums">
                            {{ number_format($price, 0) }}
                            <span class="text-base font-bold text-slate-500">{{ $cyclePhrase }}</span>
                        </p>
                        @if($meta['priceHint'] !== '')
                            <p class="text-sm text-slate-500 mt-1">{{ $meta['priceHint'] }}</p>
                        @endif
                    </div>
                    <ul class="space-y-3 text-sm text-slate-600 mb-6 flex-1">
                        @foreach($features as $featureKey)
                            <li class="flex items-start gap-2">
                                <i class="fas fa-check-circle text-[var(--edu-primary)] mt-0.5 shrink-0"></i>
                                <div>
                                    <p class="font-semibold text-slate-800">{{ __("student.subscription_feature.{$featureKey}") }}</p>
                                    @if(!empty($featureDescriptions[$featureKey]))
                                        <p class="text-xs text-slate-500 mt-0.5">{{ $featureDescriptions[$featureKey] }}</p>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                    @if($meta['footer_note'] !== '')
                        <div class="mb-4 px-3 py-2 rounded-xl text-xs font-semibold text-[var(--edu-primary)] bg-[var(--edu-primary-light)] border border-[var(--edu-primary)]/15">
                            {{ $meta['footer_note'] }}
                        </div>
                    @endif
                    <a href="{{ route('public.subscription.checkout', $planKey) }}" class="edu-btn-primary w-full justify-center text-sm">
                        {{ $meta['cta'] }}
                        <i class="fas fa-arrow-left text-xs"></i>
                    </a>
                </article>
            @endforeach
        </div>
    </div>
</section>

{{-- باقات أولياء الأمور --}}
@if(isset($packages) && $packages->count() > 0)
<section class="py-12 lg:py-16 bg-[var(--edu-bg)] border-t border-slate-100" id="parent-packages">
    <div class="edu-container">
        <div class="text-center mb-10 reveal">
            <span class="edu-sub-title">{{ __('public.pricing_packages_badge') }}</span>
            <h2 class="edu-section-title text-slate-900">{{ __('public.pricing_packages_title') }}</h2>
            <p class="text-slate-600 max-w-2xl mx-auto mt-3 leading-7 text-sm">{{ __('public.pricing_packages_sub') }}</p>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($packages as $package)
                @php
                    $isPopular = (bool) $package->is_popular;
                    $cardFeatures = collect($package->features ?? [])->map(fn ($f) => trim((string) $f))->filter()->values();
                    $cardBody = trim((string) ($package->card_summary ?? '')) !== ''
                        ? $package->card_summary
                        : ($package->description ?? '');
                @endphp
                <article class="edu-card reveal flex flex-col !p-6 sm:!p-8 relative {{ $isPopular ? 'edu-package-popular' : '' }}">
                    @if($isPopular)
                        <span class="edu-pricing-badge">{{ __('public.pricing_popular') }}</span>
                    @endif

                    <div class="text-center mb-5 {{ $isPopular ? 'mt-2' : '' }}">
                        @if($package->thumbnail)
                            <div class="w-16 h-16 rounded-2xl overflow-hidden mx-auto mb-4 ring-2 ring-white/30">
                                <img src="{{ asset('storage/' . $package->thumbnail) }}" alt="{{ $package->name }}" class="w-full h-full object-cover" loading="lazy">
                            </div>
                        @else
                            <div class="w-16 h-16 rounded-2xl mx-auto mb-4 flex items-center justify-center text-2xl {{ $isPopular ? 'bg-white/20 text-white' : 'bg-[var(--edu-primary-light)] text-[var(--edu-primary)]' }}">
                                <i class="fas {{ $package->is_featured ? 'fa-crown' : ($isPopular ? 'fa-star' : 'fa-box-open') }}"></i>
                            </div>
                        @endif

                        <h3 class="text-xl font-bold mb-2 {{ $isPopular ? 'text-white' : 'text-slate-900' }}">{{ $package->name }}</h3>

                        @if($package->original_price && $package->original_price > $package->price)
                            <p class="text-sm line-through mb-1 {{ $isPopular ? 'text-white/70' : 'text-slate-400' }}">
                                {{ number_format($package->original_price, 0) }} {{ __('public.currency') }}
                            </p>
                        @endif

                        <p class="text-3xl font-black tabular-nums {{ $isPopular ? 'text-white' : 'text-[var(--edu-primary)]' }}">
                            @if($package->price > 0)
                                {{ number_format($package->price, 0) }}
                                <span class="text-lg font-bold">{{ __('public.currency') }}</span>
                            @else
                                {{ __('public.pricing_free') }}
                            @endif
                        </p>

                        @if($cardBody !== '')
                            <p class="text-sm mt-3 leading-relaxed line-clamp-4 {{ $isPopular ? 'edu-package-muted' : 'text-slate-600' }}">{{ $cardBody }}</p>
                        @endif

                        @if($package->courses_count > 0)
                            <p class="text-xs mt-2 {{ $isPopular ? 'edu-package-muted' : 'text-slate-500' }}">
                                <i class="fas fa-graduation-cap"></i>
                                {{ $package->courses_count }} {{ $tr('instructors.courses') }}
                            </p>
                        @endif
                    </div>

                    @if($cardFeatures->isNotEmpty())
                        <ul class="space-y-2.5 mb-6 flex-1 text-sm">
                            @foreach($cardFeatures as $feature)
                                <li class="flex items-start gap-2 {{ $isPopular ? 'text-white/95' : 'text-slate-700' }}">
                                    <i class="fas fa-check-circle mt-0.5 shrink-0 {{ $isPopular ? 'text-[var(--edu-accent)]' : 'text-[var(--edu-primary)]' }}"></i>
                                    <span>{{ $feature }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    <a href="{{ route('public.package.show', $package->slug) }}"
                       class="w-full justify-center text-sm {{ $isPopular ? 'edu-btn-white !text-[var(--edu-primary)]' : 'edu-btn-primary' }}">
                        <i class="fas {{ $package->price > 0 ? 'fa-shopping-cart' : 'fa-eye' }}"></i>
                        {{ $package->price > 0 ? __('public.pricing_buy_now') : __('public.pricing_view_details') }}
                    </a>
                </article>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- CTA --}}
<section class="py-12 lg:py-14 bg-white">
    <div class="edu-container reveal">
        <div class="edu-cta-wrap px-8 py-10 lg:py-12 text-center text-white">
            <h2 class="text-2xl sm:text-3xl font-bold mb-3">{{ __('public.pricing_cta_title') }}</h2>
            <p class="text-white/90 text-sm sm:text-base max-w-xl mx-auto mb-8 leading-7">{{ __('public.pricing_cta_sub') }}</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('register') }}" class="edu-btn-white !text-[var(--edu-primary)] hover:!text-[var(--edu-primary-dark)]">
                    {{ $tr('hero.cta_book') }}
                    <i class="fas fa-arrow-left text-sm"></i>
                </a>
                <a href="{{ route('public.courses') }}" class="edu-btn-ghost-light">
                    {{ $tr('hero.cta_courses') }}
                    <i class="fas fa-arrow-left text-sm"></i>
                </a>
                <a href="{{ route('public.instructors.index') }}" class="edu-btn-ghost-light">
                    {{ $tr('nav.instructors') }}
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
