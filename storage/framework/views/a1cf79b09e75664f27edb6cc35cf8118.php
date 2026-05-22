<?php
    $brand = config('app.name', 'Sana');
    $bc = config('brand.colors');
    $tr = fn (string $key) => str_replace(':brand', $brand, __('sana_home.'.$key));
    $tagClasses = ['edu-tag-green', 'edu-tag-orange', 'edu-tag-purple', 'edu-tag-blue'];
    $categoriesJson = ($courseFilterCategories ?? collect())->map(fn ($c) => [
        'id' => $c->id,
        'name' => $c->name,
    ])->values();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
    <title><?php echo e(!empty($savedOnly) ? __('public.saved_courses_page_title') : __('public.courses_page_title')); ?> - <?php echo e($brand); ?></title>
    <meta name="description" content="<?php echo e(__('public.courses_subtitle')); ?>">
    <meta name="theme-color" content="<?php echo e($bc['blue']); ?>">
    <link rel="canonical" href="<?php echo e(url('/courses')); ?>">
    <meta property="og:title" content="<?php echo e(__('public.courses_page_title')); ?> - <?php echo e($brand); ?>">
    <meta property="og:description" content="<?php echo e(__('public.courses_subtitle')); ?>">
    <meta property="og:image" content="<?php echo e(asset('images/og-image.jpg')); ?>">
    <?php echo $__env->make('partials.favicon-links', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('partials.seo-jsonld', ['jsonldType' => 'website'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <?php echo $__env->make('landing.eduvalt.theme', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('landing.eduvalt.courses-page', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('partials.rtl-base', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('landing.eduvalt.partials.course-favorites-init', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="antialiased bg-white"
      x-data="{
        searchQuery: '',
        selectedCategoryId: new URLSearchParams(window.location.search).get('category') || '',
        showFreeOnly: false,
        showFeaturedOnly: false,
        viewMode: 'grid',
        filtersOpen: false,
        savedIds: <?php echo \Illuminate\Support\Js::from($savedCourseIds ?? [])->toHtml() ?>,
        get hasActiveFilters() {
          return this.searchQuery.trim() !== '' || this.selectedCategoryId !== '' || this.showFreeOnly || this.showFeaturedOnly;
        },
        courses: <?php echo \Illuminate\Support\Js::from($courses ?? [])->toHtml() ?>,
        categories: <?php echo \Illuminate\Support\Js::from($categoriesJson)->toHtml() ?>,
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
        courseUrl(id) { return '<?php echo e(url('/course')); ?>/' + id; },
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

<?php echo $__env->make('landing.eduvalt.navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<main class="pt-[76px] lg:pt-[84px]">


<section class="edu-courses-page-hero py-8 lg:py-10">
    <div class="edu-container-full">
        <div class="edu-courses-inner">
        <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6 reveal">
            <div class="max-w-2xl">
                <nav class="edu-breadcrumb" aria-label="مسار التنقل">
                    <a href="<?php echo e(route('home')); ?>"><?php echo e($tr('nav.home')); ?></a>
                    <i class="fas fa-chevron-left text-[10px] opacity-50"></i>
                    <span class="text-slate-800 font-semibold"><?php echo e(!empty($savedOnly) ? __('public.saved_courses_page_title') : __('public.courses_page_title')); ?></span>
                </nav>
                <?php if(!empty($savedOnly)): ?>
                    <span class="edu-sub-title"><?php echo e(__('public.saved_courses_page_title')); ?></span>
                    <h1 class="edu-section-title text-slate-900 mt-1"><?php echo e(__('public.saved_courses_page_title')); ?></h1>
                    <p class="text-slate-600 leading-8 mt-3 text-sm lg:text-base"><?php echo e(__('public.saved_courses_subtitle')); ?></p>
                <?php else: ?>
                    <span class="edu-sub-title"><?php echo e($tr('courses.badge')); ?></span>
                    <h1 class="edu-section-title text-slate-900 mt-1">
                        <?php echo e(__('public.courses_hero')); ?>

                        <?php echo $__env->make('landing.eduvalt.partials.title-mark', ['text' => __('public.courses_hero_highlight')], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    </h1>
                    <p class="text-slate-600 leading-8 mt-3 text-sm lg:text-base"><?php echo e(__('public.courses_subtitle')); ?></p>
                <?php endif; ?>
            </div>
            <div class="grid grid-cols-2 gap-3 w-full lg:w-auto lg:flex lg:flex-wrap shrink-0">
                <div class="edu-card px-4 py-3 sm:px-5 sm:py-4 text-center">
                    <p class="text-xl sm:text-2xl font-black text-[var(--edu-primary)]" x-text="courses.length">0</p>
                    <p class="text-[11px] sm:text-xs text-slate-500 mt-1"><?php echo e(__('public.courses_stats_available')); ?></p>
                </div>
                <div class="edu-card px-4 py-3 sm:px-5 sm:py-4 text-center bg-[var(--edu-primary-light)] border-[var(--edu-primary)]/20">
                    <p class="text-xl sm:text-2xl font-black text-[var(--edu-primary)]" x-text="courses.filter(c=>c.is_featured).length">0</p>
                    <p class="text-[11px] sm:text-xs text-slate-600 mt-1"><?php echo e(__('public.courses_stats_featured')); ?></p>
                </div>
            </div>
        </div>
        </div>
    </div>
</section>


<section class="py-10 lg:py-14 bg-[#F8FAFC]">
    <div class="edu-container-full">
        <div class="edu-courses-inner">
        <div class="edu-courses-layout">

            
            <div class="edu-courses-mobile-bar lg:hidden reveal">
                <div class="relative flex-1 min-w-0">
                    <input type="search" x-model="searchQuery" class="edu-filter-search edu-filter-search--mobile" placeholder="<?php echo e(__('public.search_course_placeholder')); ?>" aria-label="<?php echo e(__('public.search_course_placeholder')); ?>">
                    <i class="fas fa-search absolute top-1/2 -translate-y-1/2 start-3 text-slate-400 text-sm pointer-events-none"></i>
                </div>
                <button type="button"
                        class="edu-mobile-filter-btn"
                        :class="filtersOpen && 'is-open'"
                        @click="filtersOpen = !filtersOpen"
                        :aria-expanded="filtersOpen">
                    <i class="fas fa-sliders"></i>
                    <span class="hidden sm:inline">فلترة</span>
                    <span class="edu-mobile-filter-dot" x-show="hasActiveFilters" x-cloak></span>
                </button>
            </div>

            
            <div class="edu-courses-mobile-filters lg:hidden reveal"
                 x-show="filtersOpen"
                 x-cloak
                 @click.outside="if (window.innerWidth < 1024) filtersOpen = false">
                <?php echo $__env->make('landing.eduvalt.partials.courses-filter-panel', ['mobile' => true], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </div>

            
            <aside class="edu-courses-sidebar hidden lg:block reveal">
                <?php echo $__env->make('landing.eduvalt.partials.courses-filter-panel', ['mobile' => false], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </aside>

            
            <div class="min-w-0 reveal edu-courses-results">
                <div class="edu-toolbar">
                    <p class="text-sm text-slate-600">
                        <span class="font-bold text-slate-900" x-text="filteredCourses.length">0</span>
                        <?php echo e(__('public.courses_stats_available')); ?>

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

                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4 mb-6">
                    <a href="<?php echo e(route('register')); ?>" class="edu-quick-action group">
                        <span class="edu-quick-action-icon" style="background:var(--edu-primary-light);color:var(--edu-primary)"><i class="fas fa-calendar-check"></i></span>
                        <span class="min-w-0">
                            <span class="block font-bold text-slate-900 text-sm group-hover:text-[var(--edu-primary)]"><?php echo e(__('public.courses_quick_book_title')); ?></span>
                            <span class="block text-xs text-slate-500 mt-1 leading-5"><?php echo e(__('public.courses_quick_book_desc')); ?></span>
                        </span>
                        <i class="fas fa-arrow-left text-slate-300 text-xs shrink-0 group-hover:text-[var(--edu-primary)]"></i>
                    </a>
                    <a href="<?php echo e(route('public.pricing')); ?>" class="edu-quick-action group">
                        <span class="edu-quick-action-icon" style="background:var(--edu-accent-light);color:var(--edu-accent-dark)"><i class="fas fa-box-open"></i></span>
                        <span class="min-w-0">
                            <span class="block font-bold text-slate-900 text-sm group-hover:text-[var(--edu-primary)]"><?php echo e(__('public.courses_quick_packages_title')); ?></span>
                            <span class="block text-xs text-slate-500 mt-1 leading-5"><?php echo e(__('public.courses_quick_packages_desc')); ?></span>
                        </span>
                        <i class="fas fa-arrow-left text-slate-300 text-xs shrink-0 group-hover:text-[var(--edu-primary)]"></i>
                    </a>
                    <a href="<?php echo e(route('public.instructors.index')); ?>" class="edu-quick-action group">
                        <span class="edu-quick-action-icon" style="background:var(--edu-purple-light);color:var(--edu-purple)"><i class="fas fa-chalkboard-user"></i></span>
                        <span class="min-w-0">
                            <span class="block font-bold text-slate-900 text-sm group-hover:text-[var(--edu-primary)]"><?php echo e(__('public.courses_quick_teachers_title')); ?></span>
                            <span class="block text-xs text-slate-500 mt-1 leading-5"><?php echo e(__('public.courses_quick_teachers_desc')); ?></span>
                        </span>
                        <i class="fas fa-arrow-left text-slate-300 text-xs shrink-0 group-hover:text-[var(--edu-primary)]"></i>
                    </a>
                </div>
                <div class="edu-quick-tip mb-6 flex gap-3 items-start p-4 rounded-xl border border-blue-100 bg-[var(--edu-primary-light)]">
                    <span class="w-9 h-9 rounded-lg bg-white text-[var(--edu-primary)] flex items-center justify-center shrink-0"><i class="fas fa-lightbulb"></i></span>
                    <div>
                        <p class="text-sm font-bold text-slate-800"><?php echo e(__('public.courses_tip_title')); ?></p>
                        <p class="text-xs text-slate-600 mt-1 leading-6"><?php echo e(__('public.courses_tip_text')); ?></p>
                    </div>
                </div>

                
                <template x-if="filteredCourses.length > 0">
                    <div :class="viewMode === 'grid' ? 'grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-5' : 'flex flex-col gap-4'">
                        <template x-for="(course, index) in filteredCourses" :key="course.id">
                            <article class="edu-course-card group"
                               :class="viewMode === 'list' && 'list-mode'">
                                <div class="edu-course-thumb relative">
                                    <a :href="courseUrl(course.id)" class="block w-full h-full">
                                        <img :src="cardImage(course)" :alt="course.title" loading="lazy" class="w-full h-full object-cover">
                                    </a>
                                    <template x-if="course.is_featured">
                                        <span class="absolute top-3 start-3 edu-tag edu-tag-orange text-[10px] z-[11]"><?php echo e(__('public.featured_badge')); ?></span>
                                    </template>
                                    <button type="button"
                                            class="edu-course-fav-btn"
                                            :class="isSaved(course.id) && 'is-saved'"
                                            :data-course-id="course.id"
                                            data-course-fav
                                            :data-saved="isSaved(course.id) ? '1' : '0'"
                                            :aria-pressed="isSaved(course.id) ? 'true' : 'false'"
                                            :title="isSaved(course.id) ? <?php echo \Illuminate\Support\Js::from(__('public.course_unsave'))->toHtml() ?> : <?php echo \Illuminate\Support\Js::from(__('public.course_save'))->toHtml() ?>"
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
                                        <span><i class="far fa-clock"></i> <span x-text="(course.lectures_count || 0) + ' <?php echo e(__('public.lecture_single')); ?>'"></span></span>
                                        <span x-show="course.duration_hours" x-text="course.duration_hours + ' <?php echo e(__('public.hours')); ?>'"></span>
                                    </div>
                                    <div class="flex items-center justify-between gap-2 pt-3 border-t border-slate-100 mt-auto">
                                        <div>
                                            <template x-if="priceHtml(course) === 'whatsapp'">
                                                <a :href="course.whatsapp_url" target="_blank" rel="noopener noreferrer"
                                                   @click.stop
                                                   class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-[#25D366] text-white text-sm font-bold hover:bg-[#1da851] transition-colors">
                                                    <i class="fab fa-whatsapp"></i>
                                                    <?php echo e(__('public.course_contact_support')); ?>

                                                </a>
                                            </template>
                                            <template x-if="priceHtml(course) === 'free'">
                                                <span class="text-sm font-bold text-emerald-600"><?php echo e(__('public.free_price')); ?></span>
                                            </template>
                                            <template x-if="priceHtml(course) === 'sale'">
                                                <div>
                                                    <span class="text-xs text-slate-400 line-through" x-text="course.price + ' <?php echo e(__('public.currency')); ?>'"></span>
                                                    <span class="block text-lg font-black text-[var(--edu-primary)]" x-text="course.sale_price + ' <?php echo e(__('public.currency')); ?>'"></span>
                                                </div>
                                            </template>
                                            <template x-if="priceHtml(course) === 'normal'">
                                                <span class="text-lg font-black text-[var(--edu-primary)]" x-text="(course.sale_price != null ? course.sale_price : course.price) + ' <?php echo e(__('public.currency')); ?>'"></span>
                                            </template>
                                        </div>
                                        <a :href="courseUrl(course.id)" class="text-sm font-bold text-[var(--edu-primary)] shrink-0"><?php echo e(__('public.view_details')); ?> <i class="fas fa-arrow-left text-xs"></i></a>
                                    </div>
                                </div>
                            </article>
                        </template>
                    </div>
                </template>

                
                <div x-show="filteredCourses.length === 0" x-cloak class="edu-card p-12 text-center">
                    <div class="w-16 h-16 mx-auto rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center text-2xl mb-4">
                        <i class="fas fa-magnifying-glass"></i>
                    </div>
                    <h3 class="font-bold text-lg text-slate-900 mb-2"><?php echo e(!empty($savedOnly) ? __('public.saved_courses_empty') : __('public.no_results')); ?></h3>
                    <p class="text-slate-500 text-sm mb-6"><?php echo e(!empty($savedOnly) ? __('public.saved_courses_empty_hint') : __('public.no_results_hint')); ?></p>
                    <button type="button" @click="resetFilters()" class="edu-btn-primary text-sm"><?php echo e(__('public.browse_all_courses')); ?></button>
                </div>
            </div>
        </div>
        </div>
    </div>
</section>


<section class="py-14 bg-white border-t border-slate-100">
    <div class="edu-container-full reveal">
        <div class="edu-courses-inner">
        <div class="edu-cta-wrap px-5 sm:px-8 py-8 sm:py-10 lg:py-12 flex flex-col md:flex-row items-center justify-between gap-6 text-white text-center md:text-start">
            <div class="relative z-10 max-w-lg">
                <h2 class="text-2xl font-extrabold mb-2">لم تجد الكورس المناسب؟</h2>
                <p class="text-white/90 text-sm leading-7">احجز حصة مباشرة مع معلم مختص أو استعرض باقات <?php echo e($brand); ?> لأولياء الأمور.</p>
            </div>
            <div class="relative z-10 flex flex-wrap gap-3 justify-center">
                <a href="<?php echo e(route('register')); ?>" class="edu-btn-white text-sm"><?php echo e($tr('hero.cta_book')); ?></a>
                <a href="<?php echo e(route('public.pricing')); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full font-bold text-sm text-white border-2 border-white/70 hover:bg-white/10"><?php echo e($tr('hero.cta_packages')); ?></a>
            </div>
        </div>
        </div>
    </div>
</section>

</main>

<?php echo $__env->make('landing.eduvalt.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

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
<?php echo $__env->make('partials.pwa-service-worker', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\sana\resources\views\courses.blade.php ENDPATH**/ ?>