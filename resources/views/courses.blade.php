@php
    $brand = config('app.name', 'Sana');
    $bc = config('brand.colors');
    $tr = fn (string $key) => str_replace(':brand', $brand, __('sana_home.'.$key));
    $tagClasses = ['edu-tag-green', 'edu-tag-orange', 'edu-tag-purple', 'edu-tag-blue'];
    $categoriesJson = ($courseFilterCategories ?? collect())->map(fn ($c) => [
        'id' => $c->id,
        'name' => $c->name,
    ])->values();
@endphp
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
    <title>{{ !empty($savedOnly) ? __('public.saved_courses_page_title') : __('public.courses_page_title') }} - {{ $brand }}</title>
    <meta name="description" content="{{ __('public.courses_subtitle') }}">
    <meta name="theme-color" content="{{ $bc['blue'] }}">
    <link rel="canonical" href="{{ url('/courses') }}">
    <meta property="og:title" content="{{ __('public.courses_page_title') }} - {{ $brand }}">
    <meta property="og:description" content="{{ __('public.courses_subtitle') }}">
    <meta property="og:image" content="{{ asset('images/og-image.jpg') }}">
    @include('partials.favicon-links')
    @include('partials.seo-jsonld', ['jsonldType' => 'website'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @include('landing.eduvalt.theme')
    @include('landing.eduvalt.courses-page')
    @include('partials.rtl-base')
    @include('landing.eduvalt.partials.course-favorites-init')
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="antialiased bg-white"
      x-data="{
        searchQuery: '',
        selectedCategoryId: new URLSearchParams(window.location.search).get('category') || '',
        showFreeOnly: false,
        showFeaturedOnly: false,
        viewMode: 'grid',
        savedIds: @js($savedCourseIds ?? []),
        courses: @js($courses ?? []),
        categories: @js($categoriesJson),
        get filteredCourses() {
          const q = this.searchQuery.toLowerCase().trim();
          const cat = this.selectedCategoryId;
          return this.courses.filter(c => {
            const matchQ = !q || (c.title && c.title.toLowerCase().includes(q)) || (c.description && c.description.toLowerCase().includes(q));
            const matchC = !cat || String(c.course_category_id || '') === String(cat);
            const matchFree = !this.showFreeOnly || c.is_free || (!c.price || c.price <= 0);
            const matchFeat = !this.showFeaturedOnly || c.is_featured;
            return matchQ && matchC && matchFree && matchFeat;
          });
        },
        countForCategory(id) {
          if (!id) return this.courses.length;
          return this.courses.filter(c => String(c.course_category_id || '') === String(id)).length;
        },
        resetFilters() {
          this.searchQuery = '';
          this.selectedCategoryId = '';
          this.showFreeOnly = false;
          this.showFeaturedOnly = false;
        },
        courseUrl(id) { return '{{ url('/course') }}/' + id; },
        cardImage(c) { return c.card_image_url || c.thumbnail_url || ''; },
        catLabel(c) {
          return (c.course_category && c.course_category.name) || (c.academic_subject && c.academic_subject.name) || '';
        },
        priceHtml(c) {
          if (c.contact_support_for_pricing) return 'whatsapp';
          if (c.is_free || !c.price || c.price <= 0) return 'free';
          if (c.has_promo_price && c.sale_price < c.price) return 'sale';
          return 'normal';
        },
        isSaved(id) { return this.savedIds.includes(Number(id)); },
        toggleFavorite(id, event) {
          const self = this;
          window.SanaCourseFavorites.toggle(id, event.currentTarget, function (data) {
            if (data && data.ids) self.savedIds = data.ids.map(Number);
          });
        }
      }">
<div id="edu-preloader" aria-hidden="true"><div class="edu-preloader-spinner"></div></div>
<div id="scroll-progress"></div>

@include('landing.eduvalt.navbar')

<main class="pt-[76px] lg:pt-[84px]">

{{-- PAGE HERO (مختصر — مختلف عن الصفحة الرئيسية) --}}
<section class="edu-courses-page-hero py-8 lg:py-10">
    <div class="edu-container-full">
        <div class="edu-courses-inner">
        <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6 reveal">
            <div class="max-w-2xl">
                <nav class="edu-breadcrumb" aria-label="مسار التنقل">
                    <a href="{{ route('home') }}">{{ $tr('nav.home') }}</a>
                    <i class="fas fa-chevron-left text-[10px] opacity-50"></i>
                    <span class="text-slate-800 font-semibold">{{ !empty($savedOnly) ? __('public.saved_courses_page_title') : __('public.courses_page_title') }}</span>
                </nav>
                @if(!empty($savedOnly))
                    <span class="edu-sub-title">{{ __('public.saved_courses_page_title') }}</span>
                    <h1 class="edu-section-title text-slate-900 mt-1">{{ __('public.saved_courses_page_title') }}</h1>
                    <p class="text-slate-600 leading-8 mt-3 text-sm lg:text-base">{{ __('public.saved_courses_subtitle') }}</p>
                @else
                    <span class="edu-sub-title">{{ $tr('courses.badge') }}</span>
                    <h1 class="edu-section-title text-slate-900 mt-1">
                        {{ __('public.courses_hero') }}
                        @include('landing.eduvalt.partials.title-mark', ['text' => __('public.courses_hero_highlight')])
                    </h1>
                    <p class="text-slate-600 leading-8 mt-3 text-sm lg:text-base">{{ __('public.courses_subtitle') }}</p>
                @endif
            </div>
            <div class="flex flex-wrap gap-3 shrink-0">
                <div class="edu-card px-5 py-4 min-w-[120px] text-center">
                    <p class="text-2xl font-black text-[var(--edu-primary)]" x-text="courses.length">0</p>
                    <p class="text-xs text-slate-500 mt-1">{{ __('public.courses_stats_available') }}</p>
                </div>
                <div class="edu-card px-5 py-4 min-w-[120px] text-center bg-[var(--edu-primary-light)] border-[var(--edu-primary)]/20">
                    <p class="text-2xl font-black text-[var(--edu-primary)]" x-text="courses.filter(c=>c.is_featured).length">0</p>
                    <p class="text-xs text-slate-600 mt-1">{{ __('public.courses_stats_featured') }}</p>
                </div>
            </div>
        </div>
        </div>
    </div>
</section>

{{-- MAIN: SIDEBAR + GRID --}}
<section class="py-10 lg:py-14 bg-[#F8FAFC]">
    <div class="edu-container-full">
        <div class="edu-courses-inner">
        <div class="edu-courses-layout">

            {{-- SIDEBAR FILTERS --}}
            <aside class="edu-courses-sidebar reveal">
                <div class="edu-filter-panel space-y-5">
                    <div>
                        <label class="text-xs font-bold text-slate-500 mb-2 block">{{ __('public.search_course_placeholder') }}</label>
                        <div class="relative">
                            <input type="search" x-model="searchQuery" class="edu-filter-search" placeholder="{{ __('public.search_course_placeholder') }}">
                            <i class="fas fa-search absolute top-1/2 -translate-y-1/2 start-3 text-slate-400 text-sm"></i>
                        </div>
                    </div>

                    <div>
                        <p class="text-xs font-bold text-slate-500 mb-2">{{ $tr('categories.badge') }}</p>
                        <div class="space-y-1">
                            <button type="button" @click="selectedCategoryId = ''"
                                class="edu-cat-filter" :class="selectedCategoryId === '' && 'is-active'">
                                <span>{{ $tr('courses.tab_all') }}</span>
                                <span class="count" x-text="countForCategory('')"></span>
                            </button>
                            <template x-for="cat in categories" :key="cat.id">
                                <button type="button" @click="selectedCategoryId = String(cat.id)"
                                    class="edu-cat-filter" :class="selectedCategoryId === String(cat.id) && 'is-active'">
                                    <span x-text="cat.name"></span>
                                    <span class="count" x-text="countForCategory(cat.id)"></span>
                                </button>
                            </template>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-100 space-y-1">
                        <label class="edu-check-row">
                            <input type="checkbox" x-model="showFreeOnly">
                            <span>{{ __('public.free_price') }} فقط</span>
                        </label>
                        <label class="edu-check-row">
                            <input type="checkbox" x-model="showFeaturedOnly">
                            <span>{{ __('public.featured_badge') }} فقط</span>
                        </label>
                    </div>

                    <button type="button" @click="resetFilters()" class="edu-btn-outline w-full justify-center text-sm py-2.5">
                        <i class="fas fa-rotate-left text-xs"></i> إعادة تعيين
                    </button>
                </div>
            </aside>

            {{-- RESULTS --}}
            <div class="min-w-0 reveal">
                <div class="edu-toolbar">
                    <p class="text-sm text-slate-600">
                        <span class="font-bold text-slate-900" x-text="filteredCourses.length">0</span>
                        {{ __('public.courses_stats_available') }}
                    </p>
                    <div class="edu-view-toggle" role="group" aria-label="طريقة العرض">
                        <button type="button" class="edu-view-btn" :class="viewMode === 'grid' && 'is-active'" @click="viewMode = 'grid'" title="شبكة">
                            <i class="fas fa-grid-2"></i>
                        </button>
                        <button type="button" class="edu-view-btn" :class="viewMode === 'list' && 'is-active'" @click="viewMode = 'list'" title="قائمة">
                            <i class="fas fa-list"></i>
                        </button>
                    </div>
                </div>

                {{-- روابط سريعة مفيدة --}}
                <div class="grid sm:grid-cols-3 gap-4 mb-6">
                    <a href="{{ route('register') }}" class="edu-quick-action group">
                        <span class="edu-quick-action-icon" style="background:var(--edu-primary-light);color:var(--edu-primary)"><i class="fas fa-calendar-check"></i></span>
                        <span class="min-w-0">
                            <span class="block font-bold text-slate-900 text-sm group-hover:text-[var(--edu-primary)]">{{ __('public.courses_quick_book_title') }}</span>
                            <span class="block text-xs text-slate-500 mt-1 leading-5">{{ __('public.courses_quick_book_desc') }}</span>
                        </span>
                        <i class="fas fa-arrow-left text-slate-300 text-xs shrink-0 group-hover:text-[var(--edu-primary)]"></i>
                    </a>
                    <a href="{{ route('public.pricing') }}" class="edu-quick-action group">
                        <span class="edu-quick-action-icon" style="background:var(--edu-accent-light);color:var(--edu-accent-dark)"><i class="fas fa-box-open"></i></span>
                        <span class="min-w-0">
                            <span class="block font-bold text-slate-900 text-sm group-hover:text-[var(--edu-primary)]">{{ __('public.courses_quick_packages_title') }}</span>
                            <span class="block text-xs text-slate-500 mt-1 leading-5">{{ __('public.courses_quick_packages_desc') }}</span>
                        </span>
                        <i class="fas fa-arrow-left text-slate-300 text-xs shrink-0 group-hover:text-[var(--edu-primary)]"></i>
                    </a>
                    <a href="{{ route('public.instructors.index') }}" class="edu-quick-action group">
                        <span class="edu-quick-action-icon" style="background:var(--edu-purple-light);color:var(--edu-purple)"><i class="fas fa-chalkboard-user"></i></span>
                        <span class="min-w-0">
                            <span class="block font-bold text-slate-900 text-sm group-hover:text-[var(--edu-primary)]">{{ __('public.courses_quick_teachers_title') }}</span>
                            <span class="block text-xs text-slate-500 mt-1 leading-5">{{ __('public.courses_quick_teachers_desc') }}</span>
                        </span>
                        <i class="fas fa-arrow-left text-slate-300 text-xs shrink-0 group-hover:text-[var(--edu-primary)]"></i>
                    </a>
                </div>
                <div class="edu-quick-tip mb-6 flex gap-3 items-start p-4 rounded-xl border border-blue-100 bg-[var(--edu-primary-light)]">
                    <span class="w-9 h-9 rounded-lg bg-white text-[var(--edu-primary)] flex items-center justify-center shrink-0"><i class="fas fa-lightbulb"></i></span>
                    <div>
                        <p class="text-sm font-bold text-slate-800">{{ __('public.courses_tip_title') }}</p>
                        <p class="text-xs text-slate-600 mt-1 leading-6">{{ __('public.courses_tip_text') }}</p>
                    </div>
                </div>

                {{-- GRID --}}
                <template x-if="filteredCourses.length > 0">
                    <div :class="viewMode === 'grid' ? 'grid sm:grid-cols-2 xl:grid-cols-3 gap-5' : 'flex flex-col gap-4'">
                        <template x-for="(course, index) in filteredCourses" :key="course.id">
                            <article class="edu-course-card group"
                               :class="viewMode === 'list' && 'list-mode'">
                                <div class="edu-course-thumb relative">
                                    <a :href="courseUrl(course.id)" class="block w-full h-full">
                                        <img :src="cardImage(course)" :alt="course.title" loading="lazy" class="w-full h-full object-cover">
                                    </a>
                                    <template x-if="course.is_featured">
                                        <span class="absolute top-3 start-3 edu-tag edu-tag-orange text-[10px] z-[11]">{{ __('public.featured_badge') }}</span>
                                    </template>
                                    <button type="button"
                                            class="edu-course-fav-btn"
                                            :class="isSaved(course.id) && 'is-saved'"
                                            :data-course-id="course.id"
                                            data-course-fav
                                            :data-saved="isSaved(course.id) ? '1' : '0'"
                                            :aria-pressed="isSaved(course.id) ? 'true' : 'false'"
                                            :title="isSaved(course.id) ? @js(__('public.course_unsave')) : @js(__('public.course_save'))"
                                            @click.prevent.stop="toggleFavorite(course.id, $event)">
                                        <i class="fa-heart" :class="isSaved(course.id) ? 'fas' : 'far'"></i>
                                    </button>
                                </div>
                                <div class="p-5 flex flex-col flex-1">
                                    <template x-if="catLabel(course)">
                                        <span class="text-xs font-bold text-[var(--edu-primary)] mb-2" x-text="catLabel(course)"></span>
                                    </template>
                                    <h3 class="font-bold text-slate-900 leading-snug mb-2">
                                        <a :href="courseUrl(course.id)" class="group-hover:text-[var(--edu-primary)] transition-colors"
                                           :class="viewMode === 'list' ? 'text-lg' : 'text-base line-clamp-2 block'"
                                           x-text="course.title"></a>
                                    </h3>
                                    <p class="text-sm text-slate-500 mb-4 flex-1 line-clamp-2" x-show="viewMode === 'grid'" x-text="(course.description || '').substring(0, 100)"></p>
                                    <div class="flex items-center gap-3 text-xs text-slate-400 mb-3">
                                        <span><i class="far fa-clock"></i> <span x-text="(course.lectures_count || 0) + ' {{ __('public.lecture_single') }}'"></span></span>
                                        <span x-show="course.duration_hours" x-text="course.duration_hours + ' {{ __('public.hours') }}'"></span>
                                    </div>
                                    <div class="flex items-center justify-between gap-2 pt-3 border-t border-slate-100 mt-auto">
                                        <div>
                                            <template x-if="priceHtml(course) === 'whatsapp'">
                                                <a :href="course.whatsapp_url" target="_blank" rel="noopener noreferrer"
                                                   @click.stop
                                                   class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-[#25D366] text-white text-sm font-bold hover:bg-[#1da851] transition-colors">
                                                    <i class="fab fa-whatsapp"></i>
                                                    {{ __('public.course_contact_support') }}
                                                </a>
                                            </template>
                                            <template x-if="priceHtml(course) === 'free'">
                                                <span class="text-sm font-bold text-emerald-600">{{ __('public.free_price') }}</span>
                                            </template>
                                            <template x-if="priceHtml(course) === 'sale'">
                                                <div>
                                                    <span class="text-xs text-slate-400 line-through" x-text="course.price + ' {{ __('public.currency') }}'"></span>
                                                    <span class="block text-lg font-black text-[var(--edu-primary)]" x-text="course.sale_price + ' {{ __('public.currency') }}'"></span>
                                                </div>
                                            </template>
                                            <template x-if="priceHtml(course) === 'normal'">
                                                <span class="text-lg font-black text-[var(--edu-primary)]" x-text="(course.sale_price != null ? course.sale_price : course.price) + ' {{ __('public.currency') }}'"></span>
                                            </template>
                                        </div>
                                        <a :href="courseUrl(course.id)" class="text-sm font-bold text-[var(--edu-primary)] shrink-0">{{ __('public.view_details') }} <i class="fas fa-arrow-left text-xs"></i></a>
                                    </div>
                                </div>
                            </article>
                        </template>
                    </div>
                </template>

                {{-- EMPTY --}}
                <div x-show="filteredCourses.length === 0" x-cloak class="edu-card p-12 text-center">
                    <div class="w-16 h-16 mx-auto rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center text-2xl mb-4">
                        <i class="fas fa-magnifying-glass"></i>
                    </div>
                    <h3 class="font-bold text-lg text-slate-900 mb-2">{{ !empty($savedOnly) ? __('public.saved_courses_empty') : __('public.no_results') }}</h3>
                    <p class="text-slate-500 text-sm mb-6">{{ !empty($savedOnly) ? __('public.saved_courses_empty_hint') : __('public.no_results_hint') }}</p>
                    <button type="button" @click="resetFilters()" class="edu-btn-primary text-sm">{{ __('public.browse_all_courses') }}</button>
                </div>
            </div>
        </div>
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="py-14 bg-white border-t border-slate-100">
    <div class="edu-container-full reveal">
        <div class="edu-courses-inner">
        <div class="edu-cta-wrap px-8 py-10 lg:py-12 flex flex-col md:flex-row items-center justify-between gap-6 text-white text-center md:text-start">
            <div class="relative z-10 max-w-lg">
                <h2 class="text-2xl font-extrabold mb-2">لم تجد الكورس المناسب؟</h2>
                <p class="text-white/90 text-sm leading-7">احجز حصة مباشرة مع معلم مختص أو استعرض باقات {{ $brand }} لأولياء الأمور.</p>
            </div>
            <div class="relative z-10 flex flex-wrap gap-3 justify-center">
                <a href="{{ route('register') }}" class="edu-btn-white text-sm">{{ $tr('hero.cta_book') }}</a>
                <a href="{{ route('public.pricing') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full font-bold text-sm text-white border-2 border-white/70 hover:bg-white/10">{{ $tr('hero.cta_packages') }}</a>
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
