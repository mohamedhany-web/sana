<?php
    $brand = config('app.name', 'Sana');
    $bc = config('brand.colors');
    $tr = fn (string $key) => str_replace(':brand', $brand, __('sana_home.'.$key));
    $topicLinks = [
        route('public.faq') . '#faq-main',
        route('public.faq') . '#faq-main',
        route('public.faq') . '#faq-main',
        route('public.certificates'),
        route('public.contact'),
    ];
    $topicIcons = ['user-plus', 'credit-card', 'route', 'certificate', 'headset'];
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
    <title><?php echo e(__('public.help_page_title')); ?> - <?php echo e($brand); ?></title>
    <meta name="description" content="<?php echo e(__('public.help_meta_description', ['brand' => $brand])); ?>">
    <meta name="theme-color" content="<?php echo e($bc['blue']); ?>">
    <link rel="canonical" href="<?php echo e(url('/help')); ?>">
    <meta property="og:title" content="<?php echo e(__('public.help_page_title')); ?> - <?php echo e($brand); ?>">
    <meta property="og:description" content="<?php echo e(__('public.help_meta_description', ['brand' => $brand])); ?>">
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
    <style>[x-cloak]{display:none!important}</style>
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
        <div class="edu-courses-inner text-center reveal">
            <nav class="edu-breadcrumb justify-center mb-4" aria-label="???? ??????">
                <a href="<?php echo e(route('home')); ?>"><?php echo e($tr('nav.home')); ?></a>
                <i class="fas fa-chevron-left text-[10px] opacity-50"></i>
                <span class="text-slate-800 font-semibold"><?php echo e(__('public.help_page_title')); ?></span>
            </nav>
            <span class="edu-badge mb-4"><i class="fas fa-life-ring"></i> <?php echo e(__('public.help_hero_badge')); ?></span>
            <h1 class="edu-section-title text-slate-900 max-w-3xl mx-auto">
                <?php echo e(__('public.help_page_title')); ?>

                <?php echo $__env->make('landing.eduvalt.partials.title-mark', ['text' => $brand], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </h1>
            <p class="text-slate-600 leading-8 mt-3 text-sm lg:text-base max-w-2xl mx-auto">
                <?php echo e(__('public.help_hero_sub')); ?>

            </p>
            <div class="edu-hero-actions mt-6 justify-center">
                <a href="<?php echo e(route('public.faq')); ?>" class="edu-btn-primary">
                    <i class="fas fa-circle-question"></i>
                    <?php echo e(__('public.faq_page_title')); ?>

                </a>
                <a href="<?php echo e(route('public.contact')); ?>" class="edu-btn-outline">
                    <i class="fas fa-envelope"></i>
                    <?php echo e(__('public.contact_page_title')); ?>

                </a>
            </div>
        </div>
    </div>
</section>

<section class="py-10 lg:py-14 bg-white">
    <div class="edu-container-full">
        <div class="edu-courses-inner">
            <div class="flex items-center gap-3 mb-6 reveal">
                <span class="w-10 h-1 rounded-full shrink-0" style="background:linear-gradient(90deg,var(--edu-primary),var(--edu-accent))"></span>
                <h2 class="text-xl font-bold text-slate-900"><?php echo e(__('public.help_start_title')); ?></h2>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 reveal s1">
                <a href="<?php echo e(route('public.faq')); ?>" class="edu-card edu-help-hub-card">
                    <span class="edu-help-hub-icon" style="background:linear-gradient(135deg,var(--edu-primary),var(--edu-purple))"><i class="fas fa-circle-question"></i></span>
                    <h3 class="text-lg font-bold text-slate-900 mb-2"><?php echo e(__('public.help_card_faq_title')); ?></h3>
                    <p class="text-slate-600 text-sm leading-relaxed"><?php echo e(__('public.help_card_faq_desc')); ?></p>
                </a>
                <a href="<?php echo e(route('public.contact')); ?>" class="edu-card edu-help-hub-card">
                    <span class="edu-help-hub-icon" style="background:linear-gradient(135deg,var(--edu-accent),var(--edu-accent-dark))"><i class="fas fa-envelope"></i></span>
                    <h3 class="text-lg font-bold text-slate-900 mb-2"><?php echo e(__('public.help_card_contact_title')); ?></h3>
                    <p class="text-slate-600 text-sm leading-relaxed"><?php echo e(__('public.help_card_contact_desc')); ?></p>
                </a>
                <a href="<?php echo e(route('public.courses')); ?>" class="edu-card edu-help-hub-card">
                    <span class="edu-help-hub-icon" style="background:linear-gradient(135deg,var(--edu-primary-dark),var(--edu-primary))"><i class="fas fa-graduation-cap"></i></span>
                    <h3 class="text-lg font-bold text-slate-900 mb-2"><?php echo e(__('public.help_card_courses_title')); ?></h3>
                    <p class="text-slate-600 text-sm leading-relaxed"><?php echo e(__('public.help_card_courses_desc')); ?></p>
                </a>
            </div>

            <div class="flex items-center gap-3 mt-12 mb-6 reveal">
                <span class="w-10 h-1 rounded-full shrink-0" style="background:linear-gradient(90deg,var(--edu-accent),var(--edu-primary))"></span>
                <h2 class="text-xl font-bold text-slate-900"><?php echo e(__('public.help_topics_title')); ?></h2>
            </div>
            <div class="edu-card !p-0 overflow-hidden reveal s1">
                <?php $__currentLoopData = range(1, 5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e($topicLinks[$i - 1]); ?>" class="edu-help-topic">
                    <div class="flex items-start gap-4">
                        <span class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0 text-white text-base"
                              style="background:<?php echo e($i % 2 === 0 ? 'var(--edu-accent)' : 'var(--edu-primary)'); ?>">
                            <i class="fas fa-<?php echo e($topicIcons[$i - 1]); ?>"></i>
                        </span>
                        <div class="flex-1 min-w-0 text-start">
                            <h3 class="text-base font-bold text-slate-900 mb-1"><?php echo e(__('public.help_topic_'.$i.'_title')); ?></h3>
                            <p class="text-slate-600 text-sm leading-relaxed"><?php echo e(__('public.help_topic_'.$i.'_desc')); ?></p>
                        </div>
                        <i class="fas fa-chevron-left text-slate-300 flex-shrink-0 mt-2 text-sm"></i>
                    </div>
                </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <div class="flex items-center gap-3 mt-12 mb-6 reveal">
                <span class="w-10 h-1 rounded-full shrink-0" style="background:linear-gradient(90deg,var(--edu-primary),var(--edu-accent))"></span>
                <h2 class="text-xl font-bold text-slate-900"><?php echo e(__('public.help_steps_title')); ?></h2>
            </div>
            <div class="relative max-w-2xl mx-auto ps-10 sm:ps-12 border-s-2 border-[var(--edu-primary)]/20 reveal s2">
                <?php $__currentLoopData = range(1, 4); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="relative pb-8 last:pb-0">
                    <span class="edu-help-step-num" style="background:<?php echo e($step % 2 === 1 ? 'var(--edu-primary)' : 'var(--edu-accent)'); ?>"><?php echo e($step); ?></span>
                    <div class="edu-card px-5 py-4">
                        <p class="font-bold text-slate-900 mb-1"><?php echo e(__('public.help_step_'.$step.'_title')); ?></p>
                        <p class="text-sm text-slate-600 leading-relaxed"><?php echo e(__('public.help_step_'.$step.'_desc')); ?></p>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>
</section>

<section class="py-12 lg:py-14 bg-[var(--edu-bg)]">
    <div class="edu-container-full">
        <div class="edu-courses-inner reveal">
            <div class="edu-cta-wrap px-8 py-10 lg:py-12 text-center text-white">
                <h2 class="text-2xl sm:text-3xl font-bold mb-3"><?php echo e(__('public.help_cta_title')); ?></h2>
                <p class="text-white/90 text-sm sm:text-base max-w-xl mx-auto mb-8 leading-7">
                    <?php echo e(__('public.help_cta_desc')); ?>

                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="<?php echo e(route('public.contact')); ?>" class="edu-btn-white !text-[var(--edu-primary)]">
                        <i class="fas fa-paper-plane"></i>
                        <?php echo e(__('public.help_cta_btn')); ?>

                    </a>
                    <a href="<?php echo e(route('public.faq')); ?>" class="edu-btn-ghost-light">
                        <i class="fas fa-circle-question"></i>
                        <?php echo e(__('public.faq_page_title')); ?>

                    </a>
                    <a href="<?php echo e(route('home')); ?>" class="edu-btn-ghost-light">
                        <i class="fas fa-house"></i>
                        <?php echo e($tr('nav.home')); ?>

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
<?php /**PATH C:\xampp\htdocs\sana\resources\views\public\help.blade.php ENDPATH**/ ?>