<?php
    $brand = config('app.name', 'Sana');
    $bc = config('brand.colors');
    $tr = fn (string $key) => str_replace(':brand', $brand, __('sana_home.'.$key));
    $defaultGrouped = collect($defaultFaqs ?? [])->groupBy('category');
    $hasDbFaqs = isset($faqs) && $faqs->isNotEmpty();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
    <title><?php echo e(__('public.faq_page_title')); ?> - <?php echo e($brand); ?></title>
    <meta name="description" content="<?php echo e(__('public.faq_meta_description', ['brand' => $brand])); ?>">
    <meta name="theme-color" content="<?php echo e($bc['blue']); ?>">
    <link rel="canonical" href="<?php echo e(url('/faq')); ?>">
    <meta property="og:title" content="<?php echo e(__('public.faq_page_title')); ?> - <?php echo e($brand); ?>">
    <meta property="og:description" content="<?php echo e(__('public.faq_meta_description', ['brand' => $brand])); ?>">
    <meta property="og:image" content="<?php echo e(asset('images/og-image.jpg')); ?>">
    <?php echo $__env->make('partials.favicon-links', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('partials.seo-jsonld', ['jsonldType' => 'website'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
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
        <div class="edu-courses-inner">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-12 items-center reveal">
                <div>
                    <nav class="edu-breadcrumb mb-4" aria-label="مسار التنقل">
                        <a href="<?php echo e(route('home')); ?>"><?php echo e($tr('nav.home')); ?></a>
                        <i class="fas fa-chevron-left text-[10px] opacity-50"></i>
                        <span class="text-slate-800 font-semibold"><?php echo e(__('public.faq_page_title')); ?></span>
                    </nav>
                    <span class="edu-badge mb-4"><i class="fas fa-circle-question"></i> <?php echo e(__('public.faq_hero_highlight')); ?></span>
                    <h1 class="edu-section-title text-slate-900">
                        <?php echo e(__('public.faq_page_title')); ?>

                    </h1>
                    <p class="text-slate-600 leading-8 mt-3 text-sm lg:text-base max-w-xl">
                        <?php echo e(__('public.faq_hero_sub')); ?>

                    </p>
                    <div class="edu-hero-actions mt-6">
                        <a href="#faq-main" class="edu-btn-primary">
                            <i class="fas fa-list"></i>
                            <?php echo e(__('public.faq_filter_all')); ?>

                        </a>
                        <a href="<?php echo e(route('public.contact')); ?>" class="edu-btn-outline">
                            <i class="fas fa-envelope"></i>
                            <?php echo e(__('public.contact_page_title')); ?>

                        </a>
                    </div>
                </div>
                <div class="edu-hero-panel reveal s1">
                    <div class="flex items-start gap-3 rounded-xl bg-white/10 border border-white/15 px-4 py-3 mb-4">
                        <span class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 bg-[var(--edu-accent)]"><i class="fas fa-magnifying-glass"></i></span>
                        <div>
                            <p class="font-bold text-sm"><?php echo e(__('public.faq_page_title')); ?></p>
                            <p class="text-sm text-white/80 leading-relaxed"><?php echo e(__('public.faq_sidebar_hint')); ?></p>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <a href="<?php echo e(route('public.courses')); ?>" class="inline-flex items-center gap-1.5 rounded-full px-3 py-1.5 text-xs font-bold bg-white/15 border border-white/20 hover:bg-white/25 transition-colors text-white">
                            <i class="fas fa-graduation-cap"></i> <?php echo e($tr('nav.courses')); ?>

                        </a>
                        <a href="<?php echo e(route('public.certificates')); ?>" class="inline-flex items-center gap-1.5 rounded-full px-3 py-1.5 text-xs font-bold bg-white/15 border border-white/20 hover:bg-white/25 transition-colors text-white">
                            <i class="fas fa-certificate"></i> <?php echo e(__('public.certificates_page_title')); ?>

                        </a>
                        <a href="<?php echo e(route('public.pricing')); ?>" class="inline-flex items-center gap-1.5 rounded-full px-3 py-1.5 text-xs font-bold bg-white/15 border border-white/20 hover:bg-white/25 transition-colors text-white">
                            <i class="fas fa-tags"></i> <?php echo e(__('public.pricing_page_title')); ?>

                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="faq-main" class="py-10 lg:py-14 bg-white">
    <div class="edu-container-full">
        <div class="edu-courses-inner">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10 items-start">
                <aside class="lg:col-span-4 xl:col-span-3 lg:sticky lg:top-28 lg:self-start space-y-5 reveal">
                    <?php if(isset($categories) && $categories->isNotEmpty()): ?>
                    <div class="edu-card p-5">
                        <p class="text-xs font-bold uppercase tracking-wide text-[var(--edu-primary)] mb-3"><?php echo e(__('public.faq_sidebar_categories')); ?></p>
                        <div class="flex flex-col gap-2">
                            <button type="button" class="edu-faq-filter is-active filter-btn-faq" data-category="all"><?php echo e(__('public.faq_filter_all')); ?></button>
                            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <button type="button" class="edu-faq-filter filter-btn-faq" data-category="<?php echo e($cat); ?>"><?php echo e($cat); ?></button>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="edu-faq-side-panel">
                        <p class="text-xs font-bold uppercase tracking-wide text-white/70 mb-3"><?php echo e(__('public.faq_quick_links')); ?></p>
                        <ul class="space-y-2">
                            <li><a href="<?php echo e(route('public.contact')); ?>" class="edu-faq-side-link"><i class="fas fa-paper-plane text-[var(--edu-accent)]"></i> <?php echo e(__('public.contact_page_title')); ?></a></li>
                            <li><a href="<?php echo e(route('public.help')); ?>" class="edu-faq-side-link"><i class="fas fa-book-open text-[var(--edu-accent)]"></i> <?php echo e(__('public.help_page_title')); ?></a></li>
                            <li><a href="<?php echo e(route('public.privacy')); ?>" class="edu-faq-side-link"><i class="fas fa-shield-halved text-[var(--edu-accent)]"></i> <?php echo e(__('public.privacy_page_title')); ?></a></li>
                        </ul>
                    </div>
                </aside>

                <div class="lg:col-span-8 xl:col-span-9 space-y-8 min-w-0 reveal s1">
                    <?php if(isset($categories) && $categories->isNotEmpty()): ?>
                    <div class="lg:hidden flex flex-wrap gap-2">
                        <button type="button" class="edu-faq-filter is-active filter-btn-faq !w-auto" data-category="all"><?php echo e(__('public.faq_filter_all')); ?></button>
                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <button type="button" class="edu-faq-filter filter-btn-faq !w-auto" data-category="<?php echo e($cat); ?>"><?php echo e($cat); ?></button>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <?php endif; ?>

                    <?php if($hasDbFaqs): ?>
                    <?php $__currentLoopData = $faqs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $categoryName => $categoryFaqs): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="faq-block" data-category="<?php echo e($categoryName ?? 'general'); ?>">
                        <?php if($categoryName): ?>
                        <div class="flex items-center gap-3 mb-4">
                            <span class="w-1 h-7 rounded-full shrink-0" style="background:linear-gradient(180deg,var(--edu-accent),var(--edu-primary))"></span>
                            <h2 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                                <i class="fas fa-layer-group text-[var(--edu-primary)]"></i>
                                <?php echo e($categoryName); ?>

                            </h2>
                        </div>
                        <?php endif; ?>
                        <div class="space-y-3">
                            <?php $__currentLoopData = $categoryFaqs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $faq): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="edu-card edu-faq-acc !p-0" x-data="{ open: false }" :class="{ 'is-open': open }">
                                <button type="button" @click="open = !open" class="w-full px-5 py-4 text-start flex items-center justify-between gap-4 hover:bg-slate-50/80 transition-colors">
                                    <span class="text-base font-bold text-slate-800 flex-1 min-w-0"><?php echo e($faq->question); ?></span>
                                    <i class="fas fa-chevron-down faq-chevron flex-shrink-0"></i>
                                </button>
                                <div x-show="open" x-cloak class="border-t border-slate-100">
                                    <div class="px-5 py-4 text-slate-600 leading-relaxed text-sm sm:text-base">
                                        <?php echo nl2br(e($faq->answer)); ?>

                                    </div>
                                </div>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>

                    <?php if($defaultGrouped->isNotEmpty()): ?>
                    <div id="default" class="faq-block default-faqs" data-category="default">
                        <div class="flex items-center gap-3 mb-4">
                            <span class="w-1 h-7 rounded-full shrink-0" style="background:linear-gradient(180deg,var(--edu-primary),var(--edu-accent))"></span>
                            <h2 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                                <i class="fas fa-graduation-cap text-[var(--edu-accent)]"></i>
                                <?php echo e(__('public.faq_section_platform', ['brand' => $brand])); ?>

                            </h2>
                        </div>
                        <div class="space-y-6">
                            <?php $__currentLoopData = $defaultGrouped; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $catName => $items): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div>
                                <?php if($catName): ?>
                                <h3 class="text-sm font-bold text-slate-600 mb-3 flex items-center gap-2">
                                    <i class="fas fa-tag text-[var(--edu-primary)] text-xs"></i>
                                    <?php echo e($catName); ?>

                                </h3>
                                <?php endif; ?>
                                <div class="space-y-3">
                                    <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="edu-card edu-faq-acc !p-0" x-data="{ open: false }" :class="{ 'is-open': open }">
                                        <button type="button" @click="open = !open" class="w-full px-5 py-4 text-start flex items-center justify-between gap-4 hover:bg-slate-50/80 transition-colors">
                                            <span class="text-base font-bold text-slate-800 flex-1 min-w-0"><?php echo e($item['question']); ?></span>
                                            <i class="fas fa-chevron-down faq-chevron flex-shrink-0"></i>
                                        </button>
                                        <div x-show="open" x-cloak class="border-t border-slate-100">
                                            <div class="px-5 py-4 text-slate-600 leading-relaxed text-sm sm:text-base">
                                                <?php echo nl2br(e($item['answer'] ?? '')); ?>

                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if(!$hasDbFaqs && $defaultGrouped->isEmpty()): ?>
                    <div class="edu-card text-center py-14 px-6 border-dashed">
                        <div class="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4 text-white text-2xl" style="background:linear-gradient(135deg,var(--edu-primary),var(--edu-purple))">
                            <i class="fas fa-question"></i>
                        </div>
                        <p class="text-slate-600 text-lg font-medium mb-5"><?php echo e(__('public.faq_empty_title')); ?></p>
                        <a href="<?php echo e(route('public.contact')); ?>" class="edu-btn-primary">
                            <i class="fas fa-envelope"></i>
                            <?php echo e(__('public.faq_empty_cta')); ?>

                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-12 lg:py-14 bg-[var(--edu-bg)]">
    <div class="edu-container-full">
        <div class="edu-courses-inner reveal">
            <div class="edu-cta-wrap px-8 py-10 lg:py-12 text-center text-white">
                <h2 class="text-2xl sm:text-3xl font-bold mb-3"><?php echo e(__('public.faq_cta_title')); ?></h2>
                <p class="text-white/90 text-sm sm:text-base max-w-xl mx-auto mb-8 leading-7">
                    <?php echo e(__('public.faq_cta_desc')); ?>

                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="<?php echo e(route('public.contact')); ?>" class="edu-btn-white !text-[var(--edu-primary)]">
                        <i class="fas fa-paper-plane"></i>
                        <?php echo e(__('public.faq_cta_btn')); ?>

                    </a>
                    <a href="<?php echo e(route('home')); ?>" class="edu-btn-ghost-light">
                        <i class="fas fa-house"></i>
                        <?php echo e($tr('nav.home')); ?>

                    </a>
                    <a href="<?php echo e(route('public.help')); ?>" class="edu-btn-ghost-light">
                        <i class="fas fa-life-ring"></i>
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

    document.querySelectorAll('.filter-btn-faq').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var cat = this.getAttribute('data-category');
            document.querySelectorAll('.filter-btn-faq').forEach(function (b) {
                b.classList.toggle('is-active', b.getAttribute('data-category') === cat);
            });
            document.querySelectorAll('.faq-block').forEach(function (block) {
                var blockCat = block.getAttribute('data-category');
                block.style.display = (cat === 'all' || blockCat === cat) ? '' : 'none';
            });
        });
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
<?php /**PATH C:\xampp\htdocs\sana\resources\views\public\faq.blade.php ENDPATH**/ ?>