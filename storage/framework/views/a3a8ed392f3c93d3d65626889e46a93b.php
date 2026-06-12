<?php
    $brand = config('app.name', 'Sana');
    $tr = fn (string $key) => str_replace(':brand', $brand, __('sana_home.'.$key));
    $tp = fn (string $key) => __('teacher_policy.'.$key);
    $sections = $tp('sections');
    $relatedLinks = [
        ['route' => 'tutor.apply', 'icon' => 'chalkboard-teacher', 'label' => __('teacher_policy.cta_apply'), 'external' => false],
        ['route' => 'public.terms', 'icon' => 'file-contract', 'label' => __('public.terms_page_title'), 'external' => false],
        ['route' => 'public.privacy', 'icon' => 'shield-halved', 'label' => __('public.privacy_page_title'), 'external' => false],
        ['route' => 'public.contact', 'icon' => 'envelope', 'label' => __('public.contact_page_title'), 'external' => false],
    ];
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
    <title><?php echo e($tp('page_title')); ?> — <?php echo e($brand); ?></title>
    <meta name="description" content="<?php echo e($tp('meta_description')); ?>">
    <meta name="theme-color" content="#5B21B6">
    <link rel="canonical" href="<?php echo e(url('/teacher-policy')); ?>">
    <meta property="og:title" content="<?php echo e($tp('page_title')); ?> — <?php echo e($brand); ?>">
    <meta property="og:description" content="<?php echo e($tp('meta_description')); ?>">
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
<body class="sana-home sana-courses-page sana-teacher-policy-page">

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
                    <span><?php echo e($tp('page_title')); ?></span>
                </nav>
                <span class="sana-sub-hero__eyebrow"><i class="fas fa-chalkboard-teacher"></i> <?php echo e($tp('page_title')); ?></span>
                <h1 class="sana-sub-hero__title">
                    <?php echo e($tp('page_subtitle')); ?>

                    <span class="hl"><?php echo e($brand); ?></span>
                </h1>
                <p class="sana-sub-hero__sub"><?php echo e($tp('hero_sub')); ?></p>
                <div class="sana-sub-hero__actions">
                    <a href="<?php echo e(route('tutor.apply')); ?>" class="sana-btn sana-btn--yellow">
                        <i class="fas fa-file-signature"></i> <?php echo e($tp('cta_apply')); ?>

                    </a>
                    <a href="<?php echo e(route('public.privacy')); ?>" class="sana-btn sana-btn--white-outline">
                        <i class="fas fa-shield-halved"></i> <?php echo e(__('public.privacy_page_title')); ?>

                    </a>
                </div>
            </div>
            <svg class="sana-sub-hero__illus" viewBox="0 0 160 160" fill="none" aria-hidden="true">
                <rect x="28" y="36" width="104" height="88" rx="14" fill="rgba(255,255,255,0.08)" stroke="rgba(255,255,255,0.35)" stroke-width="1.5"/>
                <path d="M44 60 H116 M44 76 H100 M44 92 H88" stroke="rgba(255,255,255,0.4)" stroke-width="2" stroke-linecap="round"/>
                <circle cx="80" cy="118" r="26" fill="rgba(251,191,36,0.3)" stroke="#FBBF24" stroke-width="1.5"/>
                <path d="M68 118 L76 126 L94 108" stroke="#92400E" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                <rect x="52" y="44" width="20" height="8" rx="4" fill="rgba(255,255,255,0.25)"/>
            </svg>
        </div>
    </div>
</section>

<section class="sana-section">
    <div class="sana-container">
        <div class="sana-policy-note sana-reveal">
            <span class="sana-policy-note__icon"><i class="fas fa-scale-balanced"></i></span>
            <p><?php echo e($tp('legal_note')); ?></p>
        </div>
        <div class="sana-legal-intro sana-reveal" style="margin-top:18px">
            <span class="sana-legal-intro__icon"><i class="fas fa-bullseye"></i></span>
            <div>
                <p><?php echo e($tp('intro')); ?></p>
                <p style="margin-top:10px;font-weight:800;color:var(--p)"><?php echo e($tp('intro_commit')); ?></p>
            </div>
        </div>
    </div>
</section>

