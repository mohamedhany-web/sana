@php
    $brand = config('app.name', 'Sana');
    $tr = fn (string $key) => str_replace(':brand', $brand, __('sana_home.'.$key));
    $hasProfiles = ($allProfilesCount ?? $profiles->count()) > 0;
    $bookableCount = (int) (($filterOptions['bookable_count'] ?? 0));
    $withCourses = (int) (($filterOptions['courses_count'] ?? 0));
    $activeFilters = $activeFilters ?? [];
    $filterOptions = $filterOptions ?? [];
    $hasActiveFilters = count($activeFilters) > 0;
    $queryBase = request()->only(array_filter([
        request()->boolean('tutors') ? 'tutors' : null,
        request('mode') === 'pick_teacher' ? 'mode' : null,
    ]));
@endphp
<!DOCTYPE html>
@php
    $htmlLang = $htmlLang ?? (str_starts_with((string) app()->getLocale(), 'en') ? 'en' : 'ar');
    $htmlDir = $htmlDir ?? ($htmlLang === 'en' ? 'ltr' : 'rtl');
@endphp
<html lang="{{ $htmlLang }}" dir="{{ $htmlDir }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
    <title>{{ __('public.instructors_page_title') }} — {{ $brand }}</title>
    <meta name="description" content="{{ __('public.instructors_subtitle') }}">
    <meta name="theme-color" content="#5B21B6">
    <link rel="canonical" href="{{ url('/instructors') }}">
    @include('partials.favicon-links')
    @include('partials.seo-jsonld', ['jsonldType' => 'website'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@800;900&family=Tajawal:wght@500;700;800&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @include('partials.rtl-base')
    @include('landing.sana.theme')
    @include('landing.sana.courses-catalog-theme')
    @include('landing.sana.instructors-catalog-theme')
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="sana-home sana-courses-page sana-instructors-page" x-data="{ filtersOpen: false }">

<div id="sana-scroll-progress"></div>
@include('landing.sana.navbar')

<main class="sana-cat-page">

<section class="sana-cat-hero" id="inst-hero">
    <div class="sana-cat-hero__dots"></div>
    <div class="sana-container sana-cat-hero__inner">
        <nav class="sana-cat-hero__breadcrumb" aria-label="{{ __('public.breadcrumb_aria') }}">
            <a href="{{ route('home') }}">{{ __('public.home') }}</a>
            <i class="fas fa-chevron-left" style="font-size:0.6rem;opacity:0.5"></i>
            <span>{{ __('public.instructors_page_title') }}</span>
        </nav>

        <span class="sana-inst-hero__eyebrow"><i class="fas fa-chalkboard-user"></i> {{ $tr('instructors.badge') }}</span>

        <h1 class="sana-cat-hero__title">
            {{ $tr('instructors.title') }} <span class="hl">{{ $tr('instructors.title_highlight') }}</span>
        </h1>
        <p class="sana-cat-hero__desc">{{ __('public.instructors_subtitle') }}</p>

        @if($hasProfiles)
            @if($bookableCount > 0 || $withCourses > 0)
            <div class="sana-cat-hero__stats sana-reveal">
                @if($bookableCount > 0)
                <span class="sana-cat-hero__stat"><i class="fas fa-calendar-check"></i> {{ $bookableCount }} {{ __('public.instructor_stat_bookable') }}</span>
                @endif
                @if($withCourses > 0)
                <span class="sana-cat-hero__stat"><i class="fas fa-book-open"></i> {{ $withCourses }} {{ $tr('instructors.courses') }}</span>
                @endif
            </div>
            @endif

            <form method="GET" action="{{ route('public.instructors.index') }}" class="sana-inst-hero-search sana-reveal">
                @foreach($queryBase as $k => $v)
                    <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                @endforeach
                @foreach(request()->except(array_merge(['q', 'search', 'page'], array_keys($queryBase))) as $k => $v)
                    @if(is_scalar($v) && filled($v))
                        <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                    @endif
                @endforeach
                <i class="fas fa-search" aria-hidden="true"></i>
                <input type="search"
                       name="q"
                       value="{{ request('q', request('search')) }}"
                       placeholder="{{ __('public.instructors_search_placeholder') }}"
                       aria-label="{{ __('public.instructors_search_placeholder') }}">
            </form>
        @else
            <div class="sana-cat-hero__soon sana-reveal">
                <span class="sana-cat-hero__soon-badge"><i class="fas fa-hourglass-half"></i> {{ __('public.instructors_coming_soon_title') }}</span>
                <p>{{ __('public.instructors_coming_soon_sub') }}</p>
                <div class="sana-cat-hero__soon-actions">
                    @include('landing.sana.partials.site-cta-buttons', ['hero' => true])
                </div>
            </div>
        @endif

        <div class="sana-inst-hero__actions sana-reveal">
            @if($hasPublishedCourses ?? false)
                <a href="{{ route('public.courses') }}" class="sana-btn sana-btn--white-outline sana-btn--sm"><i class="fas fa-book-open"></i> {{ $tr('hero.cta_courses') }}</a>
            @endif
            <a href="{{ route('tutor.apply') }}" class="sana-btn sana-btn--white-outline sana-btn--sm"><i class="fas fa-chalkboard-teacher"></i> {{ __('public.instructors_cta_register') }}</a>
        </div>
    </div>
</section>

<section class="sana-section {{ $hasProfiles ? 'sana-section--white' : 'sana-section--soft' }}">
    <div class="sana-container">
        @if($hasProfiles)
            <div class="sana-head-row sana-reveal" style="margin-bottom:20px">
                <div class="sana-head">
                    <h2 class="sana-head__title">{{ __('public.instructors_bookable_heading_prefix') }} <span class="hl">{{ __('public.instructors_bookable_heading_hl') }}</span></h2>
                    <span class="sana-head__line"></span>
                </div>
                <div class="sana-inst-toolbar">
                    <p class="sana-inst-toolbar-note">
                        {{ $profiles->count() }}
                        @if($hasActiveFilters)
                            {{ __('public.instructors_filtered_of', ['total' => $allProfilesCount ?? $profiles->count()]) }}
                        @else
                            {{ __('public.stat_instructors') }}
                        @endif
                    </p>
                    <button type="button" class="sana-cat-filter-mobile-btn sana-btn sana-btn--outline-purple sana-btn--sm" @click="filtersOpen = true">
                        <i class="fas fa-sliders"></i> {{ __('public.instructors_filters_title') }}
                        @if($hasActiveFilters)<span class="sana-inst-filter-badge">{{ count($activeFilters) }}</span>@endif
                    </button>
                </div>
            </div>

            <div class="sana-cat-layout sana-inst-layout">
                @include('instructors.partials.filters', [
                    'filterOptions' => $filterOptions,
                    'queryBase' => $queryBase,
                    'hasActiveFilters' => $hasActiveFilters,
                    'desktop' => true,
                ])

                <div>
                    @if($profiles->isNotEmpty())
                        <div class="sana-inst-grid-v2">
                            @foreach($profiles as $p)
                            @php
                                $photo = $p->photo_url;
                                $name = $p->user->name ?? __('public.instructor_fallback');
                                $headline = $p->headline ?: ($p->bio ? Str::limit(strip_tags($p->bio), 48) : __('public.instructor_fallback'));
                                $subjects = array_slice($p->public_subject_labels ?? [], 0, 3);
                                $bookUrl = $p->public_book_url ?? route('register');
                            @endphp
                            <article class="sana-inst-card-v2 sana-reveal">
                                <a href="{{ route('public.instructors.show', $p->user) }}" class="sana-inst-card-v2__main">
                                    <div class="sana-inst-card-v2__ring">
                                        @if($photo)
                                            <img src="{{ $photo }}" alt="{{ $name }}" loading="lazy">
                                        @else
                                            <span class="av">{{ mb_substr($name, 0, 1) }}</span>
                                        @endif
                                    </div>
                                    <h3>{{ $name }}</h3>
                                    <p class="sana-inst-card-v2__role">{{ Str::limit($headline, 42) }}</p>
                                    @if(count($subjects) > 0)
                                    <div class="sana-inst-card-v2__tags">
                                        @foreach($subjects as $subject)<span>{{ $subject }}</span>@endforeach
                                    </div>
                                    @endif
                                    <div class="sana-inst-card-v2__badges">
                                        @if(($p->courses_count ?? 0) > 0)
                                            <span><i class="fas fa-book-open"></i> {{ (int) $p->courses_count }} {{ $tr('instructors.courses') }}</span>
                                        @elseif(!empty($p->is_bookable))
                                            <span class="is-book"><i class="fas fa-calendar-check"></i> {{ __('public.instructor_stat_bookable') }}</span>
                                        @endif
                                        @if(!empty($p->public_years_experience))
                                            <span><i class="fas fa-briefcase"></i> {{ __('public.instructor_experience_years', ['years' => $p->public_years_experience]) }}</span>
                                        @endif
                                        @if(!empty($p->public_has_video))
                                            <span><i class="fas fa-play"></i> {{ __('public.instructors_filter_has_video_short') }}</span>
                                        @endif
                                    </div>
                                    <span class="sana-inst-card-v2__link">{{ __('public.view_instructor_profile') }} <i class="fas fa-arrow-left"></i></span>
                                </a>
                                @if(!empty($p->is_bookable))
                                <a href="{{ $bookUrl }}" class="sana-btn sana-btn--yellow sana-btn--sm sana-inst-card-v2__book">
                                    <i class="fas fa-calendar-plus"></i> {{ __('public.instructor_book_with') }}
                                </a>
                                @endif
                            </article>
                            @endforeach
                        </div>
                    @else
                        <div class="sana-sub-empty sana-reveal" style="margin-top:8px">
                            <div class="sana-sub-empty__icon"><i class="fas fa-magnifying-glass"></i></div>
                            <h3 style="font-weight:900;margin:0 0 8px">{{ __('public.no_results') }}</h3>
                            <p style="color:var(--muted);font-size:0.88rem;margin:0 0 16px">{{ __('public.instructors_no_filter_results') }}</p>
                            <a href="{{ route('public.instructors.index', $queryBase) }}" class="sana-btn sana-btn--outline-purple sana-btn--sm">
                                <i class="fas fa-undo"></i> {{ __('public.filter_reset') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Mobile filters drawer --}}
            <div class="sana-inst-filters-drawer" x-show="filtersOpen" x-cloak>
                <div class="sana-inst-filters-drawer__backdrop" @click="filtersOpen = false"></div>
                <div class="sana-inst-filters-drawer__panel" @click.stop>
                    <div class="sana-inst-filters-drawer__head">
                        <strong>{{ __('public.instructors_filters_title') }}</strong>
                        <button type="button" class="sana-inst-filters-drawer__close" @click="filtersOpen = false" aria-label="{{ __('public.filter_reset') }}">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    @include('instructors.partials.filters', [
                        'filterOptions' => $filterOptions,
                        'queryBase' => $queryBase,
                        'hasActiveFilters' => $hasActiveFilters,
                        'desktop' => false,
                    ])
                </div>
            </div>
        @else
            <div class="sana-inst-empty-panel sana-reveal">
                <div class="sana-inst-empty-panel__icon"><i class="fas fa-users"></i></div>
                <h2>{{ __('public.instructors_coming_soon_title') }}</h2>
                <p>{{ __('public.instructors_coming_soon_sub') }}</p>
                <div class="sana-inst-empty-panel__actions">
                    @include('landing.sana.partials.site-cta-buttons')
                    <a href="{{ route('tutor.apply') }}" class="sana-btn sana-btn--outline-purple"><i class="fas fa-chalkboard-teacher"></i> {{ __('public.instructors_cta_register') }}</a>
                </div>
            </div>
        @endif
    </div>
</section>

<section class="sana-cat-cta">
    <div class="sana-container">
        <div class="sana-cat-cta__inner sana-inst-cta">
            <div>
                <h2>{{ __('public.instructors_cta_title') }}</h2>
                <p>{{ __('public.instructors_cta_subtitle') }}</p>
            </div>
            <div class="sana-cat-cta__actions">
                @include('landing.sana.partials.site-cta-buttons', ['hero' => true])
                <a href="{{ route('tutor.apply') }}" class="sana-btn sana-btn--purple-outline"><i class="fas fa-chalkboard-teacher"></i> {{ __('public.audience_teacher_cta') }}</a>
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
