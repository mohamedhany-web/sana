<?php
    $brand = config('brand.name', config('app.name', 'Sana'));
    $bc = config('brand.colors');
    $tr = fn (string $key) => str_replace(':brand', $brand, __('sana_home.'.$key));
    $servicesSub = str_replace(':brand', $brand, __('public.services_subtitle'));
    $serviceThemes = [
        ['icon' => 'fa-layer-group', 'color' => $bc['blue'], 'bg' => $bc['blue_light']],
        ['icon' => 'fa-chalkboard-user', 'color' => $bc['purple'], 'bg' => $bc['purple_light']],
        ['icon' => 'fa-users', 'color' => $bc['blue_dark'], 'bg' => $bc['blue_light']],
        ['icon' => 'fa-handshake', 'color' => $bc['yellow_dark'], 'bg' => $bc['yellow_light']],
        ['icon' => 'fa-video', 'color' => $bc['purple_dark'], 'bg' => $bc['purple_light']],
        ['icon' => 'fa-clipboard-list', 'color' => $bc['blue'], 'bg' => $bc['blue_light']],
    ];
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
    <title><?php echo e(__('public.services_page_title')); ?> - <?php echo e($brand); ?></title>
    <meta name="description" content="<?php echo e($servicesSub); ?>">
    <meta name="theme-color" content="<?php echo e($bc['blue']); ?>">
    <link rel="canonical" href="<?php echo e(url('/services')); ?>">
    <meta property="og:title" content="<?php echo e(__('public.services_page_title')); ?> - <?php echo e($brand); ?>">
    <meta property="og:description" content="<?php echo e($servicesSub); ?>">
    <meta property="og:image" content="<?php echo e(asset('images/og-image.jpg')); ?>">
    <?php echo $__env->make('partials.favicon-links', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('partials.seo-jsonld', ['jsonldType' => 'website'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <?php echo $__env->make('landing.eduvalt.theme', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('landing.eduvalt.services-page', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('partials.rtl-base', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</head>
<body class="antialiased bg-white">
<div id="edu-preloader" aria-hidden="true"><div class="edu-preloader-spinner"></div></div>
<div id="scroll-progress"></div>

<?php echo $__env->make('landing.eduvalt.navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<main class="pt-[76px] lg:pt-[84px]">

<section class="edu-services-hero relative overflow-hidden py-10 lg:py-14">
    <div class="edu-services-hero__blob edu-services-hero__blob--1" aria-hidden="true"></div>
    <div class="edu-services-hero__blob edu-services-hero__blob--2" aria-hidden="true"></div>
    <div class="edu-services-hero__blob edu-services-hero__blob--3" aria-hidden="true"></div>
    <svg class="edu-services-hero__shape top-24 end-[12%] w-20 h-20 edu-float" viewBox="0 0 100 100" fill="currentColor" aria-hidden="true"><circle cx="20" cy="20" r="4"/><path d="M10 50 Q50 10 90 50" fill="none" stroke="currentColor" stroke-width="2"/></svg>
    <div class="edu-container relative z-10">
        <nav class="edu-breadcrumb mb-6 reveal" aria-label="مسار التنقل">
            <a href="<?php echo e(route('home')); ?>"><?php echo e($tr('nav.home')); ?></a>
            <i class="fas fa-chevron-left text-[10px] opacity-50"></i>
            <span class="text-slate-800 font-semibold"><?php echo e(__('public.services_page_title')); ?></span>
        </nav>

        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-8 reveal">
            <div>
                <span class="edu-sub-title"><?php echo e(__('public.services_page_title')); ?></span>
                <h1 class="edu-section-title text-slate-900"><?php echo e(__('public.services_heading')); ?></h1>
                <p class="text-slate-500 mt-2 max-w-xl text-sm leading-7"><?php echo e($servicesSub); ?></p>
            </div>
            <a href="<?php echo e(route('public.contact')); ?>" class="edu-btn-outline shrink-0 text-sm">
                <?php echo e(__('public.contact_us')); ?>

                <i class="fas fa-arrow-left text-xs"></i>
            </a>
        </div>

        <?php if($services->count() > 0): ?>
            <p class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-slate-50 border border-slate-200 text-xs font-bold text-slate-600 reveal">
                <i class="fas fa-layer-group text-[var(--edu-primary)]"></i>
                <?php echo e($services->count()); ?> <?php echo e(__('public.services_count_label')); ?>

            </p>
        <?php endif; ?>
    </div>
</section>

<section class="edu-services-section py-10 lg:py-12">
    <div class="edu-container relative z-10">
        <?php if($services->count() > 0): ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 reveal">
                <?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $theme = $serviceThemes[$idx % count($serviceThemes)]; ?>
                    <a href="<?php echo e(route('public.services.show', $service)); ?>"
                       class="edu-cat-card group"
                       style="--cat-accent:<?php echo e($theme['color']); ?>;--cat-bg:<?php echo e($theme['bg']); ?>">
                        <span class="edu-cat-icon">
                            <?php if($service->publicImageUrl()): ?>
                                <img src="<?php echo e($service->publicImageUrl()); ?>" alt="" loading="lazy">
                            <?php else: ?>
                                <i class="fas <?php echo e($theme['icon']); ?>"></i>
                            <?php endif; ?>
                        </span>
                        <span class="edu-cat-body">
                            <span class="edu-cat-name"><?php echo e($service->name); ?></span>
                            <span class="edu-cat-meta">
                                <span class="edu-cat-count line-clamp-1"><?php echo e(Str::limit(strip_tags($service->summary ?: $service->body), 72)); ?></span>
                                <span class="edu-cat-arrow"><i class="fas fa-arrow-left"></i></span>
                            </span>
                        </span>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <div class="flex flex-wrap items-center justify-center gap-4 mt-10 pt-8 border-t border-slate-200/80 reveal">
                <a href="<?php echo e(route('public.courses')); ?>" class="edu-btn-outline text-sm">
                    <?php echo e(__('public.browse_courses')); ?>

                    <i class="fas fa-arrow-left text-xs"></i>
                </a>
                <a href="<?php echo e(route('public.pricing')); ?>" class="text-sm font-bold text-[var(--edu-primary)] hover:underline">
                    <?php echo e($tr('platform.cta_services')); ?>

                </a>
            </div>
        <?php else: ?>
            <div class="edu-card max-w-md mx-auto p-8 text-center reveal">
                <span class="w-12 h-12 mx-auto rounded-xl flex items-center justify-center text-lg mb-4" style="background:var(--edu-primary-light);color:var(--edu-primary)">
                    <i class="fas fa-concierge-bell"></i>
                </span>
                <h2 class="font-bold text-slate-900 text-lg mb-2"><?php echo e(__('public.services_empty_title')); ?></h2>
                <p class="text-sm text-slate-500 leading-7 mb-5"><?php echo e(__('public.services_empty_desc')); ?></p>
                <a href="<?php echo e(route('home')); ?>" class="edu-btn-primary text-sm inline-flex items-center gap-2">
                    <?php echo e($tr('nav.home')); ?>

                    <i class="fas fa-arrow-left text-xs"></i>
                </a>
            </div>
        <?php endif; ?>
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
    var io = new IntersectionObserver(function (entries) {
        entries.forEach(function (e) {
            if (e.isIntersecting) { e.target.classList.add('revealed'); io.unobserve(e.target); }
        });
    }, { threshold: 0.08, rootMargin: '0px 0px -24px 0px' });
    document.querySelectorAll('.reveal').forEach(function (el) { io.observe(el); });
    var pre = document.getElementById('edu-preloader');
    if (pre) window.addEventListener('load', function () { pre.style.opacity = '0'; setTimeout(function () { pre.remove(); }, 400); });
})();
</script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\sana\resources\views\public\services\index.blade.php ENDPATH**/ ?>