<section class="sana-section sana-section--soft">
    <div class="sana-container">
        <div class="sana-policy-layout">
            <aside class="sana-policy-toc sana-reveal" aria-label="فهرس السياسة">
                <strong><i class="fas fa-list-ul"></i> محتويات الوثيقة</strong>
                <nav>
                    <?php $__currentLoopData = $sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="#<?php echo e($section['id']); ?>"><?php echo e($section['title']); ?></a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <a href="#annex"><?php echo e($tp('annex_title')); ?></a>
                    <a href="#acknowledgment"><?php echo e($tp('ack_title')); ?></a>
                    <a href="#digital"><?php echo e($tp('digital_title')); ?></a>
                    <a href="#references"><?php echo e($tp('refs_title')); ?></a>
                </nav>
            </aside>

            <div class="sana-policy-main">
                <div class="sana-head sana-head--center sana-reveal" style="margin-bottom:28px">
                    <h2 class="sana-head__title"><?php echo e($tp('page_title')); ?></h2>
                    <span class="sana-head__line"></span>
                    <p class="sana-head__sub"><?php echo e($tp('page_subtitle')); ?></p>
                </div>

                <div class="sana-legal-grid">
                    <?php $__currentLoopData = $sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <article id="<?php echo e($section['id']); ?>" class="sana-legal-card sana-reveal <?php if(!empty($section['wide'])): ?> is-wide <?php endif; ?>">
                        <div class="sana-legal-card__head">
                            <span class="sana-legal-card__icon <?php if($i % 2 === 1): ?> sana-legal-card__icon--gold <?php endif; ?>">
                                <i class="fas fa-<?php echo e($section['icon']); ?>"></i>
                            </span>
                            <h2><?php echo e($section['title']); ?></h2>
                        </div>
                        <p><?php echo e($section['body'] ?? ''); ?></p>
                        <?php if(!empty($section['items'])): ?>
                        <ul class="sana-policy-list">
                            <?php $__currentLoopData = $section['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($item); ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                        <?php endif; ?>
                        <?php if(!empty($section['footer'])): ?>
                        <p class="sana-policy-footer-note"><?php echo e($section['footer']); ?></p>
                        <?php endif; ?>
                    </article>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="sana-section">
    <div class="sana-container">
        <article id="annex" class="sana-legal-card is-wide sana-reveal sana-policy-annex">
            <div class="sana-legal-card__head">
                <span class="sana-legal-card__icon sana-legal-card__icon--gold"><i class="fas fa-file-contract"></i></span>
                <h2><?php echo e($tp('annex_title')); ?></h2>
            </div>
            <p><?php echo e($tp('annex_body')); ?></p>
        </article>
    </div>
</section>

<section class="sana-section sana-section--soft">
    <div class="sana-container">
        <div class="sana-policy-sign-grid sana-reveal">
            <article id="acknowledgment" class="sana-policy-sign">
                <div class="sana-policy-sign__head">
                    <span><i class="fas fa-pen-fancy"></i></span>
                    <div>
                        <h2><?php echo e($tp('ack_title')); ?></h2>
                        <p><?php echo e($tp('ack_body')); ?></p>
                    </div>
                </div>
                <div class="sana-policy-sign__fields">
                    <?php $__currentLoopData = $tp('ack_fields'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="sana-policy-sign__field">
                        <label><?php echo e($label); ?></label>
                        <span aria-hidden="true"></span>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <p class="sana-policy-sign__hint"><i class="fas fa-print"></i> نموذج للطباعة والتوقيع — يُرفق مع عقد التعاون</p>
            </article>

            <article id="digital" class="sana-policy-digital">
                <h2><i class="fas fa-laptop"></i> <?php echo e($tp('digital_title')); ?></h2>
                <p><?php echo e($tp('digital_sub')); ?></p>
                <ul class="sana-policy-checklist">
                    <?php $__currentLoopData = $tp('digital_items'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><i class="fas fa-check-circle"></i> <?php echo e($item); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
                <a href="<?php echo e(route('tutor.apply')); ?>" class="sana-btn sana-btn--yellow" style="margin-top:18px;width:100%;justify-content:center">
                    <i class="fas fa-arrow-left"></i> <?php echo e($tp('cta_apply')); ?>

                </a>
            </article>
        </div>
    </div>
</section>

<section class="sana-section">
    <div class="sana-container">
        <article id="references" class="sana-policy-refs sana-reveal">
            <h2><i class="fas fa-book-open"></i> <?php echo e($tp('refs_title')); ?></h2>
            <ol>
                <?php $__currentLoopData = $tp('refs'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ref): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($ref); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ol>
        </article>
    </div>
</section>

<section class="sana-section">
    <div class="sana-container">
        <div class="sana-legal-related sana-reveal">
            <div class="sana-head sana-head--center" style="margin-bottom:28px">
                <h2 class="sana-head__title">روابط ذات صلة</h2>
                <span class="sana-head__line"></span>
            </div>
            <div class="sana-legal-related__grid">
                <?php $__currentLoopData = $relatedLinks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route($link['route'])); ?>" class="sana-legal-related-card">
                    <span class="sana-legal-related-card__icon"><i class="fas fa-<?php echo e($link['icon']); ?>"></i></span>
                    <strong><?php echo e($link['label']); ?></strong>
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
            <h2><?php echo e($tp('cta_title')); ?></h2>
            <p><?php echo e($tp('cta_sub')); ?></p>
            <div class="sana-sub-final__actions">
                <a href="<?php echo e(route('tutor.apply')); ?>" class="sana-btn sana-btn--yellow">
                    <i class="fas fa-chalkboard-teacher"></i> <?php echo e($tp('cta_apply')); ?>

                </a>
                <a href="<?php echo e(route('public.contact')); ?>" class="sana-btn sana-btn--ghost-light">
                    <i class="fas fa-envelope"></i> <?php echo e($tp('cta_contact')); ?>

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
<?php /**PATH C:\xampp\htdocs\sana\resources\views\public\teacher-policy.blade.php ENDPATH**/ ?>