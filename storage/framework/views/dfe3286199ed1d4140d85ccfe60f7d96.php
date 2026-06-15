<?php
    $brand = config('app.name', 'Sana');
    $tr = fn (string $key) => str_replace(':brand', $brand, __('sana_home.'.$key));
    $pub = fn (string $key) => str_replace(':brand', $brand, __('public.'.$key));
    $studentTopicIcons = ['user-plus', 'calendar-check', 'book-open', 'certificate', 'headset'];
    $teacherTopicIcons = ['file-signature', 'shield-halved', 'chalkboard-user', 'wallet', 'life-ring'];
    $hasPublishedCourses = $hasPublishedCourses ?? \App\Support\PublicCourseCatalog::hasPublicCourses();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
    <title><?php echo e(__('public.help_page_title')); ?> — <?php echo e($brand); ?></title>
    <meta name="description" content="<?php echo e($pub('help_meta_description')); ?>">
    <meta name="theme-color" content="#5B21B6">
    <link rel="canonical" href="<?php echo e(url('/help')); ?>">
    <?php echo $__env->make('partials.favicon-links', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@800;900&family=Tajawal:wght@500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <?php echo $__env->make('partials.rtl-base', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('landing.sana.theme', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('landing.sana.courses-catalog-theme', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('landing.sana.subpages-theme', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</head>
<body class="sana-home sana-courses-page" x-data="{ audience: 'student' }">

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
                    <span><?php echo e(__('public.help_page_title')); ?></span>
                </nav>
                <span class="sana-sub-hero__eyebrow"><i class="fas fa-life-ring"></i> <?php echo e(__('public.help_hero_badge')); ?></span>
                <h1 class="sana-sub-hero__title">
                    <?php echo e(__('public.help_page_title')); ?>

                    <span class="hl"><?php echo e($brand); ?></span>
                </h1>
                <p class="sana-sub-hero__sub"><?php echo e($pub('help_hero_sub')); ?></p>
                <div class="sana-sub-hero__actions">
                    <a href="<?php echo e(route('public.faq')); ?>" class="sana-btn sana-btn--yellow">
                        <i class="fas fa-circle-question"></i> <?php echo e(__('public.faq_page_title')); ?>

                    </a>
                    <a href="<?php echo e(route('public.contact')); ?>" class="sana-btn sana-btn--white-outline">
                        <i class="fas fa-envelope"></i> <?php echo e(__('public.contact_page_title')); ?>

                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="sana-section sana-section--soft">
    <div class="sana-container">
        <div class="sana-help-audience sana-reveal">
            <div class="sana-help-audience__toggle" role="tablist">
                <button type="button" role="tab" :class="{ 'is-active': audience === 'student' }" @click="audience = 'student'">
                    <i class="fas fa-user-graduate"></i> <?php echo e(__('public.help_audience_student')); ?>

                </button>
                <button type="button" role="tab" :class="{ 'is-active': audience === 'teacher' }" @click="audience = 'teacher'">
                    <i class="fas fa-chalkboard-teacher"></i> <?php echo e(__('public.help_audience_teacher')); ?>

                </button>
            </div>

            <p class="sana-help-audience__intro" x-show="audience === 'student'" x-cloak><?php echo e($pub('help_family_intro')); ?></p>
            <p class="sana-help-audience__intro" x-show="audience === 'teacher'" x-cloak><?php echo e($pub('help_teacher_intro')); ?></p>

            
            <div x-show="audience === 'student'" x-cloak>
                <h2 class="sana-help-section-title sana-help-section-title--first"><?php echo e(__('public.help_start_title')); ?></h2>
                <div class="sana-help-cards">
                    <a href="<?php echo e(route('public.faq')); ?>" class="sana-help-card">
                        <span class="sana-help-card__icon"><i class="fas fa-circle-question"></i></span>
                        <strong><?php echo e(__('public.help_card_faq_title')); ?></strong>
                        <p><?php echo e(__('public.help_card_faq_desc')); ?></p>
                    </a>
                    <?php if($hasPublishedCourses): ?>
                    <a href="<?php echo e(route('public.courses')); ?>" class="sana-help-card">
                        <span class="sana-help-card__icon"><i class="fas fa-book-open"></i></span>
                        <strong><?php echo e(__('public.help_card_courses_title')); ?></strong>
                        <p><?php echo e(__('public.help_card_courses_desc')); ?></p>
                    </a>
                    <?php else: ?>
                    <a href="<?php echo e(route('public.instructors.index')); ?>" class="sana-help-card">
                        <span class="sana-help-card__icon"><i class="fas fa-chalkboard-user"></i></span>
                        <strong><?php echo e(__('public.help_card_instructors_title')); ?></strong>
                        <p><?php echo e(__('public.help_card_instructors_desc')); ?></p>
                    </a>
                    <?php endif; ?>
                    <a href="<?php echo e(route('public.contact')); ?>" class="sana-help-card">
                        <span class="sana-help-card__icon"><i class="fas fa-envelope"></i></span>
                        <strong><?php echo e(__('public.help_card_contact_title')); ?></strong>
                        <p><?php echo e(__('public.help_card_contact_desc')); ?></p>
                    </a>
                </div>

                <h2 class="sana-help-section-title"><?php echo e(__('public.help_topics_title')); ?></h2>
                <div class="sana-help-topics">
                    <?php $__currentLoopData = range(1, 5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <article class="sana-help-topic">
                        <span class="sana-help-topic__icon"><i class="fas fa-<?php echo e($studentTopicIcons[$i - 1]); ?>"></i></span>
                        <div>
                            <h3><?php echo e(__('public.help_topic_'.$i.'_title')); ?></h3>
                            <p><?php echo e(__('public.help_topic_'.$i.'_desc')); ?></p>
                        </div>
                    </article>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                <h2 class="sana-help-section-title"><?php echo e(__('public.help_steps_title')); ?></h2>
                <ol class="sana-help-steps">
                    <?php $__currentLoopData = range(1, 4); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li>
                        <strong><?php echo e(__('public.help_step_'.$step.'_title')); ?></strong>
                        <span><?php echo e(__('public.help_step_'.$step.'_desc')); ?></span>
                    </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ol>
            </div>

            
            <div x-show="audience === 'teacher'" x-cloak>
                <h2 class="sana-help-section-title sana-help-section-title--first"><?php echo e(__('public.help_start_title')); ?></h2>
                <div class="sana-help-cards">
                    <a href="<?php echo e(route('tutor.apply')); ?>" class="sana-help-card">
                        <span class="sana-help-card__icon"><i class="fas fa-file-signature"></i></span>
                        <strong><?php echo e(__('public.help_card_apply_title')); ?></strong>
                        <p><?php echo e(__('public.help_card_apply_desc')); ?></p>
                    </a>
                    <a href="<?php echo e(route('tutor.policy')); ?>" class="sana-help-card">
                        <span class="sana-help-card__icon"><i class="fas fa-shield-halved"></i></span>
                        <strong><?php echo e(__('public.teacher_policy')); ?></strong>
                        <p><?php echo e(__('public.audience_teacher_desc')); ?></p>
                    </a>
                    <a href="<?php echo e(route('public.contact')); ?>" class="sana-help-card">
                        <span class="sana-help-card__icon"><i class="fas fa-envelope"></i></span>
                        <strong><?php echo e(__('public.help_card_contact_title')); ?></strong>
                        <p><?php echo e(__('public.help_card_contact_desc')); ?></p>
                    </a>
                </div>

                <h2 class="sana-help-section-title"><?php echo e(__('public.help_topics_title')); ?></h2>
                <div class="sana-help-topics">
                    <?php $__currentLoopData = range(1, 5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <article class="sana-help-topic">
                        <span class="sana-help-topic__icon sana-help-topic__icon--teacher"><i class="fas fa-<?php echo e($teacherTopicIcons[$i - 1]); ?>"></i></span>
                        <div>
                            <h3><?php echo e(__('public.help_teacher_topic_'.$i.'_title')); ?></h3>
                            <p><?php echo e(__('public.help_teacher_topic_'.$i.'_desc')); ?></p>
                        </div>
                    </article>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                <h2 class="sana-help-section-title"><?php echo e(__('public.help_steps_title')); ?></h2>
                <ol class="sana-help-steps">
                    <?php $__currentLoopData = range(1, 4); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li>
                        <strong><?php echo e(__('public.help_teacher_step_'.$step.'_title')); ?></strong>
                        <span><?php echo e(__('public.help_teacher_step_'.$step.'_desc')); ?></span>
                    </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ol>
            </div>
        </div>
    </div>
</section>

<section class="sana-sub-final">
    <div class="sana-container sana-reveal">
        <div class="sana-sub-final__box">
            <h2><?php echo e(__('public.help_cta_title')); ?></h2>
            <p><?php echo e(__('public.help_cta_desc')); ?></p>
            <div class="sana-sub-final__actions">
                <a href="<?php echo e(route('public.contact')); ?>" class="sana-btn sana-btn--yellow">
                    <i class="fas fa-paper-plane"></i> <?php echo e(__('public.help_cta_btn')); ?>

                </a>
                <a href="<?php echo e(route('public.faq')); ?>" class="sana-btn sana-btn--ghost-light">
                    <i class="fas fa-circle-question"></i> <?php echo e(__('public.faq_page_title')); ?>

                </a>
            </div>
        </div>
    </div>
</section>

</main>

<?php echo $__env->make('landing.sana.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('landing.sana.scripts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('partials.pwa-service-worker', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>[x-cloak]{display:none!important}</style>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\sana\resources\views\public\help.blade.php ENDPATH**/ ?>