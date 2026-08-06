@php
    $brand = config('app.name', 'Sana');
    $catalogIsEmpty = (bool) ($catalogIsEmpty ?? true);
    $catalogShowLaunch = $catalogIsEmpty && empty($savedOnly);
    $catalogShowCatalog = ! $catalogIsEmpty || ! empty($savedOnly);
    $categoriesJson = ($courseFilterCategories ?? collect())->map(fn ($c) => ['id' => $c->id, 'name' => $c->name])->values();
    $yearsJson = ($academicYears ?? collect())->map(fn ($y) => ['id' => $y->id, 'name' => $y->name])->values();
    $subjectsJson = ($academicSubjects ?? collect())->map(fn ($s) => ['id' => $s->id, 'name' => $s->name])->values();
    $pageTitle = !empty($savedOnly)
        ? __('public.saved_courses_page_title')
        : ($catalogShowLaunch ? __('public.courses_launch_page_title') : __('public.courses_explore_page_title'));
    $pageDesc = !empty($savedOnly)
        ? __('public.saved_courses_subtitle')
        : ($catalogShowLaunch
            ? __('public.courses_launch_subtitle')
            : __('public.courses_subtitle'));
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
    <title>{{ $pageTitle }} — {{ $brand }}</title>
    <meta name="description" content="{{ $pageDesc }}">
    <meta name="theme-color" content="#5B21B6">
    <link rel="canonical" href="{{ url('/courses') }}">
    @include('partials.favicon-links')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@800;900&family=Tajawal:wght@500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @include('partials.rtl-base')
    @include('landing.sana.theme')
    @include('landing.sana.courses-catalog-theme')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="{{ asset('js/course-favorites.js') }}"></script>
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="sana-home sana-courses-page{{ $catalogShowLaunch ? ' sana-courses-page--launch' : '' }}"
      x-data="sanaCoursesCatalog({
        courses: @js($courses ?? []),
        categories: @js($categoriesJson),
        years: @js($yearsJson),
        subjects: @js($subjectsJson),
        instructors: @js($instructors ?? []),
        catalogCategories: @js($catalogCategories ?? []),
        savedIds: @js($savedCourseIds ?? []),
        initialCategoryId: @js($initialCategoryId ?? ''),
        initialSubjectId: @js($initialSubjectId ?? ''),
        initialYearId: @js($initialYearId ?? ''),
        savedOnly: @json(!empty($savedOnly)),
        currency: @js(__('public.currency')),
        labels: {
          free: @js(__('public.free_price')),
          featured: @js(__('public.featured_badge')),
          viewDetails: @js(__('public.view_details')),
          contactSupport: @js(__('public.course_contact_support')),
          contactPrice: @js(__('public.contact_for_price')),
          noResults: @js(!empty($savedOnly) ? __('public.saved_courses_empty') : __('public.no_results')),
          noResultsHint: @js(!empty($savedOnly) ? __('public.saved_courses_empty_hint') : __('public.no_results_hint')),
          browseAll: @js(__('public.browse_all_courses')),
          lecture: @js(__('public.lecture_single')),
          hours: @js(__('public.hours')),
          save: @js(__('public.course_save')),
          unsave: @js(__('public.course_unsave')),
          progress: @js(__('public.course_progress_label')),
          continueLearning: @js(__('public.continue_learning')),
          startNow: @js(__('public.start_now')),
          instructor: @js(__('public.filter_instructor')),
          teacherFallback: @js(__('public.home_teacher_fallback')),
          descFallback: @js(__('public.course_desc_fallback')),
          courseSingular: @js(__('public.course_singular')),
          courseFeaturedStat: @js(__('public.course_featured_stat')),
          filteredResultsSuffix: @js(__('public.filtered_results_suffix')),
          showResults: @js(__('public.show_results_count', ['count' => '__COUNT__'])),
        }
      })">

<div id="sana-scroll-progress"></div>
@include('landing.sana.navbar')

