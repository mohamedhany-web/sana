@php
    $brand = config('app.name', 'Sana');
    $tr = fn (string $key) => str_replace(':brand', $brand, __('sana_home.'.$key));
    $defaultGrouped = collect($defaultFaqs ?? [])->groupBy('category');
    $hasDbFaqs = isset($faqs) && $faqs->isNotEmpty();
@endphp
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
    <title>{{ __('public.faq_page_title') }} — {{ $brand }}</title>
    <meta name="description" content="{{ __('public.faq_meta_description', ['brand' => $brand]) }}">
    <meta name="theme-color" content="#5B21B6">
    <link rel="canonical" href="{{ url('/faq') }}">
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
                    <span>{{ __('public.faq_page_title') }}</span>
                </nav>
                <span class="sana-sub-hero__eyebrow"><i class="fas fa-circle-question"></i> {{ __('public.faq_hero_highlight') }}</span>
                <h1 class="sana-sub-hero__title">{{ __('public.faq_page_title') }}</h1>
                <p class="sana-sub-hero__sub">{{ __('public.faq_hero_sub') }}</p>
                <div class="sana-sub-hero__actions">
                    <a href="#faq-main" class="sana-btn sana-btn--yellow"><i class="fas fa-list"></i> {{ __('public.faq_filter_all') }}</a>
                    <a href="{{ route('public.contact') }}" class="sana-btn sana-btn--white-outline"><i class="fas fa-envelope"></i> {{ __('public.contact_page_title') }}</a>
                </div>
            </div>
            <svg class="sana-sub-hero__illus" viewBox="0 0 160 160" fill="none" aria-hidden="true">
                <circle cx="80" cy="80" r="64" fill="rgba(255,255,255,0.08)" stroke="rgba(255,255,255,0.2)"/>
                <text x="80" y="92" text-anchor="middle" fill="#FBBF24" font-size="52" font-weight="900">?</text>
                <circle cx="120" cy="48" r="8" fill="rgba(255,255,255,0.35)"/>
                <circle cx="44" cy="112" r="6" fill="rgba(251,191,36,0.5)"/>
            </svg>
        </div>
    </div>
</section>

