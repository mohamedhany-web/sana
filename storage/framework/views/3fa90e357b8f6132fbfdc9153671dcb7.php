<?php
    $brand = config('app.name', 'Sana');
    $catalogIsEmpty = (bool) ($catalogIsEmpty ?? true);
    $catalogShowLaunch = $catalogIsEmpty && empty($savedOnly);
    $catalogShowCatalog = ! $catalogIsEmpty || ! empty($savedOnly);
    $categoriesJson = ($courseFilterCategories ?? collect())->map(fn ($c) => ['id' => $c->id, 'name' => $c->name])->values();
    $yearsJson = ($academicYears ?? collect())->map(fn ($y) => ['id' => $y->id, 'name' => $y->name])->values();
    $subjectsJson = ($academicSubjects ?? collect())->map(fn ($s) => ['id' => $s->id, 'name' => $s->name])->values();
    $pageTitle = !empty($savedOnly)
        ? __('public.saved_courses_page_title')
        : ($catalogShowLaunch ? __('public.courses_launch_page_title') : 'استكشف الدورات');
    $pageDesc = !empty($savedOnly)
        ? __('public.saved_courses_subtitle')
        : ($catalogShowLaunch
            ? __('public.courses_launch_subtitle')
            : 'اكتشف دورات تعليمية ممتعة ومنظّمة — ابحث حسب المادة، المرحلة، أو المعلّم وابدأ رحلتك فوراً.');
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
    <title><?php echo e($pageTitle); ?> — <?php echo e($brand); ?></title>
    <meta name="description" content="<?php echo e($pageDesc); ?>">
    <meta name="theme-color" content="#5B21B6">
    <link rel="canonical" href="<?php echo e(url('/courses')); ?>">
    <?php echo $__env->make('partials.favicon-links', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@800;900&family=Tajawal:wght@500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <?php echo $__env->make('partials.rtl-base', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('landing.sana.theme', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('landing.sana.courses-catalog-theme', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <script src="<?php echo e(asset('js/course-favorites.js')); ?>"></script>
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="sana-home sana-courses-page<?php echo e($catalogShowLaunch ? ' sana-courses-page--launch' : ''); ?>"
      x-data="sanaCoursesCatalog({
        courses: <?php echo \Illuminate\Support\Js::from($courses ?? [])->toHtml() ?>,
        categories: <?php echo \Illuminate\Support\Js::from($categoriesJson)->toHtml() ?>,
        years: <?php echo \Illuminate\Support\Js::from($yearsJson)->toHtml() ?>,
        subjects: <?php echo \Illuminate\Support\Js::from($subjectsJson)->toHtml() ?>,
        instructors: <?php echo \Illuminate\Support\Js::from($instructors ?? [])->toHtml() ?>,
        catalogCategories: <?php echo \Illuminate\Support\Js::from($catalogCategories ?? [])->toHtml() ?>,
        savedIds: <?php echo \Illuminate\Support\Js::from($savedCourseIds ?? [])->toHtml() ?>,
        initialCategoryId: <?php echo \Illuminate\Support\Js::from($initialCategoryId ?? '')->toHtml() ?>,
        initialSubjectId: <?php echo \Illuminate\Support\Js::from($initialSubjectId ?? '')->toHtml() ?>,
        initialYearId: <?php echo \Illuminate\Support\Js::from($initialYearId ?? '')->toHtml() ?>,
        savedOnly: <?php echo json_encode(!empty($savedOnly), 15, 512) ?>,
        currency: <?php echo \Illuminate\Support\Js::from(__('public.currency'))->toHtml() ?>,
        labels: {
          free: <?php echo \Illuminate\Support\Js::from(__('public.free_price'))->toHtml() ?>,
          featured: <?php echo \Illuminate\Support\Js::from(__('public.featured_badge'))->toHtml() ?>,
          viewDetails: <?php echo \Illuminate\Support\Js::from(__('public.view_details'))->toHtml() ?>,
          contactSupport: <?php echo \Illuminate\Support\Js::from(__('public.course_contact_support'))->toHtml() ?>,
          noResults: <?php echo \Illuminate\Support\Js::from(!empty($savedOnly) ? __('public.saved_courses_empty') : __('public.no_results'))->toHtml() ?>,
          noResultsHint: <?php echo \Illuminate\Support\Js::from(!empty($savedOnly) ? __('public.saved_courses_empty_hint') : __('public.no_results_hint'))->toHtml() ?>,
          browseAll: <?php echo \Illuminate\Support\Js::from(__('public.browse_all_courses'))->toHtml() ?>,
          lecture: <?php echo \Illuminate\Support\Js::from(__('public.lecture_single'))->toHtml() ?>,
          hours: <?php echo \Illuminate\Support\Js::from(__('public.hours'))->toHtml() ?>,
          save: <?php echo \Illuminate\Support\Js::from(__('public.course_save'))->toHtml() ?>,
          unsave: <?php echo \Illuminate\Support\Js::from(__('public.course_unsave'))->toHtml() ?>,
        }
      })">

<div id="sana-scroll-progress"></div>
<?php echo $__env->make('landing.sana.navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>


<?php if($catalogShowCatalog): ?>
<div class="sana-cat-sticky" :class="stickySearch && 'is-visible'" x-cloak>
    <div class="sana-container">
        <div class="sana-cat-sticky__row">
            <div style="position:relative;flex:1;min-width:0">
                <i class="fas fa-search" style="position:absolute;inset-inline-start:14px;top:50%;transform:translateY(-50%);color:var(--muted);font-size:0.85rem"></i>
                <input type="search" class="sana-cat-sticky__input" x-model="searchQuery" placeholder="ابحث عن دورة..." aria-label="بحث">
            </div>
            <button type="button" class="sana-cat-sticky__filter-btn" @click="filterSheetOpen = true" aria-label="فلترة">
                <i class="fas fa-sliders"></i>
                <span class="sana-cat-sticky__dot" x-show="hasActiveFilters" x-cloak></span>
            </button>
        </div>
    </div>
</div>
<?php endif; ?>

<main class="sana-cat-page">

    
    <section class="sana-cat-hero" id="cat-hero">
        <div class="sana-cat-hero__dots"></div>
        <div class="sana-container sana-cat-hero__inner">
            <nav class="sana-cat-hero__breadcrumb" aria-label="مسار التنقل">
                <a href="<?php echo e(route('home')); ?>">الرئيسية</a>
                <i class="fas fa-chevron-left" style="font-size:0.6rem;opacity:0.5"></i>
                <span><?php echo e($pageTitle); ?></span>
            </nav>
            <h1 class="sana-cat-hero__title">
                <?php if(!empty($savedOnly)): ?>
                    <?php echo e(__('public.saved_courses_page_title')); ?>

                <?php elseif($catalogShowLaunch): ?>
                    <?php echo e(__('public.courses_launch_badge')); ?>

                <?php else: ?>
                    استكشف <span class="hl">الدورات</span>
                <?php endif; ?>
            </h1>
            <p class="sana-cat-hero__desc"><?php echo e($pageDesc); ?></p>
            <?php if($catalogShowLaunch): ?>
            <div class="sana-cat-hero__soon sana-reveal">
                <span class="sana-cat-hero__soon-badge"><i class="fas fa-hourglass-half"></i> <?php echo e(__('public.courses_launch_badge')); ?></span>
                <p><?php echo e(__('public.courses_launch_hint')); ?></p>
                <div class="sana-cat-hero__soon-actions">
                    <?php echo $__env->make('landing.sana.partials.site-cta-buttons', ['hero' => true], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>
            </div>
            <?php else: ?>
            <div class="sana-cat-hero__stats">
                <span class="sana-cat-hero__stat"><i class="fas fa-book-open"></i> <span x-text="courses.length">0</span> دورة متاحة</span>
                <span class="sana-cat-hero__stat" x-show="!savedOnly"><i class="fas fa-star"></i> <span x-text="courses.filter(c=>c.is_featured).length">0</span> دورة مميزة</span>
            </div>

            <?php if (! (!empty($savedOnly))): ?>
            <div class="sana-cat-search sana-reveal">
                <div class="sana-cat-search__row">
                    <div class="sana-cat-search__input-wrap">
                        <i class="fas fa-search"></i>
                        <input type="search" class="sana-cat-search__input" x-model="searchQuery" placeholder="ابحث بالاسم، المادة، أو المعلّم..." aria-label="بحث الدورات">
                    </div>
                    <button type="button" class="sana-cat-search__btn" @click="scrollToCatalog()">
                        <i class="fas fa-search"></i> بحث
                    </button>
                </div>
                <div class="sana-cat-search__filters">
                    <select class="sana-cat-search__select" x-model="selectedCategoryId" aria-label="التصنيف">
                        <option value="">كل التصنيفات</option>
                        <template x-for="cat in categories" :key="cat.id">
                            <option :value="String(cat.id)" x-text="cat.name"></option>
                        </template>
                    </select>
                    <select class="sana-cat-search__select" x-model="selectedYearId" aria-label="المرحلة">
                        <option value="">كل المراحل</option>
                        <template x-for="y in years" :key="y.id">
                            <option :value="String(y.id)" x-text="y.name"></option>
                        </template>
                    </select>
                    <select class="sana-cat-search__select" x-model="selectedSubjectId" aria-label="المادة">
                        <option value="">كل المواد</option>
                        <template x-for="s in subjects" :key="s.id">
                            <option :value="String(s.id)" x-text="s.name"></option>
                        </template>
                    </select>
                </div>
            </div>
            <?php endif; ?>
            <?php endif; ?>
        </div>
    </section>

    
    <?php if($catalogShowCatalog && empty($savedOnly)): ?>
    <section class="sana-cat-categories" x-show="catalogCategories.length > 0" x-cloak>
        <div class="sana-container">
            <div class="sana-head sana-reveal" style="margin-bottom:24px">
                <h2 class="sana-head__title">تصفّح <span class="hl">التصنيفات</span></h2>
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
                        <span class="sana-cat-category__count"><span x-text="cat.count"></span> دورة</span>
                    </button>
                </template>
            </div>
        </div>
    </section>

    
    <section class="sana-cat-featured" x-show="featuredCourses.length > 0" x-cloak>
        <div class="sana-container">
            <div class="sana-head-row sana-reveal" style="margin-bottom:28px">
                <div class="sana-head">
                    <h2 class="sana-head__title">دورات <span class="hl">مميزة</span></h2>
                    <span class="sana-head__line"></span>
                </div>
            </div>
            <div class="sana-cat-featured__grid" x-show="featuredCourses.length > 0">
                <template x-for="course in featuredCourses" :key="'feat-' + course.id">
                    <?php echo $__env->make('landing.sana.partials.course-card-alpine', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </template>
            </div>
        </div>
    </section>
    <?php endif; ?>

    
    <?php if($catalogShowCatalog): ?>
    <section class="sana-cat-catalog" id="all-courses">
        <div class="sana-container">
            <div class="sana-head sana-reveal" style="margin-bottom:32px">
                <h2 class="sana-head__title">
                    <?php if(!empty($savedOnly)): ?>
                        دوراتك <span class="hl">المحفوظة</span>
                    <?php else: ?>
                        جميع <span class="hl">الدورات</span>
                    <?php endif; ?>
                </h2>
                <span class="sana-head__line"></span>
            </div>

            <div class="sana-cat-layout">
                
                <aside class="sana-cat-sidebar">
                    <h3 class="sana-cat-sidebar__title"><i class="fas fa-sliders"></i> تصفية النتائج</h3>
                    <?php echo $__env->make('landing.sana.courses.catalog-filters', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </aside>

                
                <div class="sana-cat-results">
                    <div class="sana-cat-results__toolbar">
                        <p class="sana-cat-results__count">
                            <strong x-text="filteredCourses.length">0</strong> دورة
                            <span x-show="hasActiveFilters"> — نتائج مُصفّاة</span>
                        </p>
                        <button type="button" class="sana-btn sana-btn--purple-outline sana-cat-filter-mobile-btn" style="padding:10px 16px;font-size:0.85rem;min-height:44px" @click="filterSheetOpen = true">
                            <i class="fas fa-sliders"></i> فلترة
                        </button>
                    </div>

                    <div class="sana-cat-grid" x-show="filteredCourses.length > 0">
                        <template x-for="course in filteredCourses" :key="course.id">
                            <?php echo $__env->make('landing.sana.partials.course-card-alpine', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
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
    <?php endif; ?>

    
    <?php if($catalogShowLaunch): ?>
    <section class="sana-cat-cta">
        <div class="sana-container">
            <div class="sana-cat-cta__inner">
                <div>
                    <h2><?php echo e(__('public.courses_launch_badge')); ?></h2>
                    <p><?php echo e(__('public.courses_launch_hint')); ?></p>
                </div>
                <div class="sana-cat-cta__actions">
                    <?php echo $__env->make('landing.sana.partials.site-cta-buttons', ['hero' => true], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>
            </div>
        </div>
    </section>
    <?php elseif(empty($savedOnly)): ?>
    <section class="sana-cat-cta">
        <div class="sana-container">
            <div class="sana-cat-cta__inner">
                <div>
                    <h2>لم تجد الدورة المناسبة؟</h2>
                    <p>احجز حصة مباشرة مع معلّم مختص أو استعرض باقات <?php echo e($brand); ?> لأولياء الأمور.</p>
                </div>
                <div class="sana-cat-cta__actions">
                    <a href="<?php echo e(route('register')); ?>" class="sana-btn sana-btn--yellow">احجز حصة</a>
                    <a href="<?php echo e(route('public.pricing')); ?>" class="sana-btn sana-btn--white-outline">الباقات</a>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>
</main>

<?php if($catalogShowCatalog): ?>

<div class="sana-cat-sheet-backdrop" :class="filterSheetOpen && 'is-open'" @click="filterSheetOpen = false" x-cloak></div>
<div class="sana-cat-sheet" :class="filterSheetOpen && 'is-open'" x-cloak role="dialog" aria-label="فلترة الدورات">
    <div class="sana-cat-sheet__handle"></div>
    <div class="sana-cat-sheet__head">
        <h3>تصفية الدورات</h3>
        <button type="button" class="sana-cat-sheet__close" @click="filterSheetOpen = false" aria-label="إغلاق"><i class="fas fa-times"></i></button>
    </div>
    <?php echo $__env->make('landing.sana.courses.catalog-filters', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <button type="button" class="sana-cat-sheet__apply" @click="filterSheetOpen = false">عرض النتائج (<span x-text="filteredCourses.length"></span>)</button>
</div>
<?php endif; ?>

<?php echo $__env->make('landing.sana.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

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
                    authenticated: <?php echo json_encode(auth()->check(), 15, 512) ?>,
                    loginUrl: <?php echo json_encode(route('login'), 15, 512) ?>,
                    toggleUrlTemplate: <?php echo json_encode(url('/saved-courses/__ID__/toggle'), 15, 512) ?>,
                    syncUrl: <?php echo json_encode(route('public.saved-courses.sync'), 15, 512) ?>,
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

        courseUrl(id) { return <?php echo json_encode(url('/course'), 15, 512) ?> + '/' + id; },
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
<?php echo $__env->make('landing.sana.scripts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('partials.pwa-service-worker', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\sana\resources\views/courses.blade.php ENDPATH**/ ?>