{{-- Sticky mobile search --}}
@if($catalogShowCatalog)
<div class="sana-cat-sticky" :class="stickySearch && 'is-visible'" x-cloak>
    <div class="sana-container">
        <div class="sana-cat-sticky__row">
            <div style="position:relative;flex:1;min-width:0">
                <i class="fas fa-search" style="position:absolute;inset-inline-start:14px;top:50%;transform:translateY(-50%);color:var(--muted);font-size:0.85rem"></i>
                <input type="search" class="sana-cat-sticky__input" x-model="searchQuery" placeholder="{{ __('public.search_course_placeholder_mobile') }}" aria-label="{{ __('public.search_btn') }}">
            </div>
            <button type="button" class="sana-cat-sticky__filter-btn" @click="filterSheetOpen = true" aria-label="{{ __('public.filter_btn') }}">
                <i class="fas fa-sliders"></i>
                <span class="sana-cat-sticky__dot" x-show="hasActiveFilters" x-cloak></span>
            </button>
        </div>
    </div>
</div>
@endif

<main class="sana-cat-page">

    {{-- SECTION 1: HERO + SEARCH --}}
    <section class="sana-cat-hero" id="cat-hero">
        <div class="sana-cat-hero__dots"></div>
        <div class="sana-container sana-cat-hero__inner">
            <nav class="sana-cat-hero__breadcrumb" aria-label="{{ __('public.breadcrumb_aria') }}">
                <a href="{{ route('home') }}">{{ __('public.home') }}</a>
                <i class="fas fa-chevron-{{ app()->getLocale() === 'en' ? 'right' : 'left' }}" style="font-size:0.6rem;opacity:0.5"></i>
                <span>{{ $pageTitle }}</span>
            </nav>
            <h1 class="sana-cat-hero__title">
                @if(!empty($savedOnly))
                    {{ __('public.saved_courses_page_title') }}
                @elseif($catalogShowLaunch)
                    {{ __('public.courses_launch_badge') }}
                @else
                    {{ __('public.courses_hero') }} <span class="hl">{{ __('public.courses_hero_highlight') }}</span>
                @endif
            </h1>
            <p class="sana-cat-hero__desc">{{ $pageDesc }}</p>
            @if($catalogShowLaunch)
            <div class="sana-cat-hero__soon sana-reveal">
                <span class="sana-cat-hero__soon-badge"><i class="fas fa-hourglass-half"></i> {{ __('public.courses_launch_badge') }}</span>
                <p>{{ __('public.courses_launch_hint') }}</p>
                <div class="sana-cat-hero__soon-actions">
                    @include('landing.sana.partials.site-cta-buttons', ['hero' => true])
                </div>
            </div>
            @else
            <div class="sana-cat-hero__stats">
                <span class="sana-cat-hero__stat"><i class="fas fa-book-open"></i> <span x-text="courses.length">0</span> {{ __('public.course_available') }}</span>
                <span class="sana-cat-hero__stat" x-show="!savedOnly"><i class="fas fa-star"></i> <span x-text="courses.filter(c=>c.is_featured).length">0</span> {{ __('public.course_featured_stat') }}</span>
            </div>

            @unless(!empty($savedOnly))
            <div class="sana-cat-search sana-reveal">
                <div class="sana-cat-search__row">
                    <div class="sana-cat-search__input-wrap">
                        <i class="fas fa-search"></i>
                        <input type="search" class="sana-cat-search__input" x-model="searchQuery" placeholder="{{ __('public.search_course_placeholder') }}" aria-label="{{ __('public.search_courses_aria') }}">
                    </div>
                    <button type="button" class="sana-cat-search__btn" @click="scrollToCatalog()">
                        <i class="fas fa-search"></i> {{ __('public.search_btn') }}
                    </button>
                </div>
                <div class="sana-cat-search__filters">
                    <select class="sana-cat-search__select" x-model="selectedCategoryId" aria-label="{{ __('public.filter_category') }}">
                        <option value="">{{ __('public.all_course_categories') }}</option>
                        <template x-for="cat in categories" :key="cat.id">
                            <option :value="String(cat.id)" x-text="cat.name"></option>
                        </template>
                    </select>
                    <select class="sana-cat-search__select" x-model="selectedYearId" aria-label="{{ __('public.filter_grade') }}">
                        <option value="">{{ __('public.filter_all') }}</option>
                        <template x-for="y in years" :key="y.id">
                            <option :value="String(y.id)" x-text="y.name"></option>
                        </template>
                    </select>
                    <select class="sana-cat-search__select" x-model="selectedSubjectId" aria-label="{{ __('public.filter_subject') }}">
                        <option value="">{{ __('public.filter_all') }}</option>
                        <template x-for="s in subjects" :key="s.id">
                            <option :value="String(s.id)" x-text="s.name"></option>
                        </template>
                    </select>
                </div>
            </div>
            @endunless
            @endif
        </div>
    </section>

    {{-- SECTION 2: POPULAR CATEGORIES --}}
    @if($catalogShowCatalog && empty($savedOnly))
    <section class="sana-cat-categories" x-show="catalogCategories.length > 0" x-cloak>
        <div class="sana-container">
            <div class="sana-head sana-reveal" style="margin-bottom:24px">
                <h2 class="sana-head__title">{{ __('public.home_categories_title') }} <span class="hl">{{ __('public.home_categories_title_hl') }}</span></h2>
                <span class="sana-head__line"></span>
            </div>
            <div class="sana-cat-categories__scroll sana-reveal">
                <template x-for="cat in catalogCategories" :key="cat.id">
                    <button type="button" class="sana-cat-category"
                            :class="selectedCategoryId === String(cat.id) && 'is-active'"
                            :style="'background:' + cat.bg"
                            @click="selectCategory(cat.id)">
                        <span class="sana-cat-category__icon" x-text="cat.emoji"></span>
                        <span class="sana-cat-category__name" x-text="cat.name"></span>
                        <span class="sana-cat-category__count"><span x-text="cat.count"></span> <span x-text="labels.courseSingular"></span></span>
                    </button>
                </template>
            </div>
        </div>
    </section>

    {{-- SECTION 3: FEATURED COURSES --}}
    <section class="sana-cat-featured" x-show="featuredCourses.length > 0" x-cloak>
        <div class="sana-container">
            <div class="sana-head-row sana-reveal" style="margin-bottom:28px">
                <div class="sana-head">
                    <h2 class="sana-head__title">{{ __('public.courses_featured_prefix') }} <span class="hl">{{ __('public.courses_featured_hl') }}</span></h2>
                    <span class="sana-head__line"></span>
                </div>
            </div>
            <div class="sana-cat-featured__grid" x-show="featuredCourses.length > 0">
                <template x-for="course in featuredCourses" :key="'feat-' + course.id">
                    @include('landing.sana.partials.course-card-alpine')
                </template>
            </div>
        </div>
    </section>
    @endif

    {{-- SECTION 4: ALL COURSES --}}
    @if($catalogShowCatalog)
    <section class="sana-cat-catalog" id="all-courses">
        <div class="sana-container">
            <div class="sana-head sana-reveal" style="margin-bottom:32px">
                <h2 class="sana-head__title">
                    @if(!empty($savedOnly))
                        {{ __('public.saved_courses_section_prefix') }} <span class="hl">{{ __('public.saved_courses_section_hl') }}</span>
                    @else
                        {{ __('public.courses_all_prefix') }} <span class="hl">{{ __('public.courses_all_hl') }}</span>
                    @endif
                </h2>
                <span class="sana-head__line"></span>
            </div>

            <div class="sana-cat-layout">
                {{-- Desktop sidebar --}}
                <aside class="sana-cat-sidebar">
                    <h3 class="sana-cat-sidebar__title"><i class="fas fa-sliders"></i> {{ __('public.filter_results_title') }}</h3>
                    @include('landing.sana.courses.catalog-filters')
                </aside>

                {{-- Results --}}
                <div class="sana-cat-results">
                    <div class="sana-cat-results__toolbar">
                        <p class="sana-cat-results__count">
                            <strong x-text="filteredCourses.length">0</strong> <span x-text="labels.courseSingular"></span>
                            <span x-show="hasActiveFilters" x-text="labels.filteredResultsSuffix"></span>
                        </p>
                        <button type="button" class="sana-btn sana-btn--purple-outline sana-cat-filter-mobile-btn" style="padding:10px 16px;font-size:0.85rem;min-height:44px" @click="filterSheetOpen = true">
                            <i class="fas fa-sliders"></i> {{ __('public.filter_btn') }}
                        </button>
                    </div>

                    <div class="sana-cat-grid" x-show="filteredCourses.length > 0">
                        <template x-for="course in filteredCourses" :key="course.id">
                            @include('landing.sana.partials.course-card-alpine')
                        </template>
                    </div>

                    <div class="sana-cat-empty" x-show="filteredCourses.length === 0" x-cloak>
                        <div class="sana-cat-empty__icon"><i class="fas fa-magnifying-glass"></i></div>
                        <h3 style="font-weight:900;margin:0 0 8px" x-text="labels.noResults"></h3>
                        <p style="color:var(--muted);font-size:0.9rem;margin:0 0 20px" x-text="labels.noResultsHint"></p>
                        <button type="button" class="sana-btn sana-btn--yellow" @click="resetFilters()" x-text="labels.browseAll"></button>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- CTA --}}
    @if($catalogShowLaunch)
    <section class="sana-cat-cta">
        <div class="sana-container">
            <div class="sana-cat-cta__inner">
                <div>
                    <h2>{{ __('public.courses_launch_badge') }}</h2>
                    <p>{{ __('public.courses_launch_hint') }}</p>
                </div>
                <div class="sana-cat-cta__actions">
                    @include('landing.sana.partials.site-cta-buttons', ['hero' => true])
                </div>
            </div>
        </div>
    </section>
    @elseif(empty($savedOnly))
    <section class="sana-cat-cta">
        <div class="sana-container">
            <div class="sana-cat-cta__inner">
                <div>
                    <h2>{{ __('public.pricing_cta_title') }}</h2>
                    <p>{{ __('public.pricing_cta_sub') }}</p>
                </div>
                <div class="sana-cat-cta__actions">
                    <a href="{{ route('register') }}" class="sana-btn sana-btn--yellow">{{ __('public.courses_launch_cta_book') }}</a>
                    <a href="{{ route('public.pricing') }}" class="sana-btn sana-btn--white-outline">{{ __('public.packages_link') }}</a>
                </div>
            </div>
        </div>
    </section>
    @endif
