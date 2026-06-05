<?php
    $brand = config('app.name', 'Sana');
    $bc = config('brand.colors');
    $tr = fn (string $key) => str_replace(':brand', $brand, __('sana_home.'.$key));
    $fmt = fn (int $n) => number_format($n, 0, '.', ',');
    $photos = [
        'instructor' => 'https://images.unsplash.com/photo-1560250097-0b93528c311a?w=600&auto=format&fit=crop&q=80',
        'student' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=500&auto=format&fit=crop&q=80',
    ];
    $aboutFeatures = [
        ['icon' => 'fa-chalkboard-user', 'color' => $bc['blue'], 'bg' => $bc['blue_light'], 'value' => $fmt((int) ($stats['instructors'] ?? 0)).'+', 'label' => $tr('about.f1')],
        ['icon' => 'fa-calendar-check', 'color' => $bc['purple'], 'bg' => $bc['purple_light'], 'value' => $fmt((int) ($stats['courses'] ?? 0)).'+', 'label' => $tr('about.f3')],
        ['icon' => 'fa-user-graduate', 'color' => $bc['yellow_dark'], 'bg' => $bc['yellow_light'], 'value' => $fmt((int) ($stats['students'] ?? 0)).'+', 'label' => $tr('instructors.students')],
        ['icon' => 'fa-chart-line', 'color' => $bc['blue_dark'], 'bg' => $bc['blue_light'], 'value' => '24/7', 'label' => $tr('about.f4')],
    ];
    $howSteps = [
        ['icon' => 'fa-magnifying-glass', 'num' => '01'],
        ['icon' => 'fa-calendar-plus', 'num' => '02'],
        ['icon' => 'fa-video', 'num' => '03'],
        ['icon' => 'fa-file-lines', 'num' => '04'],
    ];
    $whyCards = [
        ['icon' => 'fa-video', 'style' => 'background:var(--edu-primary-light);color:var(--edu-primary)', 'title' => __('public.why_1_title'), 'desc' => __('public.why_1_desc')],
        ['icon' => 'fa-chalkboard-user', 'style' => 'background:var(--edu-purple-light);color:var(--edu-purple)', 'title' => __('public.why_2_title'), 'desc' => __('public.why_2_desc')],
        ['icon' => 'fa-box-open', 'style' => 'background:var(--edu-accent-light);color:var(--edu-accent-dark)', 'title' => __('public.why_3_title'), 'desc' => __('public.why_3_desc')],
        ['icon' => 'fa-layer-group', 'style' => 'background:var(--edu-primary-light);color:var(--edu-primary-dark)', 'title' => __('public.why_4_title'), 'desc' => __('public.why_4_desc')],
    ];
    $values = [
        ['num' => '1', 'bg' => $bc['blue'], 'title' => __('public.value_1_title'), 'desc' => __('public.value_1_desc')],
        ['num' => '2', 'bg' => $bc['purple'], 'title' => __('public.value_2_title'), 'desc' => __('public.value_2_desc')],
        ['num' => '3', 'bg' => $bc['yellow_dark'], 'title' => __('public.value_3_title'), 'desc' => __('public.value_3_desc')],
    ];
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
    <title><?php echo e(__('public.about_page_title')); ?> - <?php echo e($brand); ?></title>
    <meta name="description" content="<?php echo e(__('public.about_hero_sub')); ?>">
    <meta name="theme-color" content="<?php echo e($bc['blue']); ?>">
    <link rel="canonical" href="<?php echo e(url('/about')); ?>">
    <meta property="og:title" content="<?php echo e(__('public.about_page_title')); ?> - <?php echo e($brand); ?>">
    <meta property="og:description" content="<?php echo e(__('public.about_hero_sub')); ?>">
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
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="antialiased bg-white">
<div id="edu-preloader" aria-hidden="true"><div class="edu-preloader-spinner"></div></div>
<div id="scroll-progress"></div>

