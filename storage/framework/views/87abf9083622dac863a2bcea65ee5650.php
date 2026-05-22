<?php
    $brand = config('app.name', 'Sana');
    $bc = config('brand.colors');
    $tr = fn (string $key) => str_replace(':brand', $brand, __('sana_home.'.$key));
    $pub = fn (string $key) => str_replace(':brand', $brand, __('public.'.$key));
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
    <title><?php echo e(__('public.certificates_page_title')); ?> - <?php echo e($brand); ?></title>
    <meta name="description" content="<?php echo e($pub('certificates_meta_description')); ?>">
    <meta name="theme-color" content="<?php echo e($bc['blue']); ?>">
    <link rel="canonical" href="<?php echo e(url('/certificates')); ?>">
    <meta property="og:title" content="<?php echo e(__('public.certificates_page_title')); ?> - <?php echo e($brand); ?>">
    <meta property="og:description" content="<?php echo e($pub('certificates_meta_description')); ?>">
    <meta property="og:image" content="<?php echo e(asset('images/og-image.jpg')); ?>">
    <?php echo $__env->make('partials.favicon-links', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('partials.seo-jsonld', ['jsonldType' => 'website'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config={theme:{extend:{colors:{edu:{primary:'<?php echo e($bc['blue']); ?>',purple:'<?php echo e($bc['purple']); ?>',accent:'<?php echo e($bc['yellow']); ?>'}}}}}</script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <?php echo $__env->make('landing.eduvalt.theme', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('landing.eduvalt.courses-page', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('landing.eduvalt.support-pages', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('partials.rtl-base', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</head>
<body class="antialiased bg-white">
<div id="edu-preloader" aria-hidden="true"><div class="edu-preloader-spinner"></div></div>
<div id="scroll-progress"></div>

<?php echo $__env->make('landing.eduvalt.navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<main class="pt-[76px] lg:pt-[84px]">

<section class="edu-support-hero relative overflow-hidden py-10 lg:py-14">
    <div class="absolute top-16 start-0 w-64 h-64 rounded-full bg-sky-200/40 blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-0 end-0 w-80 h-80 rounded-full bg-blue-200/30 blur-3xl pointer-events-none"></div>
    <div class="edu-container-full relative z-10">
        <div class="edu-courses-inner">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-12 items-center reveal">
                <div>
                    <nav class="edu-breadcrumb mb-4" aria-label="مسار التنقل">
                        <a href="<?php echo e(route('home')); ?>"><?php echo e($tr('nav.home')); ?></a>
                        <i class="fas fa-chevron-left text-[10px] opacity-50"></i>
                        <span class="text-slate-800 font-semibold"><?php echo e(__('public.certificates_page_title')); ?></span>
                    </nav>
                    <span class="edu-badge mb-4"><i class="fas fa-certificate"></i> <?php echo e(__('public.certificates_hero_badge')); ?></span>
                    <h1 class="edu-section-title text-slate-900">
                        <?php echo e(__('public.certificates_hero_title')); ?>

                        <?php echo $__env->make('landing.eduvalt.partials.title-mark', ['text' => __('public.certificates_hero_highlight')], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    </h1>
                    <p class="text-slate-600 leading-8 mt-3 text-sm lg:text-base max-w-xl">
                        <?php echo e($pub('certificates_hero_sub')); ?>

                    </p>
                    <div class="edu-hero-actions mt-6">
                        <a href="<?php echo e(route('public.certificates.verify')); ?>" class="edu-btn-primary">
                            <i class="fas fa-shield-halved"></i>
                            <?php echo e(__('public.certificates_verify_cta')); ?>

                        </a>
                        <a href="<?php echo e(route('public.courses')); ?>" class="edu-btn-outline">
                            <i class="fas fa-book-open"></i>
                            <?php echo e(__('public.certificates_browse_courses')); ?>

                        </a>
                    </div>
                </div>
                <div class="edu-hero-panel reveal s1">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="w-12 h-12 rounded-xl bg-white/15 flex items-center justify-center text-xl"><i class="fas fa-award"></i></span>
                        <div>
                            <p class="font-bold text-lg"><?php echo e(__('public.certificates_panel_title')); ?></p>
                            <p class="text-sm text-white/85 leading-6"><?php echo e($pub('certificates_panel_desc')); ?></p>
                        </div>
                    </div>
                    <a href="<?php echo e(route('public.certificates.verify')); ?>" class="inline-flex items-center justify-center gap-2 w-full rounded-xl font-bold bg-white text-[var(--edu-primary)] px-5 py-3 hover:bg-slate-50 transition-colors">
                        <i class="fas fa-magnifying-glass"></i>
                        <?php echo e(__('public.certificates_panel_btn')); ?>

                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-12 lg:py-16 bg-white border-t border-slate-100">
    <div class="edu-container-full">
        <div class="edu-courses-inner">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-8 max-w-4xl mx-auto">
                <article class="edu-cert-feature-card reveal">
                    <div class="edu-cert-feature-icon" style="background:linear-gradient(135deg,var(--edu-primary),var(--edu-primary-dark))">
                        <i class="fas fa-certificate"></i>
                    </div>
                    <h2 class="text-xl font-extrabold text-slate-900 text-center mb-3"><?php echo e(__('public.certificates_type_completion_title')); ?></h2>
                    <p class="text-slate-600 text-center text-sm leading-7 mb-6"><?php echo e(__('public.certificates_type_completion_desc')); ?></p>
                    <ul class="space-y-3 text-sm text-slate-700">
                        <li class="flex items-start gap-2"><i class="fas fa-check-circle text-emerald-500 mt-0.5 shrink-0"></i><?php echo e(__('public.certificates_type_completion_f1')); ?></li>
                        <li class="flex items-start gap-2"><i class="fas fa-check-circle text-emerald-500 mt-0.5 shrink-0"></i><?php echo e(__('public.certificates_type_completion_f2')); ?></li>
                        <li class="flex items-start gap-2"><i class="fas fa-check-circle text-emerald-500 mt-0.5 shrink-0"></i><?php echo e(__('public.certificates_type_completion_f3')); ?></li>
                    </ul>
                </article>
                <article class="edu-cert-feature-card reveal s1">
                    <div class="edu-cert-feature-icon" style="background:linear-gradient(135deg,var(--edu-purple),var(--edu-primary))">
                        <i class="fas fa-medal"></i>
                    </div>
                    <h2 class="text-xl font-extrabold text-slate-900 text-center mb-3"><?php echo e(__('public.certificates_type_pro_title')); ?></h2>
                    <p class="text-slate-600 text-center text-sm leading-7 mb-6"><?php echo e(__('public.certificates_type_pro_desc')); ?></p>
                    <ul class="space-y-3 text-sm text-slate-700">
                        <li class="flex items-start gap-2"><i class="fas fa-check-circle text-emerald-500 mt-0.5 shrink-0"></i><?php echo e(__('public.certificates_type_pro_f1')); ?></li>
                        <li class="flex items-start gap-2"><i class="fas fa-check-circle text-emerald-500 mt-0.5 shrink-0"></i><?php echo e(__('public.certificates_type_pro_f2')); ?></li>
                        <li class="flex items-start gap-2"><i class="fas fa-check-circle text-emerald-500 mt-0.5 shrink-0"></i><?php echo e(__('public.certificates_type_pro_f3')); ?></li>
                    </ul>
                </article>
            </div>
        </div>
    </div>
</section>

<section class="py-12 lg:py-16 bg-slate-50 border-t border-slate-100">
    <div class="edu-container-full">
        <div class="edu-courses-inner">
            <div class="text-center max-w-2xl mx-auto mb-10 reveal">
                <h2 class="edu-section-title text-slate-900"><?php echo e(__('public.certificates_how_title')); ?></h2>
                <p class="text-slate-600 mt-2 text-sm lg:text-base"><?php echo e(__('public.certificates_how_sub')); ?></p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-4xl mx-auto">
                <?php $__currentLoopData = [
                    ['01', 'user-graduate', 'certificates_step1_title', 'certificates_step1_desc'],
                    ['02', 'clipboard-check', 'certificates_step2_title', 'certificates_step2_desc'],
                    ['03', 'file-certificate', 'certificates_step3_title', 'certificates_step3_desc'],
                ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="edu-cert-step reveal <?php if($i > 0): ?> s<?php echo e($i); ?> <?php endif; ?>">
                    <span class="edu-cert-step-num"><?php echo e($step[0]); ?></span>
                    <span class="w-11 h-11 rounded-xl mx-auto mb-3 flex items-center justify-center text-lg" style="background:var(--edu-primary-light);color:var(--edu-primary)">
                        <i class="fas fa-<?php echo e($step[1]); ?>"></i>
                    </span>
                    <h3 class="font-bold text-slate-900 mb-2"><?php echo e(__('public.'.$step[2])); ?></h3>
                    <p class="text-slate-600 text-sm leading-7"><?php echo e(__('public.'.$step[3])); ?></p>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>
</section>

<section class="py-12 lg:py-16 bg-white">
    <div class="edu-container-full">
        <div class="edu-courses-inner">
            <div class="edu-card p-8 sm:p-10 text-center max-w-3xl mx-auto reveal">
                <span class="edu-badge mb-4"><i class="fas fa-rocket"></i> <?php echo e($tr('nav.get_started')); ?></span>
                <h2 class="edu-section-title text-slate-900 mb-3"><?php echo e(__('public.certificates_cta_title')); ?></h2>
                <p class="text-slate-600 leading-8 mb-8 max-w-xl mx-auto"><?php echo e($pub('certificates_cta_desc')); ?></p>
                <div class="edu-hero-actions justify-center">
                    <a href="<?php echo e(route('register')); ?>" class="edu-btn-primary">
                        <i class="fas fa-user-plus"></i>
                        <?php echo e($tr('nav.get_started')); ?>

                    </a>
                    <a href="<?php echo e(route('public.help')); ?>" class="edu-btn-outline">
                        <i class="fas fa-circle-question"></i>
                        <?php echo e(__('public.help_page_title')); ?>

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
<?php /**PATH C:\xampp\htdocs\sana\resources\views\public\certificates.blade.php ENDPATH**/ ?>