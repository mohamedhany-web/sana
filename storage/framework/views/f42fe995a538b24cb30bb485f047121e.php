<?php
    $brand = config('app.name', 'Sana');
    $bc = config('brand.colors');
    $tr = fn (string $key) => str_replace(':brand', $brand, __('sana_home.'.$key));
    $defaultPhoto = 'https://images.unsplash.com/photo-1560250097-0b93528c311a?w=400&auto=format&fit=crop&q=80';
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
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
    <title><?php echo e(__('public.instructors_page_title')); ?> - <?php echo e($brand); ?></title>
    <meta name="description" content="<?php echo e(__('public.instructors_subtitle')); ?>">
    <meta name="theme-color" content="<?php echo e($bc['blue']); ?>">
    <link rel="canonical" href="<?php echo e(url('/instructors')); ?>">
    <meta property="og:title" content="<?php echo e(__('public.instructors_page_title')); ?> - <?php echo e($brand); ?>">
    <meta property="og:description" content="<?php echo e(__('public.instructors_subtitle')); ?>">
    <meta property="og:image" content="<?php echo e(asset('images/og-image.jpg')); ?>">
    <?php echo $__env->make('partials.favicon-links', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <script type="application/ld+json">
    {"@context":"https://schema.org","@type":"BreadcrumbList","itemListElement":[{"@type":"ListItem","position":1,"name":"الرئيسية","item":"<?php echo e(url('/')); ?>"},{"@type":"ListItem","position":2,"name":"<?php echo e(__('public.instructors_page_title')); ?>","item":"<?php echo e(url('/instructors')); ?>"}]}
    </script>
    <?php echo $__env->make('partials.seo-jsonld', ['jsonldType' => 'website'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <?php echo $__env->make('landing.eduvalt.theme', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('landing.eduvalt.courses-page', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('landing.eduvalt.instructors-page', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('partials.rtl-base', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="antialiased bg-white"
      x-data="{
        searchQuery: '',
        searchMap: <?php echo \Illuminate\Support\Js::from($searchMap)->toHtml() ?>,
        profileVisible(userId) {
          const q = this.searchQuery.toLowerCase().trim();
          if (!q) return true;
          return (this.searchMap[userId] || '').includes(q);
        },
        get visibleCount() {
          return Object.keys(this.searchMap).filter((id) => this.profileVisible(Number(id))).length;
        }
      }">
<div id="edu-preloader" aria-hidden="true"><div class="edu-preloader-spinner"></div></div>
<div id="scroll-progress"></div>

<?php echo $__env->make('landing.eduvalt.navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<main class="pt-[76px] lg:pt-[84px]">

<section class="edu-instructors-hero py-8 lg:py-10">
    <div class="edu-container-full">
        <div class="edu-courses-inner">
            <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6 reveal">
                <div class="max-w-2xl">
                    <nav class="edu-breadcrumb" aria-label="مسار التنقل">
                        <a href="<?php echo e(route('home')); ?>"><?php echo e($tr('nav.home')); ?></a>
                        <i class="fas fa-chevron-left text-[10px] opacity-50"></i>
                        <span class="text-slate-800 font-semibold"><?php echo e(__('public.instructors_page_title')); ?></span>
                    </nav>
                    <span class="edu-sub-title"><?php echo e($tr('instructors.badge')); ?></span>
                    <h1 class="edu-section-title text-slate-900 mt-1">
                        <?php echo e($tr('instructors.title')); ?>

                        <?php echo $__env->make('landing.eduvalt.partials.title-mark', ['text' => $tr('instructors.title_highlight')], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    </h1>
                    <p class="text-slate-600 leading-8 mt-3 text-sm lg:text-base"><?php echo e(__('public.instructors_subtitle')); ?></p>
                </div>
                <div class="flex flex-wrap gap-3 shrink-0">
                    <div class="edu-card px-5 py-4 min-w-[120px] text-center">
                        <p class="text-2xl font-black text-[var(--edu-primary)]"><?php echo e($profiles->count()); ?></p>
                        <p class="text-xs text-slate-500 mt-1"><?php echo e(__('public.stat_instructors')); ?></p>
                    </div>
                    <div class="edu-card px-5 py-4 min-w-[120px] text-center bg-[var(--edu-primary-light)] border-[var(--edu-primary)]/20">
                        <p class="text-2xl font-black text-[var(--edu-primary)]"><?php echo e($totalCourses); ?></p>
                        <p class="text-xs text-slate-600 mt-1"><?php echo e($tr('instructors.courses')); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-10 lg:py-14 bg-white">
    <div class="edu-container-full">
        <div class="edu-courses-inner">
            <?php if($profiles->isNotEmpty()): ?>
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8 reveal">
                <p class="text-sm text-slate-500 max-w-xl leading-7">
                    <?php echo e(__('public.instructors_ranking_hint')); ?>

                </p>
                <div class="relative w-full sm:max-w-md shrink-0">
                    <input type="search"
                           x-model="searchQuery"
                           class="edu-instructors-search"
                           placeholder="<?php echo e(__('public.instructors_search_placeholder')); ?>"
                           aria-label="<?php echo e(__('public.instructors_search_placeholder')); ?>">
                    <i class="fas fa-search absolute top-1/2 -translate-y-1/2 start-3 text-slate-400 text-sm pointer-events-none"></i>
                </div>
            </div>

            <div class="edu-instructors-grid">
                <?php $__currentLoopData = $profiles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $userId = (int) $p->user->id;
                    $photo = $p->photo_path ? $p->photo_url : $defaultPhoto;
                    $skills = array_slice($p->skills_list ?? [], 0, 3);
                    $skillsMore = max(0, count($p->skills_list ?? []) - 3);
                    $photoBgs = [$bc['blue_light'], $bc['purple_light'], $bc['yellow_light'], '#f1f5f9'];
                ?>
                <article class="edu-card edu-instructor-card reveal"
                         x-show="profileVisible(<?php echo e($userId); ?>)"
                         x-cloak>
                    <a href="<?php echo e(route('public.instructors.show', $p->user)); ?>" class="flex flex-col flex-1 min-h-0 group">
                        <div class="edu-instructor-photo-wrap" style="background:<?php echo e($photoBgs[$idx % 4]); ?>">
                            <img src="<?php echo e($photo); ?>" alt="<?php echo e($p->user->name); ?>" loading="lazy"
                                 onerror="this.src='<?php echo e($defaultPhoto); ?>'">
                            <?php if(!empty($p->marketing_featured_today)): ?>
                                <span class="edu-instructor-badge"><i class="fas fa-bolt text-[8px]"></i> <?php echo e(__('public.instructor_featured_badge')); ?></span>
                            <?php endif; ?>
                            <?php if(($p->courses_count ?? 0) > 0): ?>
                                <span class="edu-instructor-courses-pill">
                                    <?php echo e((int) $p->courses_count); ?> <?php echo e($tr('instructors.courses')); ?>

                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="edu-instructor-body">
                            <h3 class="font-bold text-slate-900 text-lg group-hover:text-[var(--edu-primary)] transition-colors">
                                <?php echo e($p->user->name); ?>

                            </h3>
                            <p class="text-sm text-[var(--edu-primary)] font-medium mt-1 mb-2 line-clamp-2">
                                <?php echo e($p->headline ?? __('public.instructor_fallback')); ?>

                            </p>
                            <?php if(count($skills) > 0): ?>
                                <div class="edu-instructor-skills">
                                    <?php $__currentLoopData = $skills; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $skill): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <span class="edu-instructor-skill"><?php echo e($skill); ?></span>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php if($skillsMore > 0): ?>
                                        <span class="edu-instructor-skill" style="background:var(--edu-purple-light);color:var(--edu-purple-dark)">+<?php echo e($skillsMore); ?></span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                            <?php if($p->bio): ?>
                                <p class="text-xs text-slate-500 leading-relaxed line-clamp-2 mb-3"><?php echo e($p->bio); ?></p>
                            <?php endif; ?>
                            <span class="inline-flex items-center justify-center gap-2 text-sm font-bold text-[var(--edu-primary)] mt-auto pt-2">
                                <?php echo e(__('public.view_instructor_profile')); ?>

                                <i class="fas fa-arrow-left text-xs"></i>
                            </span>
                        </div>
                    </a>

                </article>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <div class="edu-card p-12 text-center mt-6" x-show="searchQuery.trim() && visibleCount === 0" x-cloak>
                <div class="w-14 h-14 mx-auto rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center text-xl mb-4">
                    <i class="fas fa-magnifying-glass"></i>
                </div>
                <h3 class="font-bold text-slate-900 mb-2"><?php echo e(__('public.no_results')); ?></h3>
                <p class="text-sm text-slate-500"><?php echo e(__('public.no_results_hint')); ?></p>
            </div>
            <?php else: ?>
            <div class="edu-card p-12 text-center reveal max-w-lg mx-auto">
                <div class="w-16 h-16 mx-auto rounded-2xl bg-[var(--edu-primary-light)] text-[var(--edu-primary)] flex items-center justify-center text-2xl mb-4">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-2"><?php echo e(__('public.no_instructors')); ?></h3>
                <p class="text-slate-500 text-sm mb-6"><?php echo e(__('public.instructors_empty_hint')); ?></p>
                <a href="<?php echo e(route('home')); ?>" class="edu-btn-primary text-sm">
                    <i class="fas fa-home"></i>
                    <?php echo e($tr('nav.home')); ?>

                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="py-12 bg-[var(--edu-bg)]">
    <div class="edu-container-full">
        <div class="edu-courses-inner reveal">
            <div class="edu-cta-wrap px-8 py-10 lg:py-12 text-center text-white">
                <h2 class="text-2xl sm:text-3xl font-bold mb-3"><?php echo e(__('public.instructors_cta_title')); ?></h2>
                <p class="text-white/90 text-sm sm:text-base max-w-xl mx-auto mb-8 leading-7">
                    <?php echo e(__('public.instructors_cta_subtitle')); ?>

                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="<?php echo e(route('register')); ?>" class="edu-btn-white !text-[var(--edu-primary)] hover:!text-[var(--edu-primary-dark)]">
                        <?php echo e(__('public.instructors_cta_register')); ?>

                        <i class="fas fa-arrow-left text-sm"></i>
                    </a>
                    <a href="<?php echo e(route('public.courses')); ?>" class="edu-btn-ghost-light">
                        <?php echo e($tr('hero.cta_courses')); ?>

                        <i class="fas fa-arrow-left text-sm"></i>
                    </a>
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
<?php /**PATH C:\xampp\htdocs\sana\resources\views/instructors/index.blade.php ENDPATH**/ ?>