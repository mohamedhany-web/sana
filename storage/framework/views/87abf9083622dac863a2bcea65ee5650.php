<?php
    $brand = config('app.name', 'Sana');
    $tr = fn (string $key) => str_replace(':brand', $brand, __('sana_home.'.$key));
    $pub = fn (string $key) => str_replace(':brand', $brand, __('public.'.$key));
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
    <title><?php echo e(__('public.certificates_page_title')); ?> — <?php echo e($brand); ?></title>
    <meta name="description" content="<?php echo e($pub('certificates_meta_description')); ?>">
    <meta name="theme-color" content="#5B21B6">
    <link rel="canonical" href="<?php echo e(url('/certificates')); ?>">
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
                    <span><?php echo e(__('public.certificates_page_title')); ?></span>
                </nav>
                <span class="sana-sub-hero__eyebrow"><i class="fas fa-certificate"></i> <?php echo e(__('public.certificates_hero_badge')); ?></span>
                <h1 class="sana-sub-hero__title">
                    <?php echo e(__('public.certificates_hero_title')); ?>

                    <span class="hl"><?php echo e(__('public.certificates_hero_highlight')); ?></span>
                </h1>
                <p class="sana-sub-hero__sub"><?php echo e($pub('certificates_hero_sub')); ?></p>
                <div class="sana-sub-hero__actions">
                    <a href="<?php echo e(route('public.certificates.verify')); ?>" class="sana-btn sana-btn--yellow">
                        <i class="fas fa-shield-halved"></i> <?php echo e(__('public.certificates_verify_cta')); ?>

                    </a>
                    <a href="<?php echo e(route('public.courses')); ?>" class="sana-btn sana-btn--white-outline">
                        <i class="fas fa-book-open"></i> <?php echo e(__('public.certificates_browse_courses')); ?>

                    </a>
                </div>
            </div>
            <svg class="sana-sub-hero__illus" viewBox="0 0 160 160" fill="none" aria-hidden="true">
                <circle cx="80" cy="80" r="70" stroke="rgba(255,255,255,0.15)" stroke-width="2" stroke-dasharray="6 8"/>
                <rect x="32" y="36" width="96" height="72" rx="12" fill="rgba(255,255,255,0.12)" stroke="rgba(255,255,255,0.3)" stroke-width="1.5"/>
                <circle cx="80" cy="58" r="14" fill="#FBBF24"/>
                <path d="M74 58 L78 62 L86 54" stroke="#92400E" stroke-width="2" stroke-linecap="round"/>
                <path d="M48 78 H112 M48 88 H96 M48 98 H88" stroke="rgba(255,255,255,0.45)" stroke-width="2" stroke-linecap="round"/>
                <path d="M56 120 L80 132 L104 120 V108 H56 Z" fill="rgba(251,191,36,0.35)" stroke="#FBBF24"/>
            </svg>
        </div>
    </div>
</section>

<section class="sana-section">
    <div class="sana-container">
        <div class="sana-cert-grid sana-reveal">
            <article class="sana-cert-card">
                <div class="sana-cert-card__icon"><i class="fas fa-certificate"></i></div>
                <h3><?php echo e(__('public.certificates_type_completion_title')); ?></h3>
                <p><?php echo e(__('public.certificates_type_completion_desc')); ?></p>
                <ul>
                    <li><i class="fas fa-check-circle"></i><?php echo e(__('public.certificates_type_completion_f1')); ?></li>
                    <li><i class="fas fa-check-circle"></i><?php echo e(__('public.certificates_type_completion_f2')); ?></li>
                    <li><i class="fas fa-check-circle"></i><?php echo e(__('public.certificates_type_completion_f3')); ?></li>
                </ul>
            </article>
            <article class="sana-cert-card">
                <div class="sana-cert-card__icon sana-cert-card__icon--gold"><i class="fas fa-medal"></i></div>
                <h3><?php echo e(__('public.certificates_type_pro_title')); ?></h3>
                <p><?php echo e(__('public.certificates_type_pro_desc')); ?></p>
                <ul>
                    <li><i class="fas fa-check-circle"></i><?php echo e(__('public.certificates_type_pro_f1')); ?></li>
                    <li><i class="fas fa-check-circle"></i><?php echo e(__('public.certificates_type_pro_f2')); ?></li>
                    <li><i class="fas fa-check-circle"></i><?php echo e(__('public.certificates_type_pro_f3')); ?></li>
                </ul>
            </article>
        </div>
    </div>
</section>

<section class="sana-section sana-section--soft">
    <div class="sana-container">
        <div class="sana-head sana-head--center sana-reveal" style="margin-bottom:36px">
            <h2 class="sana-head__title"><?php echo e(__('public.certificates_how_title')); ?></h2>
            <span class="sana-head__line"></span>
            <p class="sana-head__sub"><?php echo e(__('public.certificates_how_sub')); ?></p>
        </div>
        <div class="sana-cert-steps">
            <?php $__currentLoopData = [
                ['01', 'user-graduate', 'certificates_step1_title', 'certificates_step1_desc'],
                ['02', 'clipboard-check', 'certificates_step2_title', 'certificates_step2_desc'],
                ['03', 'file-certificate', 'certificates_step3_title', 'certificates_step3_desc'],
            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="sana-cert-step sana-reveal">
                <span class="sana-cert-step__num"><?php echo e($step[0]); ?></span>
                <span class="sana-cert-step__icon"><i class="fas fa-<?php echo e($step[1]); ?>"></i></span>
                <strong><?php echo e(__('public.'.$step[2])); ?></strong>
                <p><?php echo e(__('public.'.$step[3])); ?></p>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>

<section class="sana-section">
    <div class="sana-container sana-reveal">
        <div class="sana-cert-verify-box">
            <span class="sana-head__eyebrow"><i class="fas fa-shield-halved"></i> <?php echo e(__('public.certificates_panel_title')); ?></span>
            <h2 class="sana-head__title" style="margin:12px 0"><?php echo e($pub('certificates_panel_desc')); ?></h2>
            <a href="<?php echo e(route('public.certificates.verify')); ?>" class="sana-btn sana-btn--purple" style="margin-top:8px">
                <i class="fas fa-magnifying-glass"></i> <?php echo e(__('public.certificates_panel_btn')); ?>

            </a>
        </div>
    </div>
</section>

<section class="sana-sub-final">
    <div class="sana-container sana-reveal">
        <div class="sana-sub-final__box">
            <h2><?php echo e(__('public.certificates_cta_title')); ?></h2>
            <p><?php echo e($pub('certificates_cta_desc')); ?></p>
            <div class="sana-sub-final__actions">
                <a href="<?php echo e(route('register')); ?>" class="sana-btn sana-btn--yellow"><i class="fas fa-user-plus"></i> <?php echo e($tr('nav.get_started')); ?></a>
                <a href="<?php echo e(route('public.help')); ?>" class="sana-btn sana-btn--ghost-light"><i class="fas fa-circle-question"></i> <?php echo e(__('public.help_page_title')); ?></a>
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
<?php /**PATH C:\xampp\htdocs\sana\resources\views\public\certificates.blade.php ENDPATH**/ ?>