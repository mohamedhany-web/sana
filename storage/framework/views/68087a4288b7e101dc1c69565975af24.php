<?php
    $brand = config('app.name', 'Sana');
    $tr = fn (string $key) => str_replace(':brand', $brand, __('sana_home.'.$key));
    $defaultGrouped = collect($defaultFaqs ?? [])->groupBy('category');
    $hasDbFaqs = isset($faqs) && $faqs->isNotEmpty();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
    <title><?php echo e(__('public.faq_page_title')); ?> — <?php echo e($brand); ?></title>
    <meta name="description" content="<?php echo e(__('public.faq_meta_description', ['brand' => $brand])); ?>">
    <meta name="theme-color" content="#5B21B6">
    <link rel="canonical" href="<?php echo e(url('/faq')); ?>">
    <?php echo $__env->make('partials.favicon-links', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('partials.seo-jsonld', ['jsonldType' => 'website'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@800;900&family=Tajawal:wght@500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <?php echo $__env->make('partials.rtl-base', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('landing.sana.theme', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('landing.sana.courses-catalog-theme', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('landing.sana.subpages-theme', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</head>
<body class="sana-home sana-courses-page">

<div id="sana-scroll-progress"></div>
<?php echo $__env->make('landing.sana.navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<main class="sana-sub-page">

<section class="sana-sub-hero">
    <div class="sana-container">
        <div class="sana-sub-hero__grid sana-reveal">
            <div class="sana-sub-hero__content">
                <nav class="sana-sub-hero__breadcrumb" aria-label="مسار التنقل">
                    <a href="<?php echo e(route('home')); ?>"><?php echo e($tr('nav.home')); ?></a>
                    <i class="fas fa-chevron-left" style="font-size:10px;opacity:.5"></i>
                    <span><?php echo e(__('public.faq_page_title')); ?></span>
                </nav>
                <span class="sana-sub-hero__eyebrow"><i class="fas fa-circle-question"></i> <?php echo e(__('public.faq_hero_highlight')); ?></span>
                <h1 class="sana-sub-hero__title"><?php echo e(__('public.faq_page_title')); ?></h1>
                <p class="sana-sub-hero__sub"><?php echo e(__('public.faq_hero_sub')); ?></p>
                <div class="sana-sub-hero__actions">
                    <a href="#faq-main" class="sana-btn sana-btn--yellow"><i class="fas fa-list"></i> <?php echo e(__('public.faq_filter_all')); ?></a>
                    <a href="<?php echo e(route('public.contact')); ?>" class="sana-btn sana-btn--white-outline"><i class="fas fa-envelope"></i> <?php echo e(__('public.contact_page_title')); ?></a>
                </div>
            </div>
            <svg class="sana-sub-hero__illus" viewBox="0 0 160 160" fill="none" aria-hidden="true">
                <circle cx="80" cy="80" r="64" fill="rgba(255,255,255,0.08)" stroke="rgba(255,255,255,0.2)"/>
                <text x="80" y="92" text-anchor="middle" fill="#FBBF24" font-size="52" font-weight="900">?</text>
                <circle cx="120" cy="48" r="8" fill="rgba(255,255,255,0.35)"/>
                <circle cx="44" cy="112" r="6" fill="rgba(251,191,36,0.5)"/>
            </svg>
        </div>
    </div>
</section>

<section class="sana-section" id="faq-main">
    <div class="sana-container">
        <div class="sana-faq-layout sana-reveal">
            <aside class="sana-faq-sidebar">
                <?php if(isset($categories) && $categories->isNotEmpty()): ?>
                <div class="sana-faq-filters">
                    <button type="button" class="sana-faq-filter is-active filter-btn-faq" data-category="all"><?php echo e(__('public.faq_filter_all')); ?></button>
                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <button type="button" class="sana-faq-filter filter-btn-faq" data-category="<?php echo e($cat); ?>"><?php echo e($cat); ?></button>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <?php endif; ?>
                <div class="sana-faq-side-panel">
                    <h4><?php echo e(__('public.faq_quick_links')); ?></h4>
                    <a href="<?php echo e(route('public.contact')); ?>"><i class="fas fa-paper-plane"></i> <?php echo e(__('public.contact_page_title')); ?></a>
                    <a href="<?php echo e(route('public.help')); ?>"><i class="fas fa-book-open"></i> <?php echo e(__('public.help_page_title')); ?></a>
                    <a href="<?php echo e(route('public.certificates')); ?>"><i class="fas fa-certificate"></i> <?php echo e(__('public.certificates_page_title')); ?></a>
                    <a href="<?php echo e(route('public.pricing')); ?>"><i class="fas fa-tags"></i> <?php echo e(__('public.pricing_page_title')); ?></a>
                </div>
            </aside>

            <div class="sana-faq-main">
                <?php if(isset($categories) && $categories->isNotEmpty()): ?>
                <div class="sana-faq-filters sana-faq-filters--mobile">
                    <button type="button" class="sana-faq-filter is-active filter-btn-faq" data-category="all"><?php echo e(__('public.faq_filter_all')); ?></button>
                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <button type="button" class="sana-faq-filter filter-btn-faq" data-category="<?php echo e($cat); ?>"><?php echo e($cat); ?></button>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <?php endif; ?>

                <?php if($hasDbFaqs): ?>
                <?php $__currentLoopData = $faqs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $categoryName => $categoryFaqs): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="sana-faq-block faq-block" data-category="<?php echo e($categoryName ?? 'general'); ?>">
                    <?php if($categoryName): ?>
                    <h2 class="sana-faq-block__title"><i class="fas fa-layer-group"></i> <?php echo e($categoryName); ?></h2>
                    <?php endif; ?>
                    <div class="sana-faq" id="sana-faq-<?php echo e($loop->index); ?>">
                        <?php $__currentLoopData = $categoryFaqs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $faq): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="sana-faq-item <?php echo e($loop->parent->first && $i === 0 ? 'is-open' : ''); ?>">
                            <button type="button" class="sana-faq-q" aria-expanded="<?php echo e($loop->parent->first && $i === 0 ? 'true' : 'false'); ?>">
                                <?php echo e($faq->question); ?> <i class="fas fa-chevron-down"></i>
                            </button>
                            <div class="sana-faq-a"><?php echo nl2br(e($faq->answer)); ?></div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>

                <?php if($defaultGrouped->isNotEmpty()): ?>
                <div class="sana-faq-block faq-block default-faqs" data-category="default">
                    <h2 class="sana-faq-block__title"><i class="fas fa-graduation-cap"></i> <?php echo e(__('public.faq_section_platform', ['brand' => $brand])); ?></h2>
                    <?php $__currentLoopData = $defaultGrouped; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $catName => $items): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if($catName): ?>
                    <p style="font-size:0.82rem;font-weight:800;color:var(--p);margin:16px 0 10px"><i class="fas fa-tag"></i> <?php echo e($catName); ?></p>
                    <?php endif; ?>
                    <div class="sana-faq sana-faq-group">
                        <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="sana-faq-item <?php echo e($loop->parent->first && $loop->first && $i === 0 && !$hasDbFaqs ? 'is-open' : ''); ?>">
                            <button type="button" class="sana-faq-q"><?php echo e($item['question']); ?> <i class="fas fa-chevron-down"></i></button>
                            <div class="sana-faq-a"><?php echo nl2br(e($item['answer'] ?? '')); ?></div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <?php endif; ?>

                <?php if(!$hasDbFaqs && $defaultGrouped->isEmpty()): ?>
                <div class="sana-sub-empty">
                    <div class="sana-sub-empty__icon"><i class="fas fa-question"></i></div>
                    <h3 style="font-weight:900;margin:0 0 8px"><?php echo e(__('public.faq_empty_title')); ?></h3>
                    <a href="<?php echo e(route('public.contact')); ?>" class="sana-btn sana-btn--purple" style="margin-top:16px">
                        <i class="fas fa-envelope"></i> <?php echo e(__('public.faq_empty_cta')); ?>

                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<section class="sana-sub-final">
    <div class="sana-container sana-reveal">
        <div class="sana-sub-final__box">
            <h2><?php echo e(__('public.faq_cta_title')); ?></h2>
            <p><?php echo e(__('public.faq_cta_desc')); ?></p>
            <div class="sana-sub-final__actions">
                <a href="<?php echo e(route('public.contact')); ?>" class="sana-btn sana-btn--yellow"><?php echo e(__('public.faq_cta_btn')); ?> <i class="fas fa-arrow-left"></i></a>
                <a href="<?php echo e(route('public.help')); ?>" class="sana-btn sana-btn--ghost-light"><?php echo e(__('public.help_page_title')); ?> <i class="fas fa-life-ring"></i></a>
            </div>
        </div>
    </div>
</section>

</main>

<?php echo $__env->make('landing.sana.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('landing.sana.scripts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<script>
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
document.querySelectorAll('.sana-faq-group, .sana-faq[id^="sana-faq-"]').forEach(function (container) {
    container.addEventListener('click', function (e) {
        var btn = e.target.closest('.sana-faq-q');
        if (!btn) return;
        var item = btn.closest('.sana-faq-item');
        var open = item.classList.contains('is-open');
        container.querySelectorAll('.sana-faq-item').forEach(function (i) {
            i.classList.remove('is-open');
            i.querySelector('.sana-faq-q')?.setAttribute('aria-expanded', 'false');
        });
        if (!open) {
            item.classList.add('is-open');
            btn.setAttribute('aria-expanded', 'true');
        }
    });
});
</script>
<?php echo $__env->make('partials.pwa-service-worker', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\sana\resources\views\public\faq.blade.php ENDPATH**/ ?>