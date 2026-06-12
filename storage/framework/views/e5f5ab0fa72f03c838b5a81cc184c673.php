<?php
    $brand = config('brand.name', config('app.name', 'Sana'));
    $bc = config('brand.colors');
    $tr = fn (string $key) => str_replace(':brand', $brand, __('sana_home.'.$key));
    $hasImage = (bool) $siteService->publicImageUrl();
    $bodyParagraphs = collect(preg_split('/\r\n|\r|\n/', trim((string) $siteService->body)))->filter();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
    <title><?php echo e($siteService->name); ?> - <?php echo e(__('public.services_page_title')); ?> | <?php echo e($brand); ?></title>
    <meta name="description" content="<?php echo e(Str::limit(strip_tags($siteService->summary ?: $siteService->body), 160)); ?>">
    <meta name="theme-color" content="<?php echo e($bc['blue']); ?>">
    <link rel="canonical" href="<?php echo e(route('public.services.show', $siteService)); ?>">
    <meta property="og:title" content="<?php echo e($siteService->name); ?> - <?php echo e($brand); ?>">
    <meta property="og:description" content="<?php echo e(Str::limit(strip_tags($siteService->summary ?: $siteService->body), 160)); ?>">
    <?php if($hasImage): ?>
        <meta property="og:image" content="<?php echo e($siteService->publicImageUrl()); ?>">
    <?php endif; ?>
    <?php echo $__env->make('partials.favicon-links', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <?php echo $__env->make('landing.eduvalt.theme', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('landing.eduvalt.courses-page', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('landing.eduvalt.services-page', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('partials.rtl-base', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</head>
<body class="antialiased bg-white">
<div id="edu-preloader" aria-hidden="true"><div class="edu-preloader-spinner"></div></div>
<div id="scroll-progress"></div>

<?php echo $__env->make('landing.eduvalt.navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<main class="pt-[76px] lg:pt-[84px]">


<section class="edu-service-show-hero py-8 lg:py-12">
    <div class="edu-services-hero__blob edu-services-hero__blob--1" aria-hidden="true"></div>
    <div class="edu-services-hero__blob edu-services-hero__blob--2" aria-hidden="true"></div>
    <div class="edu-services-hero__blob edu-services-hero__blob--3" aria-hidden="true"></div>

    <div class="edu-container-full relative z-10">
        <div class="edu-courses-inner">
            <nav class="edu-breadcrumb mb-6 reveal" aria-label="مسار التنقل">
                <a href="<?php echo e(route('home')); ?>"><?php echo e($tr('nav.home')); ?></a>
                <i class="fas fa-chevron-left text-[10px] opacity-50"></i>
                <a href="<?php echo e(route('public.services.index')); ?>"><?php echo e(__('public.services_page_title')); ?></a>
                <i class="fas fa-chevron-left text-[10px] opacity-50"></i>
                <span class="text-slate-800 font-semibold"><?php echo e(Str::limit($siteService->name, 56)); ?></span>
            </nav>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 items-center">
                <div class="reveal order-2 lg:order-1">
                    <span class="edu-badge mb-4"><i class="fas fa-concierge-bell"></i> <?php echo e(__('public.services_page_title')); ?></span>
                    <h1 class="text-3xl sm:text-4xl lg:text-[2.5rem] font-extrabold text-slate-900 leading-tight mb-4">
                        <?php echo e($siteService->name); ?>

                    </h1>
                    <?php if($siteService->summary): ?>
                        <p class="text-slate-600 text-base sm:text-lg leading-8 mb-6 max-w-xl"><?php echo e($siteService->summary); ?></p>
                    <?php endif; ?>

                    <div class="flex flex-wrap gap-2 mb-6">
                        <span class="edu-service-show-chip">
                            <i class="fas fa-check-circle text-[var(--edu-primary)]"></i>
                            <?php echo e($brand); ?>

                        </span>
                        <span class="edu-service-show-chip">
                            <i class="fas fa-headset text-[var(--edu-purple)]"></i>
                            <?php echo e(__('public.services_support_hint')); ?>

                        </span>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <a href="<?php echo e(route('public.contact')); ?>" class="edu-btn-primary">
                            <?php echo e(__('public.contact_us')); ?>

                            <i class="fas fa-arrow-left text-sm"></i>
                        </a>
                        <a href="<?php echo e(route('public.services.index')); ?>" class="edu-btn-outline">
                            <i class="fas fa-arrow-right text-sm"></i>
                            <?php echo e(__('public.services_back_to_list')); ?>

                        </a>
                    </div>
                </div>

                <div class="reveal order-1 lg:order-2">
                    <div class="edu-service-show-media">
                        <?php if($hasImage): ?>
                            <img src="<?php echo e($siteService->publicImageUrl()); ?>" alt="<?php echo e($siteService->name); ?>" loading="eager">
                        <?php else: ?>
                            <div class="edu-service-show-media__placeholder">
                                <i class="fas fa-layer-group"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<section class="edu-service-show-body py-10 lg:py-14">
    <div class="edu-container-full">
        <div class="edu-courses-inner">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10 items-start">
                <article class="lg:col-span-8 reveal">
                    <div class="edu-service-show-article">
                        <h2 class="text-lg font-bold text-slate-900 mb-5 flex items-center gap-2">
                            <span class="w-9 h-9 rounded-lg flex items-center justify-center text-sm" style="background:var(--edu-primary-light);color:var(--edu-primary)">
                                <i class="fas fa-align-right"></i>
                            </span>
                            <?php echo e(__('public.services_read_more')); ?>

                        </h2>
                        <div class="edu-service-show-prose">
                            <?php if($bodyParagraphs->isNotEmpty()): ?>
                                <?php $__currentLoopData = $bodyParagraphs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $para): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <p><?php echo e($para); ?></p>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php else: ?>
                                <p class="text-slate-400"><?php echo e(__('public.services_empty_desc')); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </article>

                <aside class="lg:col-span-4 reveal">
                    <div class="edu-service-show-sidebar space-y-4">
                        <div class="edu-service-show-cta-card">
                            <div class="edu-service-show-cta-head">
                                <p class="text-xs font-bold uppercase tracking-wide opacity-90 mb-1"><?php echo e(__('public.services_cta_badge')); ?></p>
                                <p class="text-lg font-extrabold"><?php echo e(__('public.services_cta_title')); ?></p>
                            </div>
                            <div class="edu-service-show-cta-body">
                                <p class="text-sm text-slate-600 leading-7 mb-4"><?php echo e(__('public.services_cta_text')); ?></p>
                                <a href="<?php echo e(route('public.contact')); ?>" class="edu-btn-primary w-full justify-center text-sm mb-3">
                                    <?php echo e(__('public.contact_us')); ?>

                                    <i class="fas fa-arrow-left text-xs"></i>
                                </a>
                                <a href="<?php echo e(route('public.pricing')); ?>" class="edu-btn-outline w-full justify-center text-sm">
                                    <?php echo e($tr('platform.cta_services')); ?>

                                    <i class="fas fa-arrow-left text-xs"></i>
                                </a>
                            </div>
                        </div>

                        <a href="<?php echo e(route('public.courses')); ?>" class="edu-card flex items-center gap-3 p-4 hover:border-[var(--edu-primary)] transition-colors group">
                            <span class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0" style="background:var(--edu-accent-light);color:var(--edu-accent-dark)">
                                <i class="fas fa-graduation-cap"></i>
                            </span>
                            <span class="flex-1 min-w-0">
                                <span class="block font-bold text-slate-900 text-sm group-hover:text-[var(--edu-primary)]"><?php echo e(__('public.browse_courses')); ?></span>
                                <span class="block text-xs text-slate-500 mt-0.5"><?php echo e($tr('courses.subtitle')); ?></span>
                            </span>
                            <i class="fas fa-arrow-left text-[var(--edu-primary)] text-sm opacity-60 group-hover:opacity-100"></i>
                        </a>
                    </div>
                </aside>
            </div>
        </div>
    </div>
</section>

<?php if($others->count() > 0): ?>
<section class="py-10 lg:py-14 bg-white border-t border-slate-100">
    <div class="edu-container-full">
        <div class="edu-courses-inner">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-3 mb-6 reveal">
                <div>
                    <span class="edu-sub-title"><?php echo e(__('public.services_page_title')); ?></span>
                    <h2 class="edu-section-title text-slate-900 text-xl"><?php echo e(__('public.services_more_title')); ?></h2>
                </div>
                <a href="<?php echo e(route('public.services.index')); ?>" class="edu-btn-outline text-sm shrink-0">
                    <?php echo e(__('public.services_back_to_list')); ?>

                    <i class="fas fa-arrow-left text-xs"></i>
                </a>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 reveal">
                <?php $__currentLoopData = $others; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $o): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(route('public.services.show', $o)); ?>" class="edu-service-related-card group">
                        <span class="edu-service-related-thumb">
                            <?php if($o->publicImageUrl()): ?>
                                <img src="<?php echo e($o->publicImageUrl()); ?>" alt="" loading="lazy">
                            <?php else: ?>
                                <i class="fas fa-layer-group"></i>
                            <?php endif; ?>
                        </span>
                        <span class="flex-1 min-w-0">
                            <span class="block font-bold text-slate-900 text-sm mb-1 group-hover:text-[var(--edu-primary)] transition-colors line-clamp-2"><?php echo e($o->name); ?></span>
                            <span class="block text-xs text-slate-500 line-clamp-2 leading-6"><?php echo e(Str::limit(strip_tags($o->summary ?: $o->body), 64)); ?></span>
                        </span>
                        <i class="fas fa-arrow-left text-[var(--edu-primary)] text-sm shrink-0 opacity-50 group-hover:opacity-100"></i>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

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
    var io = new IntersectionObserver(function (entries) {
        entries.forEach(function (e) {
            if (e.isIntersecting) { e.target.classList.add('revealed'); io.unobserve(e.target); }
        });
    }, { threshold: 0.06, rootMargin: '0px 0px -32px 0px' });
    document.querySelectorAll('.reveal').forEach(function (el) { io.observe(el); });
    var pre = document.getElementById('edu-preloader');
    if (pre) window.addEventListener('load', function () { pre.style.opacity = '0'; setTimeout(function () { pre.remove(); }, 400); });
})();
</script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\sana\resources\views\public\services\show.blade.php ENDPATH**/ ?>