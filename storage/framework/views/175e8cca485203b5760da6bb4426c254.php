<?php
    $brand = config('app.name', 'Sana');
    $tr = fn (string $key) => str_replace(':brand', $brand, __('sana_home.'.$key));
    $user = $profile->user;
    $photo = $profile->photo_url;
    $subjects = $profile->public_subject_labels ?? [];
    $grades = $profile->public_grade_labels ?? [];
    $curricula = $profile->public_curriculum_labels ?? [];
    $stages = $profile->public_stage_labels ?? [];
    $sessions = $profile->public_session_labels ?? [];
    $demoVideo = $profile->public_demo_video ?? null;
    $bookUrl = $profile->public_book_url ?? route('register');
    $experienceItems = $profile->experience_list ?? [];
    $coursesCount = $courses->count();
    $instrPageTitle = ($user->name ?? __('public.instructor_fallback')).' — '.$brand;
    $instrPageDesc = Str::limit(strip_tags($profile->bio ?? $profile->headline ?? ''), 160);
    $headline = $profile->headline ?: __('public.instructor_fallback');
    $isBookable = ! empty($profile->is_bookable);
    $allPills = array_merge($subjects, $grades, $curricula, array_slice($stages, 0, 2));
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
    <title><?php echo e($instrPageTitle); ?></title>
    <meta name="description" content="<?php echo e($instrPageDesc); ?>">
    <meta name="theme-color" content="#5B21B6">
    <link rel="canonical" href="<?php echo e(route('public.instructors.show', $user)); ?>">
    <?php echo $__env->make('partials.favicon-links', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('partials.seo-jsonld', ['jsonldType' => 'instructor', 'profile' => $profile], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@800;900&family=Tajawal:wght@500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <?php echo $__env->make('partials.rtl-base', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('landing.sana.theme', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('landing.sana.courses-catalog-theme', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('landing.sana.instructors-catalog-theme', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('landing.sana.instructor-show-theme', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</head>
<body class="sana-home sana-courses-page sana-instructors-page sana-instructor-show-page">

<div id="sana-scroll-progress"></div>
<?php echo $__env->make('landing.sana.navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<main class="sana-is-page">

    <section class="sana-is-hero">
        <div class="sana-is-hero__dots"></div>
        <div class="sana-container sana-is-hero__inner">
            <nav class="sana-is-breadcrumb sana-reveal" aria-label="مسار التنقل">
                <a href="<?php echo e(route('home')); ?>"><?php echo e(__('public.home')); ?></a>
                <i class="fas fa-chevron-left" style="font-size:0.55rem;opacity:0.5"></i>
                <a href="<?php echo e(route('public.instructors.index')); ?>"><?php echo e(__('public.instructors_page_title')); ?></a>
                <i class="fas fa-chevron-left" style="font-size:0.55rem;opacity:0.5"></i>
                <span><?php echo e($user->name); ?></span>
            </nav>

            <div class="sana-is-hero__grid">
                <div class="sana-reveal">
                    <span class="sana-is-hero__eyebrow"><i class="fas fa-chalkboard-user"></i> <?php echo e($tr('instructors.badge')); ?></span>
                    <h1 class="sana-is-hero__title"><?php echo e($user->name); ?></h1>
                    <p class="sana-is-hero__headline"><?php echo e($headline); ?></p>

                    <?php if($profile->bio && mb_strlen(strip_tags($profile->bio)) <= 220): ?>
                        <p class="sana-is-hero__bio"><?php echo e($profile->bio); ?></p>
                    <?php endif; ?>

                    <div class="sana-is-hero__pills">
                        <?php if($isBookable): ?>
                            <span class="sana-is-pill sana-is-pill--book"><i class="fas fa-calendar-check"></i> <?php echo e(__('public.instructor_stat_bookable')); ?></span>
                        <?php endif; ?>
                        <?php if(!empty($profile->public_years_experience)): ?>
                            <span class="sana-is-pill sana-is-pill--gold"><i class="fas fa-briefcase"></i> <?php echo e(__('public.instructor_experience_years', ['years' => $profile->public_years_experience])); ?></span>
                        <?php endif; ?>
                        <?php if($coursesCount > 0): ?>
                            <span class="sana-is-pill"><i class="fas fa-book-open"></i> <?php echo e($coursesCount); ?> <?php echo e($tr('instructors.courses')); ?></span>
                        <?php endif; ?>
                        <?php $__currentLoopData = array_slice($allPills, 0, 5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <span class="sana-is-pill"><?php echo e($label); ?></span>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>

                    <?php if(count($sessions) > 0): ?>
                    <div class="sana-is-hero__meta">
                        <span><i class="fas fa-clock"></i> <?php echo e(implode(' · ', $sessions)); ?></span>
                    </div>
                    <?php endif; ?>

                    <div class="sana-is-hero__actions">
                        <?php if($isBookable): ?>
                            <a href="<?php echo e($bookUrl); ?>" class="sana-btn sana-btn--yellow">
                                <i class="fas fa-calendar-plus"></i> <?php echo e(__('public.instructor_book_with')); ?>

                            </a>
                        <?php endif; ?>
                        <a href="<?php echo e(route('public.instructors.index')); ?>" class="sana-btn sana-btn--white-outline sana-btn--sm">
                            <i class="fas fa-arrow-right"></i> <?php echo e(__('public.all_instructors_link')); ?>

                        </a>
                    </div>
                </div>

                <div class="sana-is-hero__photo sana-reveal">
                    <div class="sana-is-hero__ring">
                        <?php if($photo): ?>
                            <img src="<?php echo e($photo); ?>" alt="<?php echo e($user->name); ?>" loading="eager">
                        <?php else: ?>
                            <span class="av"><?php echo e(mb_substr($user->name ?? 'م', 0, 1)); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="sana-section sana-section--white">
        <div class="sana-container">
            <div class="sana-is-layout">
                <div>
                    <?php if($profile->bio && mb_strlen(strip_tags($profile->bio)) > 220): ?>
                    <div class="sana-is-panel sana-reveal">
                        <h2 class="sana-is-panel__title"><i class="fas fa-user"></i> نبذة عن المعلّم</h2>
                        <p class="sana-is-exp-text"><?php echo e($profile->bio); ?></p>
                    </div>
                    <?php endif; ?>

                    <?php if($demoVideo): ?>
                    <div class="sana-is-panel sana-reveal">
                        <h2 class="sana-is-panel__title"><i class="fas fa-circle-play"></i> <?php echo e(__('public.instructor_demo_video')); ?></h2>
                        <?php if(!empty($demoVideo['title'])): ?>
                            <p style="font-size:0.85rem;font-weight:700;color:var(--muted);margin:0 0 14px"><?php echo e($demoVideo['title']); ?></p>
                        <?php endif; ?>
                        <div class="sana-is-video">
                            <?php if(!empty($demoVideo['embed'])): ?>
                                <iframe src="<?php echo e($demoVideo['embed']); ?>" title="<?php echo e(__('public.instructor_demo_video')); ?>" allowfullscreen loading="lazy"></iframe>
                            <?php elseif(!empty($demoVideo['direct'])): ?>
                                <video src="<?php echo e($demoVideo['direct']); ?>" controls playsinline preload="metadata"></video>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if($profile->experience || count($experienceItems) > 0): ?>
                    <div class="sana-is-panel sana-reveal">
                        <h2 class="sana-is-panel__title"><i class="fas fa-briefcase"></i> <?php echo e(__('public.experience')); ?></h2>
                        <?php if(count($experienceItems) > 0): ?>
                            <ul class="sana-is-exp-list">
                                <?php $__currentLoopData = $experienceItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><i class="fas fa-check-circle"></i><span><?php echo e($item); ?></span></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        <?php else: ?>
                            <p class="sana-is-exp-text"><?php echo e($profile->experience); ?></p>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                    <?php if($coursesCount > 0): ?>
                    <div class="sana-is-panel sana-reveal">
                        <h2 class="sana-is-panel__title"><i class="fas fa-book-open"></i> <?php echo e(__('public.instructor_courses')); ?></h2>
                        <div class="sana-is-courses-grid">
                            <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php echo $__env->make('landing.sana.partials.course-card', ['course' => $c, 'noReveal' => true], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <aside class="sana-is-sidebar sana-reveal">
                    <?php if($isBookable): ?>
                        <p class="sana-is-sidebar__label"><?php echo e(__('public.instructor_booking_label')); ?></p>
                        <h2 class="sana-is-sidebar__heading"><?php echo e($user->name); ?></h2>
                        <p class="sana-is-sidebar__desc"><?php echo e($profile->public_booking_label ?? __('public.instructor_booking_via_packages')); ?></p>

                        <ul class="sana-is-sidebar__list">
                            <?php if(count($subjects) > 0): ?>
                            <li><span><?php echo e(__('public.instructor_subjects_label')); ?></span><span><?php echo e(implode('، ', array_slice($subjects, 0, 4))); ?></span></li>
                            <?php endif; ?>
                            <?php if(count($grades) > 0): ?>
                            <li><span><?php echo e(__('public.instructor_grades_label')); ?></span><span><?php echo e(implode('، ', array_slice($grades, 0, 4))); ?></span></li>
                            <?php endif; ?>
                            <?php if(count($curricula) > 0): ?>
                            <li><span><?php echo e(__('public.instructor_curricula_label')); ?></span><span><?php echo e(implode('، ', array_slice($curricula, 0, 3))); ?></span></li>
                            <?php endif; ?>
                            <?php if(count($sessions) > 0): ?>
                            <li><span><?php echo e(__('public.instructor_booking_label')); ?></span><span><?php echo e(implode('، ', $sessions)); ?></span></li>
                            <?php endif; ?>
                            <?php if($coursesCount > 0): ?>
                            <li><span><?php echo e($tr('instructors.courses')); ?></span><span><?php echo e($coursesCount); ?></span></li>
                            <?php endif; ?>
                        </ul>

                        <div class="sana-is-sidebar__actions">
                            <a href="<?php echo e($bookUrl); ?>" class="sana-btn sana-btn--yellow">
                                <i class="fas fa-calendar-plus"></i> <?php echo e(__('public.instructor_book_with')); ?>

                            </a>
                            <a href="<?php echo e(route('public.pricing')); ?>" class="sana-btn sana-btn--purple-outline">
                                <i class="fas fa-tags"></i> <?php echo e(__('public.audience_student_link_pricing')); ?>

                            </a>
                        </div>
                    <?php else: ?>
                        <p class="sana-is-sidebar__label"><?php echo e(__('public.instructors_page_title')); ?></p>
                        <h2 class="sana-is-sidebar__heading"><?php echo e(__('public.instructors_coming_soon_title')); ?></h2>
                        <p class="sana-is-sidebar__desc"><?php echo e(__('public.instructors_coming_soon_sub')); ?></p>
                        <div class="sana-is-sidebar__actions">
                            <?php echo $__env->make('landing.sana.partials.site-cta-buttons', ['size' => 'sm', 'class' => 'sana-site-cta--stack'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                            <a href="<?php echo e(route('public.contact')); ?>" class="sana-btn sana-btn--purple-outline">
                                <i class="fas fa-envelope"></i> <?php echo e(__('public.contact_page_title')); ?>

                            </a>
                        </div>
                    <?php endif; ?>

                    <a href="<?php echo e(route('public.instructors.index')); ?>" class="sana-btn sana-btn--ghost-muted" style="margin-top:14px">
                        <i class="fas fa-arrow-right"></i> <?php echo e(__('public.all_instructors_link')); ?>

                    </a>
                </aside>
            </div>
        </div>
    </section>

</main>

<?php if($isBookable): ?>
<div class="sana-is-mobile-bar is-visible" aria-hidden="false">
    <a href="<?php echo e($bookUrl); ?>" class="sana-btn sana-btn--yellow">
        <i class="fas fa-calendar-plus"></i> <?php echo e(__('public.instructor_book_with')); ?>

    </a>
</div>
<?php endif; ?>

<?php echo $__env->make('landing.sana.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('landing.sana.scripts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('partials.pwa-service-worker', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\sana\resources\views/instructors/show.blade.php ENDPATH**/ ?>