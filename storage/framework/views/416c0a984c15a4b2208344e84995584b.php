<?php
    $brand = config('app.name', 'Sana');
    $tr = fn (string $key) => str_replace(':brand', $brand, __('sana_home.'.$key));
    $pub = fn (string $key) => str_replace(':brand', $brand, __('public.'.$key));
    $refundIcons = ['list-check', 'scale-balanced', 'paper-plane', 'clock'];
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
    <title><?php echo e(__('public.legal_refund_page_title')); ?> — <?php echo e($brand); ?></title>
    <meta name="description" content="<?php echo e($pub('legal_refund_meta')); ?>">
    <meta name="theme-color" content="#5B21B6">
    <link rel="canonical" href="<?php echo e(url('/refund')); ?>">
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
                <nav class="sana-sub-hero__breadcrumb" aria-label="???? ??????">
                    <a href="<?php echo e(route('home')); ?>"><?php echo e($tr('nav.home')); ?></a>
                    <i class="fas fa-chevron-left" style="font-size:10px;opacity:.5"></i>
                    <a href="<?php echo e(route('public.terms')); ?>"><?php echo e(__('public.terms_page_title')); ?></a>
                    <i class="fas fa-chevron-left" style="font-size:10px;opacity:.5"></i>
                    <span><?php echo e(__('public.legal_refund_page_title')); ?></span>
                </nav>
                <span class="sana-sub-hero__eyebrow"><i class="fas fa-rotate-left"></i> <?php echo e(__('public.legal_refund_page_title')); ?></span>
                <h1 class="sana-sub-hero__title">
                    <?php echo e(__('public.legal_refund_page_title')); ?>

                    <span class="hl"><?php echo e($brand); ?></span>
                </h1>
                <p class="sana-sub-hero__sub"><?php echo e($pub('legal_refund_hero_sub')); ?></p>
                <div class="sana-sub-hero__actions">
                    <a href="<?php echo e(route('public.contact')); ?>" class="sana-btn sana-btn--yellow">
                        <i class="fas fa-envelope"></i> <?php echo e(__('public.contact_page_title')); ?>

                    </a>
                    <a href="<?php echo e(route('public.terms')); ?>" class="sana-btn sana-btn--white-outline">
                        <i class="fas fa-file-contract"></i> <?php echo e(__('public.terms_page_title')); ?>

                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="sana-section">
    <div class="sana-container">
        <div class="sana-legal-intro sana-reveal">
            <span class="sana-legal-intro__icon"><i class="fas fa-rotate-left"></i></span>
            <p><?php echo nl2br(e($pub('legal_refund_intro'))); ?></p>
        </div>
    </div>
</section>

<section class="sana-section sana-section--soft">
    <div class="sana-container">
        <div class="sana-legal-grid">
            <?php $__currentLoopData = range(1, 4); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <article class="sana-legal-card sana-reveal <?php if($i === 4): ?> is-wide <?php endif; ?>">
                <div class="sana-legal-card__head">
                    <span class="sana-legal-card__icon <?php if($i % 2 === 0): ?> sana-legal-card__icon--gold <?php endif; ?>">
                        <i class="fas fa-<?php echo e($refundIcons[$i - 1]); ?>"></i>
                    </span>
                    <h2><?php echo e(__('public.legal_refund_s'.$i.'_title')); ?></h2>
                </div>
                <p><?php echo nl2br(e($pub('legal_refund_s'.$i.'_body'))); ?></p>
            </article>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>

<section class="sana-sub-final">
    <div class="sana-container sana-reveal">
        <div class="sana-sub-final__box">
            <h2><?php echo e(__('public.legal_cta_title')); ?></h2>
            <p><?php echo e(__('public.legal_cta_desc')); ?></p>
            <div class="sana-sub-final__actions">
                <a href="<?php echo e(route('public.contact')); ?>" class="sana-btn sana-btn--yellow">
                    <i class="fas fa-paper-plane"></i> <?php echo e(__('public.contact_page_title')); ?>

                </a>
                <a href="<?php echo e(route('public.terms')); ?>" class="sana-btn sana-btn--ghost-light">
                    <i class="fas fa-file-contract"></i> <?php echo e(__('public.terms_page_title')); ?>

                </a>
            </div>
        </div>
    </div>
</section>

</main>

<?php echo $__env->make('landing.sana.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('landing.sana.scripts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('partials.pwa-service-worker', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\sana\resources\views\public\refund.blade.php ENDPATH**/ ?>