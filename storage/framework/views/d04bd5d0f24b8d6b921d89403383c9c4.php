<?php
    $brand = config('app.name', 'Sana');
    $tr = fn (string $key) => str_replace(':brand', $brand, __('sana_home.'.$key));
    $pub = fn (string $key) => str_replace(':brand', $brand, __('public.'.$key));
    $cta = \App\Support\PublicSiteCta::payload();
    $hasPublishedCourses = $hasPublishedCourses ?? \App\Support\PublicCourseCatalog::hasPublicCourses();
    $hasPublicInstructors = $hasPublicInstructors ?? \App\Support\PublicInstructorCatalog::hasPublicInstructors();
    $steps = [
        ['icon' => 'fa-clipboard-check', 'title' => __('public.how_it_works_step_1_title'), 'desc' => __('public.how_it_works_step_1_desc')],
        ['icon' => 'fa-user-check', 'title' => __('public.how_it_works_step_2_title'), 'desc' => __('public.how_it_works_step_2_desc')],
        ['icon' => 'fa-video', 'title' => __('public.how_it_works_step_3_title'), 'desc' => __('public.how_it_works_step_3_desc')],
        ['icon' => 'fa-chart-line', 'title' => __('public.how_it_works_step_4_title'), 'desc' => __('public.how_it_works_step_4_desc')],
    ];
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
    <title><?php echo e(__('public.how_it_works_page_title')); ?> — <?php echo e($brand); ?></title>
    <meta name="description" content="<?php echo e($pub('how_it_works_meta')); ?>">
    <meta name="theme-color" content="#5B21B6">
    <link rel="canonical" href="<?php echo e(url('/how-it-works')); ?>">
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
                    <span><?php echo e(__('public.how_it_works_page_title')); ?></span>
                </nav>
                <span class="sana-sub-hero__eyebrow"><i class="fas fa-route"></i> <?php echo e(__('public.how_it_works_hero_badge')); ?></span>
                <h1 class="sana-sub-hero__title">
                    <?php echo e(__('public.how_it_works_page_title')); ?>

                    <span class="hl"><?php echo e($brand); ?></span>
                </h1>
                <p class="sana-sub-hero__sub"><?php echo e($pub('how_it_works_hero_sub')); ?></p>
                <div class="sana-sub-hero__actions">
                    <?php echo $__env->make('landing.sana.partials.site-cta-buttons', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="sana-section sana-section--soft">
    <div class="sana-container">
        <div class="sana-journey-m sana-journey-m--detailed sana-reveal">
            <div class="sana-journey-m__line" aria-hidden="true"></div>
            <?php $__currentLoopData = $steps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <article class="sana-journey-m__step sana-journey-m__step--detail">
                <div class="sana-journey-m__icon"><i class="fas <?php echo e($step['icon']); ?>"></i></div>
                <div>
                    <span class="sana-journey-m__num"><?php echo e($i + 1); ?></span>
                    <strong><?php echo e($step['title']); ?></strong>
                    <p><?php echo e($step['desc']); ?></p>
                </div>
            </article>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>

<section class="sana-section">
    <div class="sana-container">
        <div class="sana-head sana-head--center sana-reveal" style="margin-bottom:28px">
            <h2 class="sana-head__title"><?php echo e(__('public.how_it_works_packages_title')); ?></h2>
            <span class="sana-head__line"></span>
            <p class="sana-head__sub"><?php echo e(__('public.how_it_works_packages_desc')); ?></p>
        </div>
        <div class="sana-reveal" style="display:flex;flex-wrap:wrap;gap:12px;justify-content:center;margin-bottom:16px">
            <a href="<?php echo e(route('public.pricing')); ?>" class="sana-btn sana-btn--yellow">
                <i class="fas fa-layer-group"></i> <?php echo e(__('public.how_it_works_packages_cta')); ?>

            </a>
            <?php if($hasPublishedCourses): ?>
            <a href="<?php echo e(route('public.courses')); ?>" class="sana-btn sana-btn--outline-purple">
                <i class="fas fa-book-open"></i> <?php echo e($tr('hero.cta_courses')); ?>

            </a>
            <?php endif; ?>
            <?php if($hasPublicInstructors): ?>
            <a href="<?php echo e(route('public.instructors.index')); ?>" class="sana-btn sana-btn--outline-purple">
                <i class="fas fa-chalkboard-user"></i> <?php echo e(__('public.courses_launch_cta_tutors')); ?>

            </a>
            <?php endif; ?>
        </div>
        <?php if (! ($hasPublicInstructors)): ?>
        <p class="sana-reveal" style="text-align:center;color:var(--muted);font-size:0.88rem;max-width:48ch;margin:0 auto">
            <i class="fas fa-info-circle"></i> <?php echo e(__('public.how_it_works_teachers_note')); ?>

        </p>
        <?php endif; ?>
    </div>
</section>

<section class="sana-section sana-section--soft">
    <div class="sana-container sana-reveal" style="text-align:center">
        <?php echo $__env->make('landing.sana.partials.site-cta-buttons', ['class' => 'sana-site-cta--center'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <p style="margin-top:20px;font-size:0.88rem;color:var(--muted)">
            <a href="<?php echo e(route('public.faq')); ?>"><?php echo e(__('public.how_it_works_faq_link')); ?></a>
            ·
            <a href="<?php echo e(route('public.help')); ?>"><?php echo e(__('public.how_it_works_help_link')); ?></a>
        </p>
    </div>
</section>

</main>

<?php echo $__env->make('landing.sana.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('landing.sana.scripts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('partials.pwa-service-worker', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
.sana-journey-m--detailed .sana-journey-m__step--detail { align-items: flex-start; text-align: start; gap: 16px; }
.sana-journey-m--detailed .sana-journey-m__step--detail p { margin: 6px 0 0; color: var(--muted); font-size: 0.9rem; line-height: 1.65; max-width: 42ch; }
.sana-journey-m--detailed .sana-journey-m__step--detail strong { display: block; font-size: 1.05rem; }
.sana-journey-m__num { display: inline-block; font-size: 0.72rem; font-weight: 800; color: var(--p); margin-bottom: 4px; }
.sana-site-cta--center { justify-content: center; }
</style>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\sana\resources\views\public\how-it-works.blade.php ENDPATH**/ ?>