<?php echo $__env->make('landing.eduvalt.navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<main class="pt-[76px] lg:pt-[84px]">


<section class="edu-courses-page-hero py-8 lg:py-12">
    <div class="edu-container-full">
        <div class="edu-courses-inner text-center reveal">
            <nav class="edu-breadcrumb justify-center mb-4" aria-label="مسار التنقل">
                <a href="<?php echo e(route('home')); ?>"><?php echo e($tr('nav.home')); ?></a>
                <i class="fas fa-chevron-left text-[10px] opacity-50"></i>
                <span class="text-slate-800 font-semibold"><?php echo e(__('public.about_page_title')); ?></span>
            </nav>
            <span class="edu-badge mb-4"><i class="fas fa-heart"></i> <?php echo e($tr('about.badge')); ?></span>
            <h1 class="edu-section-title text-slate-900 max-w-3xl mx-auto">
                <?php echo e($tr('about.title')); ?>

                <?php echo $__env->make('landing.eduvalt.partials.title-mark', ['text' => $tr('about.title_highlight')], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </h1>
            <p class="text-slate-600 leading-8 mt-4 max-w-2xl mx-auto text-sm lg:text-base">
                <?php echo e(__('public.about_hero_sub')); ?>

            </p>
            <p class="text-slate-500 text-sm mt-3 max-w-xl mx-auto"><?php echo e(__('public.about_intro')); ?></p>
        </div>
    </div>
</section>


<section class="py-12 lg:py-16 bg-white" id="about-story">
    <div class="edu-container">
        <div class="flex flex-col lg:flex-row gap-12 items-center">
            <div class="w-full lg:w-1/2 reveal">
                <span class="edu-sub-title"><?php echo e(__('public.about_heading')); ?></span>
                <h2 class="edu-section-title text-slate-900 mb-4"><?php echo e($brand); ?></h2>
                <p class="text-slate-600 leading-8 mb-4 text-base">
                    <?php echo __('public.about_para1', ['brand' => '<strong class="text-[var(--edu-primary)]">'.e($brand).'</strong>']); ?>

                </p>
                <p class="text-slate-600 leading-8 mb-8 text-base"><?php echo e(__('public.about_para2')); ?></p>
                <div class="grid grid-cols-2 gap-3 mb-8">
                    <?php $__currentLoopData = $aboutFeatures; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $af): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="edu-card !p-4 flex items-center gap-3">
                            <span class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0" style="background:<?php echo e($af['bg']); ?>;color:<?php echo e($af['color']); ?>">
                                <i class="fas <?php echo e($af['icon']); ?>"></i>
                            </span>
                            <div class="text-start min-w-0">
                                <p class="font-extrabold text-slate-900 text-sm"><?php echo e($af['value']); ?></p>
                                <p class="text-xs text-slate-500"><?php echo e($af['label']); ?></p>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="<?php echo e(route('register')); ?>" class="edu-btn-primary"><?php echo e($tr('hero.cta_book')); ?> <i class="fas fa-arrow-left text-sm"></i></a>
                    <a href="<?php echo e(route('public.courses')); ?>" class="edu-btn-outline"><?php echo e($tr('hero.cta_courses')); ?></a>
                </div>
            </div>
            <div class="relative w-full lg:w-1/2 reveal">
                <div class="relative max-w-md mx-auto">
                    <img src="<?php echo e($photos['instructor']); ?>" alt="" class="w-[85%] rounded-[1.5rem] shadow-xl object-cover aspect-[4/5] mx-auto" loading="lazy">
                    <img src="<?php echo e($photos['student']); ?>" alt="" class="absolute -bottom-6 -start-6 w-[55%] rounded-2xl shadow-2xl border-4 border-white object-cover aspect-square" loading="lazy">
                    <div class="edu-about-exp">
                        <p class="year"><?php echo e($tr('about.years')); ?></p>
                        <p><?php echo e($tr('about.years_label')); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<section class="py-12 lg:py-16 bg-[#F8FAFC] border-y border-slate-100" id="how">
    <div class="edu-container">
        <div class="text-center mb-10 reveal">
            <span class="edu-sub-title"><?php echo e($tr('how.badge')); ?></span>
            <h2 class="edu-section-title text-slate-900">
                <?php echo e($tr('how.title')); ?>

                <?php echo $__env->make('landing.eduvalt.partials.title-mark', ['text' => $tr('how.title_highlight')], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </h2>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5 reveal">
            <?php $__currentLoopData = $howSteps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $si => $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <article class="relative edu-card p-6 pt-8">
                    <span class="absolute -top-3 start-6 px-3 py-1 rounded-full bg-[var(--edu-primary)] text-white text-xs font-black"><?php echo e($step['num']); ?></span>
                    <span class="w-12 h-12 rounded-xl bg-[var(--edu-primary-light)] text-[var(--edu-primary)] flex items-center justify-center text-lg mb-4">
                        <i class="fas <?php echo e($step['icon']); ?>"></i>
                    </span>
                    <h3 class="font-bold text-slate-900 mb-2"><?php echo e($tr('how.s'.($si + 1).'_title')); ?></h3>
                    <p class="text-sm text-slate-600 leading-7"><?php echo e($tr('how.s'.($si + 1).'_desc')); ?></p>
                </article>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>


<section class="py-12 lg:py-16 bg-white">
    <div class="edu-container">
        <div class="grid lg:grid-cols-2 gap-6">
            <article class="edu-card !p-6 sm:!p-8 reveal overflow-hidden relative">
                <div class="absolute top-0 inset-x-0 h-1" style="background:linear-gradient(90deg,<?php echo e($bc['blue']); ?>,<?php echo e($bc['purple']); ?>)"></div>
                <div class="flex gap-4 items-start">
                    <span class="w-14 h-14 rounded-2xl text-white flex items-center justify-center shrink-0 text-xl" style="background:linear-gradient(135deg,<?php echo e($bc['blue']); ?>,<?php echo e($bc['purple']); ?>)">
                        <i class="fas fa-eye"></i>
                    </span>
                    <div>
                        <h3 class="text-xl font-bold text-slate-900 mb-3"><?php echo e(__('public.our_vision')); ?></h3>
                        <p class="text-slate-600 leading-relaxed"><?php echo e(str_replace(':brand', $brand, __('public.vision_text'))); ?></p>
                    </div>
                </div>
            </article>
            <article class="edu-card !p-6 sm:!p-8 reveal overflow-hidden relative">
                <div class="absolute top-0 inset-x-0 h-1" style="background:linear-gradient(90deg,<?php echo e($bc['purple']); ?>,<?php echo e($bc['yellow']); ?>)"></div>
                <div class="flex gap-4 items-start">
                    <span class="w-14 h-14 rounded-2xl text-white flex items-center justify-center shrink-0 text-xl" style="background:linear-gradient(135deg,<?php echo e($bc['purple']); ?>,<?php echo e($bc['yellow_dark']); ?>)">
                        <i class="fas fa-bullseye"></i>
                    </span>
                    <div>
                        <h3 class="text-xl font-bold text-slate-900 mb-3"><?php echo e(__('public.our_mission')); ?></h3>
                        <p class="text-slate-600 leading-relaxed mb-4"><?php echo e(__('public.mission_intro')); ?></p>
                        <ul class="space-y-2 text-sm text-slate-600">
                            <?php $__currentLoopData = ['mission_1', 'mission_2', 'mission_3']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li class="flex items-start gap-2">
                                    <i class="fas fa-check-circle text-[var(--edu-primary)] mt-0.5 shrink-0"></i>
                                    <span><?php echo e(__('public.'.$mk)); ?></span>
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                </div>
            </article>
        </div>
    </div>
</section>


<section class="py-12 lg:py-16 bg-[var(--edu-bg)]">
    <div class="edu-container">
        <div class="text-center mb-10 reveal">
            <h2 class="edu-section-title text-slate-900"><?php echo e(str_replace(':brand', $brand, __('public.why_platform'))); ?></h2>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <?php $__currentLoopData = $whyCards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $wc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <article class="edu-card !p-6 reveal flex flex-col">
                    <span class="w-12 h-12 rounded-xl flex items-center justify-center text-lg mb-4" style="<?php echo e($wc['style']); ?>">
                        <i class="fas <?php echo e($wc['icon']); ?>"></i>
                    </span>
                    <h4 class="font-bold text-slate-900 mb-2"><?php echo e($wc['title']); ?></h4>
                    <p class="text-sm text-slate-600 leading-7 flex-1"><?php echo e($wc['desc']); ?></p>
                </article>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>


<section class="py-12 lg:py-16 bg-white">
    <div class="edu-container">
        <div class="text-center mb-10 reveal">
            <span class="edu-sub-title"><?php echo e(__('public.our_values')); ?></span>
            <h2 class="edu-section-title text-slate-900"><?php echo e(__('public.our_values')); ?></h2>
        </div>
        <div class="grid md:grid-cols-3 gap-6">
            <?php $__currentLoopData = $values; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <article class="edu-card !p-8 text-center reveal">
                    <span class="inline-flex w-14 h-14 rounded-2xl font-black text-xl items-center justify-center mb-5 text-white" style="background:<?php echo e($v['bg']); ?>"><?php echo e($v['num']); ?></span>
                    <h4 class="text-lg font-bold text-slate-900 mb-2"><?php echo e($v['title']); ?></h4>
                    <p class="text-sm text-slate-600 leading-relaxed"><?php echo e($v['desc']); ?></p>
                </article>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>


<section class="py-14 lg:py-16">
    <div class="edu-container reveal">
        <div class="edu-cta-wrap px-8 py-10 lg:py-12 text-white text-center">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-8">
                <div>
                    <p class="text-3xl lg:text-4xl font-black counter tabular-nums" data-target="<?php echo e((int) ($stats['courses'] ?? 0)); ?>">0</p>
                    <p class="text-white/85 text-sm mt-2 font-semibold"><?php echo e(__('public.stat_courses')); ?></p>
                </div>
                <div>
                    <p class="text-3xl lg:text-4xl font-black counter tabular-nums" data-target="<?php echo e((int) ($stats['students'] ?? 0)); ?>">0</p>
                    <p class="text-white/85 text-sm mt-2 font-semibold"><?php echo e(__('public.stat_students')); ?></p>
                </div>
                <div>
                    <p class="text-3xl lg:text-4xl font-black counter tabular-nums" data-target="<?php echo e((int) ($stats['instructors'] ?? 0)); ?>">0</p>
                    <p class="text-white/85 text-sm mt-2 font-semibold"><?php echo e(__('public.stat_instructors')); ?></p>
                </div>
                <div>
                    <p class="text-3xl lg:text-4xl font-black">100%</p>
                    <p class="text-white/85 text-sm mt-2 font-semibold"><?php echo e(__('public.stat_quality')); ?></p>
                </div>
            </div>
        </div>
    </div>
</section>


<section class="py-12 lg:py-14 bg-white">
    <div class="edu-container reveal">
        <div class="edu-cta-wrap px-8 py-10 lg:py-12 text-center text-white">
            <h2 class="text-2xl sm:text-3xl font-bold mb-3"><?php echo e(__('public.cta_about_title')); ?></h2>
            <p class="text-white/90 text-sm sm:text-base max-w-xl mx-auto mb-8 leading-7"><?php echo e(__('public.cta_about_desc')); ?></p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="<?php echo e(route('register')); ?>" class="edu-btn-white !text-[var(--edu-primary)] hover:!text-[var(--edu-primary-dark)]">
                    <?php echo e(__('public.register_free_now')); ?>

                    <i class="fas fa-arrow-left text-sm"></i>
                </a>
                <a href="<?php echo e(route('public.instructors.index')); ?>" class="edu-btn-ghost-light">
                    <?php echo e($tr('instructors.view_all')); ?>

                    <i class="fas fa-arrow-left text-sm"></i>
                </a>
                <a href="<?php echo e(route('public.courses')); ?>" class="edu-btn-ghost-light">
                    <?php echo e(__('public.browse_all_courses_btn')); ?>

                    <i class="fas fa-arrow-left text-sm"></i>
                </a>
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

    function animateCounter(el) {
        var target = parseInt(el.getAttribute('data-target'), 10);
        if (isNaN(target)) return;
        var duration = 2000;
        var start = performance.now();
        function tick(now) {
            var p = Math.min((now - start) / duration, 1);
            var eased = 1 - Math.pow(1 - p, 3);
            var val = Math.floor(eased * target);
            el.textContent = val.toLocaleString('ar-EG') + (target > 0 ? '+' : '');
            if (p < 1) requestAnimationFrame(tick);
            else el.textContent = target.toLocaleString('ar-EG') + '+';
        }
        requestAnimationFrame(tick);
    }
    var counters = document.querySelectorAll('.counter[data-target]');
    if ('IntersectionObserver' in window) {
        var co = new IntersectionObserver(function (entries) {
            entries.forEach(function (e) {
                if (e.isIntersecting) { animateCounter(e.target); co.unobserve(e.target); }
            });
        }, { threshold: 0.25 });
        counters.forEach(function (c) { co.observe(c); });
    }

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
<?php /**PATH C:\xampp\htdocs\sana\resources\views/public/about.blade.php ENDPATH**/ ?>