<section class="sana-section" id="faq-main">
    <div class="sana-container">
        <div class="sana-faq-layout sana-reveal">
            <aside class="sana-faq-sidebar">
                @if(isset($categories) && $categories->isNotEmpty())
                <div class="sana-faq-filters">
                    <button type="button" class="sana-faq-filter is-active filter-btn-faq" data-category="all">{{ __('public.faq_filter_all') }}</button>
                    @foreach($categories as $cat)
                    <button type="button" class="sana-faq-filter filter-btn-faq" data-category="{{ $cat }}">{{ $cat }}</button>
                    @endforeach
                </div>
                @endif
                <div class="sana-faq-side-panel">
                    <h4>{{ __('public.faq_quick_links') }}</h4>
                    <a href="{{ route('public.contact') }}"><i class="fas fa-paper-plane"></i> {{ __('public.contact_page_title') }}</a>
                    <a href="{{ route('public.help') }}"><i class="fas fa-book-open"></i> {{ __('public.help_page_title') }}</a>
                    <a href="{{ route('public.certificates') }}"><i class="fas fa-certificate"></i> {{ __('public.certificates_page_title') }}</a>
                    <a href="{{ route('public.pricing') }}"><i class="fas fa-tags"></i> {{ __('public.pricing_page_title') }}</a>
                </div>
            </aside>

            <div class="sana-faq-main">
                @if(isset($categories) && $categories->isNotEmpty())
                <div class="sana-faq-filters sana-faq-filters--mobile">
                    <button type="button" class="sana-faq-filter is-active filter-btn-faq" data-category="all">{{ __('public.faq_filter_all') }}</button>
                    @foreach($categories as $cat)
                    <button type="button" class="sana-faq-filter filter-btn-faq" data-category="{{ $cat }}">{{ $cat }}</button>
                    @endforeach
                </div>
                @endif

                @if($hasDbFaqs)
                @foreach($faqs as $categoryName => $categoryFaqs)
                <div class="sana-faq-block faq-block" data-category="{{ $categoryName ?? 'general' }}">
                    @if($categoryName)
                    <h2 class="sana-faq-block__title"><i class="fas fa-layer-group"></i> {{ $categoryName }}</h2>
                    @endif
                    <div class="sana-faq" id="sana-faq-{{ $loop->index }}">
                        @foreach($categoryFaqs as $i => $faq)
                        <div class="sana-faq-item {{ $loop->parent->first && $i === 0 ? 'is-open' : '' }}">
                            <button type="button" class="sana-faq-q" aria-expanded="{{ $loop->parent->first && $i === 0 ? 'true' : 'false' }}">
                                {{ $faq->question }} <i class="fas fa-chevron-down"></i>
                            </button>
                            <div class="sana-faq-a">{!! nl2br(e($faq->answer)) !!}</div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
                @endif

                @if($defaultGrouped->isNotEmpty())
                <div class="sana-faq-block faq-block default-faqs" data-category="default">
                    <h2 class="sana-faq-block__title"><i class="fas fa-graduation-cap"></i> {{ __('public.faq_section_platform', ['brand' => $brand]) }}</h2>
                    @foreach($defaultGrouped as $catName => $items)
                    @if($catName)
                    <p style="font-size:0.82rem;font-weight:800;color:var(--p);margin:16px 0 10px"><i class="fas fa-tag"></i> {{ $catName }}</p>
                    @endif
                    <div class="sana-faq sana-faq-group">
                        @foreach($items as $i => $item)
                        <div class="sana-faq-item {{ $loop->parent->first && $loop->first && $i === 0 && !$hasDbFaqs ? 'is-open' : '' }}">
                            <button type="button" class="sana-faq-q">{{ $item['question'] }} <i class="fas fa-chevron-down"></i></button>
                            <div class="sana-faq-a">{!! nl2br(e($item['answer'] ?? '')) !!}</div>
                        </div>
                        @endforeach
                    </div>
                    @endforeach
                </div>
                @endif

                @if(!$hasDbFaqs && $defaultGrouped->isEmpty())
                <div class="sana-sub-empty">
                    <div class="sana-sub-empty__icon"><i class="fas fa-question"></i></div>
                    <h3 style="font-weight:900;margin:0 0 8px">{{ __('public.faq_empty_title') }}</h3>
                    <a href="{{ route('public.contact') }}" class="sana-btn sana-btn--purple" style="margin-top:16px">
                        <i class="fas fa-envelope"></i> {{ __('public.faq_empty_cta') }}
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>

<section class="sana-sub-final">
    <div class="sana-container sana-reveal">
        <div class="sana-sub-final__box">
            <h2>{{ __('public.faq_cta_title') }}</h2>
            <p>{{ __('public.faq_cta_desc') }}</p>
            <div class="sana-sub-final__actions">
                <a href="{{ route('public.contact') }}" class="sana-btn sana-btn--yellow">{{ __('public.faq_cta_btn') }} <i class="fas fa-arrow-left"></i></a>
                <a href="{{ route('public.help') }}" class="sana-btn sana-btn--ghost-light">{{ __('public.help_page_title') }} <i class="fas fa-life-ring"></i></a>
            </div>
        </div>
    </div>
</section>

</main>

@include('landing.sana.footer')
@include('landing.sana.scripts')
<script>
document.querySelectorAll('.filter-btn-faq').forEach(function (btn) {
    btn.addEventListener('click', function () {
        var cat = this.getAttribute('data-category');
        document.querySelectorAll('.filter-btn-faq').forEach(function (b) {
            b.classList.toggle('is-active', b.getAttribute('data-category') === cat);
        });
        document.querySelectorAll('.faq-block').forEach(function (block) {
            var blockCat = block.getAttribute('data-category');
            block.style.display = (cat === 'all' || blockCat === cat) ? '' : 'none';
        });
    });
});
document.querySelectorAll('.sana-faq-group, .sana-faq[id^="sana-faq-"]').forEach(function (container) {
    container.addEventListener('click', function (e) {
        var btn = e.target.closest('.sana-faq-q');
        if (!btn) return;
        var item = btn.closest('.sana-faq-item');
        var open = item.classList.contains('is-open');
        container.querySelectorAll('.sana-faq-item').forEach(function (i) {
            i.classList.remove('is-open');
            i.querySelector('.sana-faq-q')?.setAttribute('aria-expanded', 'false');
        });
        if (!open) {
            item.classList.add('is-open');
            btn.setAttribute('aria-expanded', 'true');
        }
    });
});
</script>
@include('partials.pwa-service-worker')
</body>
</html>