</main>

@if($catalogShowCatalog)
{{-- Mobile filter sheet --}}
<div class="sana-cat-sheet-backdrop" :class="filterSheetOpen && 'is-open'" @click="filterSheetOpen = false" x-cloak></div>
<div class="sana-cat-sheet" :class="filterSheetOpen && 'is-open'" x-cloak role="dialog" aria-label="{{ __('public.filter_courses_aria') }}">
    <div class="sana-cat-sheet__handle"></div>
    <div class="sana-cat-sheet__head">
        <h3>{{ __('public.filter_courses_title') }}</h3>
        <button type="button" class="sana-cat-sheet__close" @click="filterSheetOpen = false" aria-label="{{ __('public.close_btn') }}"><i class="fas fa-times"></i></button>
    </div>
    @include('landing.sana.courses.catalog-filters')
    <button type="button" class="sana-cat-sheet__apply" @click="filterSheetOpen = false" x-text="labels.showResults.replace('__COUNT__', filteredCourses.length)"></button>
</div>
@endif

@include('landing.sana.footer')

<script>
function sanaCoursesCatalog(config) {
    return {
        ...config,
        searchQuery: '',
        selectedCategoryId: config.initialCategoryId || '',
        selectedYearId: config.initialYearId || '',
        selectedSubjectId: config.initialSubjectId || '',
        selectedLevel: '',
        selectedInstructorId: '',
        priceFilter: 'all',
        durationFilter: 'all',
        filterSheetOpen: false,
        stickySearch: false,

        init() {
            const self = this;
            window.addEventListener('scroll', function () {
                const hero = document.getElementById('cat-hero');
                if (hero) self.stickySearch = window.scrollY > hero.offsetHeight - 80;
            }, { passive: true });

            if (window.SanaCourseFavorites) {
                window.SanaCourseFavorites.init({
                    authenticated: @json(auth()->check()),
                    loginUrl: @json(route('login')),
                    toggleUrlTemplate: @json(url('/saved-courses/__ID__/toggle')),
                    syncUrl: @json(route('public.saved-courses.sync')),
                    savedIds: config.savedIds,
                });
            }

            if (config.initialCategoryId || config.initialSubjectId) {
                setTimeout(() => this.scrollToCatalog(), 400);
            }
        },

        get hasActiveFilters() {
            return this.searchQuery.trim() !== '' || this.selectedCategoryId !== '' ||
                this.selectedYearId !== '' || this.selectedSubjectId !== '' ||
                this.selectedLevel !== '' || this.selectedInstructorId !== '' ||
                this.priceFilter !== 'all' || this.durationFilter !== 'all';
        },

        get featuredCourses() {
            return this.courses.filter(c => c.is_featured).slice(0, 3);
        },

        get filteredCourses() {
            const q = this.searchQuery.toLowerCase().trim();
            return this.courses.filter(c => this.matchesFilters(c, q));
        },

        matchesFilters(c, q) {
            if (q) {
                const hay = [c.title, c.description, c.instructor && c.instructor.name, c.course_category && c.course_category.name, c.academic_subject && c.academic_subject.name]
                    .filter(Boolean).join(' ').toLowerCase();
                if (!hay.includes(q)) return false;
            }
            if (this.selectedCategoryId && String(c.course_category_id || '') !== String(this.selectedCategoryId)) return false;
            if (this.selectedYearId && String(c.academic_year_id || '') !== String(this.selectedYearId)) return false;
            if (this.selectedSubjectId && String(c.academic_subject_id || '') !== String(this.selectedSubjectId)) return false;
            if (this.selectedLevel && !this.levelMatches(c.level, this.selectedLevel)) return false;
            if (this.selectedInstructorId && String(c.instructor_id || '') !== String(this.selectedInstructorId)) return false;
            if (this.priceFilter === 'free' && !c.is_free) return false;
            if (this.priceFilter === 'paid' && c.is_free) return false;
            if (this.durationFilter === 'short' && (c.duration_hours > 10 || !c.duration_hours)) return false;
            if (this.durationFilter === 'medium' && (c.duration_hours <= 10 || c.duration_hours > 30)) return false;
            if (this.durationFilter === 'long' && c.duration_hours <= 30) return false;
            return true;
        },

        countForCategory(id) {
            return this.courses.filter(c => String(c.course_category_id || '') === String(id)).length;
        },

        selectCategory(id) {
            this.selectedCategoryId = String(id);
            this.scrollToCatalog();
        },

        resetFilters() {
            this.searchQuery = '';
            this.selectedCategoryId = '';
            this.selectedYearId = '';
            this.selectedSubjectId = '';
            this.selectedLevel = '';
            this.selectedInstructorId = '';
            this.priceFilter = 'all';
            this.durationFilter = 'all';
            this.filterSheetOpen = false;
        },

        scrollToCatalog() {
            document.getElementById('all-courses')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        },

        courseUrl(id) { return @json(url('/course')) + '/' + id; },
        cardImage(c) { return c.card_image_url || c.thumbnail_url || ''; },
        catLabel(c) { return (c.course_category && c.course_category.name) || (c.academic_subject && c.academic_subject.name) || ''; },
        formatCount(n) { return (n || 0).toLocaleString('ar-EG'); },

        levelMatches(level, selected) {
            const lv = level || 'beginner';
            const groups = {
                beginner: ['beginner', ''],
                intermediate: ['intermediate', 'medium'],
                advanced: ['advanced', 'expert'],
            };
            return (groups[selected] || [selected]).includes(lv);
        },

        priceHtml(c) {
            if (c.contact_support_for_pricing) return 'whatsapp';
            if (c.is_free || !c.price || c.price <= 0) return 'free';
            if (c.has_promo_price && c.sale_price < c.price) return 'sale';
            return 'normal';
        },

        priceLabel(c) {
            const t = this.priceHtml(c);
            if (t === 'free') return this.labels.free;
            if (t === 'whatsapp') return '<i class="fab fa-whatsapp"></i>';
            const p = c.sale_price != null ? c.sale_price : c.price;
            return p + ' ' + this.currency;
        },

        isSaved(id) { return this.savedIds.includes(Number(id)); },

        toggleFavorite(id, event) {
            const self = this;
            if (!window.SanaCourseFavorites) return;
            window.SanaCourseFavorites.toggle(id, event.currentTarget, function (data) {
                if (data && data.ids) self.savedIds = data.ids.map(Number);
            });
        },
    };
}
</script>
@include('landing.sana.scripts')
@include('partials.pwa-service-worker')
</body>
</html>
