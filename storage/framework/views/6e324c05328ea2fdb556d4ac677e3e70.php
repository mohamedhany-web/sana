<?php
    $brand = config('app.name', 'Sana');
    $tr = fn (string $key) => str_replace(':brand', $brand, __('sana_home.'.$key));
    $pub = fn (string $key) => str_replace(':brand', $brand, __('public.'.$key));
    $termsIcons = ['check-circle', 'chalkboard-user', 'user-lock', 'wallet', 'copyright', 'ban', 'scale-balanced', 'file-signature'];
    $relatedLinks = [
        ['route' => 'public.privacy', 'icon' => 'shield-halved', 'label' => 'legal_terms_link_privacy'],
        ['route' => 'public.refund', 'icon' => 'rotate-left', 'label' => 'legal_terms_link_refund'],
        ['route' => 'public.certificates', 'icon' => 'certificate', 'label' => 'legal_terms_link_certificates'],
        ['route' => 'public.contact', 'icon' => 'envelope', 'label' => 'legal_terms_link_contact'],
    ];
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
    <title><?php echo e(__('public.terms_page_title')); ?> — <?php echo e($brand); ?></title>
    <meta name="description" content="<?php echo e($pub('legal_terms_meta')); ?>">
    <meta name="keywords" content="<?php echo e($pub('legal_terms_keywords')); ?>">
    <meta name="theme-color" content="#5B21B6">
    <link rel="canonical" href="<?php echo e(url('/terms')); ?>">
    <meta property="og:title" content="<?php echo e(__('public.terms_page_title')); ?> — <?php echo e($brand); ?>">
    <meta property="og:description" content="<?php echo e($pub('legal_terms_meta')); ?>">
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
                    <span><?php echo e(__('public.terms_page_title')); ?></span>
                </nav>
                <span class="sana-sub-hero__eyebrow"><i class="fas fa-gavel"></i> <?php echo e(__('public.terms_page_title')); ?></span>
                <h1 class="sana-sub-hero__title">
                    <?php echo e(__('public.terms_short')); ?>

                    <span class="hl"><?php echo e($brand); ?></span>
                </h1>
                <p class="sana-sub-hero__sub"><?php echo e($pub('legal_terms_hero_sub')); ?></p>
                <div class="sana-sub-hero__actions">
                    <a href="<?php echo e(route('public.contact')); ?>" class="sana-btn sana-btn--yellow">
                        <i class="fas fa-envelope"></i> <?php echo e(__('public.contact_page_title')); ?>

                    </a>
                    <a href="<?php echo e(route('public.privacy')); ?>" class="sana-btn sana-btn--white-outline">
                        <i class="fas fa-shield-halved"></i> <?php echo e(__('public.privacy_page_title')); ?>

                    </a>
                </div>
            </div>
            <svg class="sana-sub-hero__illus" viewBox="0 0 160 160" fill="none" aria-hidden="true">
                <rect x="36" y="32" width="88" height="104" rx="12" fill="rgba(255,255,255,0.1)" stroke="rgba(255,255,255,0.35)" stroke-width="1.5"/>
                <path d="M52 56 H108 M52 72 H96 M52 88 H88 M52 104 H76" stroke="rgba(255,255,255,0.45)" stroke-width="2" stroke-linecap="round"/>
                <circle cx="112" cy="112" r="22" fill="rgba(251,191,36,0.35)" stroke="#FBBF24" stroke-width="1.5"/>
                <path d="M104 112 L110 118 L122 106" stroke="#92400E" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                <circle cx="48" cy="44" r="6" fill="rgba(255,255,255,0.35)"/>
            </svg>
        </div>
    </div>
</section>

<section class="sana-section">
    <div class="sana-container">
        <div class="sana-legal-intro sana-reveal">
            <span class="sana-legal-intro__icon"><i class="fas fa-file-contract"></i></span>
            <p><?php echo nl2br(e($pub('legal_terms_intro'))); ?></p>
        </div>
    </div>
</section>

<section class="sana-section sana-section--soft">
    <div class="sana-container">
        <div class="sana-head sana-head--center sana-reveal" style="margin-bottom:32px">
            <h2 class="sana-head__title"><?php echo e(__('public.terms_page_title')); ?></h2>
            <span class="sana-head__line"></span>
            <p class="sana-head__sub"><?php echo e($pub('legal_terms_hero_sub')); ?></p>
        </div>
        <div class="sana-legal-grid">
            <?php $__currentLoopData = range(1, 8); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <article class="sana-legal-card sana-reveal <?php if($i === 8): ?> is-wide <?php endif; ?>">
                <div class="sana-legal-card__head">
                    <span class="sana-legal-card__icon <?php if($i % 2 === 0): ?> sana-legal-card__icon--gold <?php endif; ?>">
                        <i class="fas fa-<?php echo e($termsIcons[$i - 1]); ?>"></i>
                    </span>
                    <h2><?php echo e(__('public.legal_terms_s'.$i.'_title')); ?></h2>
                </div>
                <p><?php echo nl2br(e($pub('legal_terms_s'.$i.'_body'))); ?></p>
            </article>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>

<section class="sana-section">
    <div class="sana-container">
        <div class="sana-legal-related sana-reveal">
            <div class="sana-head sana-head--center" style="margin-bottom:28px">
                <h2 class="sana-head__title"><?php echo e(__('public.legal_terms_related_title')); ?></h2>
                <span class="sana-head__line"></span>
                <p class="sana-head__sub"><?php echo e(__('public.legal_terms_related_sub')); ?></p>
            </div>
            <div class="sana-legal-related__grid">
                <?php $__currentLoopData = $relatedLinks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route($link['route'])); ?>" class="sana-legal-related-card">
                    <span class="sana-legal-related-card__icon"><i class="fas fa-<?php echo e($link['icon']); ?>"></i></span>
                    <strong><?php echo e(__('public.'.$link['label'])); ?></strong>
                    <span><?php echo e(__('public.services_read_more')); ?> <i class="fas fa-arrow-left"></i></span>
                </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
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
                <a href="<?php echo e(route('home')); ?>" class="sana-btn sana-btn--ghost-light">
                    <i class="fas fa-home"></i> <?php echo e(__('public.home')); ?>

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
<?php /**PATH C:\xampp\htdocs\sana\resources\views\public\terms.blade.php ENDPATH**/ ?>