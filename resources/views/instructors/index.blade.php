@php
    $brand = config('app.name', 'Sana');
    $tr = fn (string $key) => str_replace(':brand', $brand, __('sana_home.'.$key));
    $defaultPhoto = null;
    $searchMap = $profiles->mapWithKeys(function ($p) {
        $blob = mb_strtolower(implode(' ', array_filter([
            $p->user->name ?? '',
            $p->headline ?? '',
            $p->bio ?? '',
            implode(' ', $p->skills_list ?? []),
        ])));
        return [(int) $p->user->id => $blob];
    });
    $totalCourses = (int) $profiles->sum('courses_count');
@endphp
<!DOCTYPE html>
<html lang="ar" dir="rtl">
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
    @include('landing.sana.subpages-theme')
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="sana-home sana-courses-page"
      x-data="{
        searchQuery: '',
        searchMap: @js($searchMap),
        profileVisible(userId) {
          const q = this.searchQuery.toLowerCase().trim();
          if (!q) return true;
          return (this.searchMap[userId] || '').includes(q);
        },
        get visibleCount() {
          return Object.keys(this.searchMap).filter((id) => this.profileVisible(Number(id))).length;
        }
      }">

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
                    <span>{{ __('public.instructors_page_title') }}</span>
                </nav>
                <span class="sana-sub-hero__eyebrow"><i class="fas fa-chalkboard-user"></i> {{ $tr('instructors.badge') }}</span>
                <h1 class="sana-sub-hero__title">
                    {{ $tr('instructors.title') }} <span class="hl">{{ $tr('instructors.title_highlight') }}</span>
                </h1>
                <p class="sana-sub-hero__sub">{{ __('public.instructors_subtitle') }}</p>
                <div class="sana-sub-hero__actions">
                    <a href="{{ route('public.courses') }}" class="sana-btn sana-btn--yellow"><i class="fas fa-book-open"></i> {{ $tr('hero.cta_courses') }}</a>
                    <a href="{{ route('register') }}" class="sana-btn sana-btn--white-outline"><i class="fas fa-user-plus"></i> {{ __('public.instructors_cta_register') }}</a>
                </div>
            </div>
            <div class="sana-sub-hero__stats">
                <div class="sana-sub-hero__stat">
                    <strong>{{ $profiles->count() }}</strong>
                    <span>{{ __('public.stat_instructors') }}</span>
                </div>
                <div class="sana-sub-hero__stat">
                    <strong>{{ $totalCourses }}</strong>
                    <span>{{ $tr('instructors.courses') }}</span>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="sana-section">
    <div class="sana-container">
        @if($profiles->isNotEmpty())
        <div class="sana-sub-toolbar sana-reveal">
            <p>{{ __('public.instructors_ranking_hint') }}</p>
            <div class="sana-sub-search">
                <input type="search" x-model="searchQuery" placeholder="{{ __('public.instructors_search_placeholder') }}" aria-label="{{ __('public.instructors_search_placeholder') }}">
                <i class="fas fa-search"></i>
            </div>
        </div>

        <div class="sana-inst-grid">
            @foreach($profiles as $p)
            @php
                $userId = (int) $p->user->id;
                $photo = $p->photo_url;
                $skills = array_slice($p->skills_list ?? [], 0, 3);
                $skillsMore = max(0, count($p->skills_list ?? []) - 3);
            @endphp
            <a href="{{ route('public.instructors.show', $p->user) }}"
               class="sana-inst-card sana-reveal"
               x-show="profileVisible({{ $userId }})"
               x-cloak>
                <div class="sana-inst-card__photo">
                    @if($photo)
                        <img src="{{ $photo }}" alt="{{ $p->user->name }}" loading="lazy">
                    @else
                        <span class="av">{{ mb_substr($p->user->name ?? 'م', 0, 1) }}</span>
                    @endif
                    @if(!empty($p->marketing_featured_today))
                        <span class="sana-inst-card__badge"><i class="fas fa-bolt"></i> {{ __('public.instructor_featured_badge') }}</span>
                    @endif
                    @if(($p->courses_count ?? 0) > 0)
                        <span class="sana-inst-card__pill">{{ (int) $p->courses_count }} {{ $tr('instructors.courses') }}</span>
                    @endif
                </div>
                <div class="sana-inst-card__body">
                    <strong>{{ $p->user->name }}</strong>
                    <p class="sana-inst-card__headline">{{ $p->headline ?? __('public.instructor_fallback') }}</p>
                    @if(count($skills) > 0)
                    <div class="sana-inst-card__skills">
                        @foreach($skills as $skill)<span>{{ $skill }}</span>@endforeach
                        @if($skillsMore > 0)<span>+{{ $skillsMore }}</span>@endif
                    </div>
                    @endif
                    @if($p->bio)
                    <p class="sana-inst-card__bio">{{ Str::limit(strip_tags($p->bio), 80) }}</p>
                    @endif
                    <span class="sana-inst-card__link">{{ __('public.view_instructor_profile') }} <i class="fas fa-arrow-left"></i></span>
                </div>
            </a>
            @endforeach
        </div>

        <div class="sana-sub-empty sana-reveal" x-show="searchQuery.trim() && visibleCount === 0" x-cloak style="margin-top:24px">
            <div class="sana-sub-empty__icon"><i class="fas fa-magnifying-glass"></i></div>
            <h3 style="font-weight:900;margin:0 0 8px">{{ __('public.no_results') }}</h3>
            <p style="color:var(--muted);font-size:0.88rem;margin:0">{{ __('public.no_results_hint') }}</p>
        </div>
        @else
        <div class="sana-sub-empty sana-reveal">
            <div class="sana-sub-empty__icon"><i class="fas fa-chalkboard-teacher"></i></div>
            <h3 style="font-weight:900;margin:0 0 8px">{{ __('public.no_instructors') }}</h3>
            <p style="color:var(--muted);font-size:0.88rem;margin:0 0 20px">{{ __('public.instructors_empty_hint') }}</p>
            <a href="{{ route('home') }}" class="sana-btn sana-btn--purple"><i class="fas fa-home"></i> {{ $tr('nav.home') }}</a>
        </div>
        @endif
    </div>
</section>

<section class="sana-sub-final">
    <div class="sana-container sana-reveal">
        <div class="sana-sub-final__box">
            <h2>{{ __('public.instructors_cta_title') }}</h2>
            <p>{{ __('public.instructors_cta_subtitle') }}</p>
            <div class="sana-sub-final__actions">
                <a href="{{ route('register') }}" class="sana-btn sana-btn--yellow">{{ __('public.instructors_cta_register') }} <i class="fas fa-arrow-left"></i></a>
                <a href="{{ route('public.courses') }}" class="sana-btn sana-btn--ghost-light">{{ $tr('hero.cta_courses') }} <i class="fas fa-arrow-left"></i></a>
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
