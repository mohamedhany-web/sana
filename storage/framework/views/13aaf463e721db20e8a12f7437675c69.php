<?php echo $__env->make('landing.eduvalt.partials.home-discover', compact('tr', 'homeStats', 'courseCategories'), array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php echo $__env->make('landing.eduvalt.partials.home-section-courses', [
    'tr' => $tr,
    'id' => 'popular-courses',
    'headingId' => 'home-popular-title',
    'badge' => $tr('courses.badge'),
    'title' => $tr('sections.popular'),
    'subtitle' => $tr('sections.popular_sub'),
    'courses' => $popularCourses ?? collect(),
    'viewAllUrl' => route('public.courses'),
    'layout' => 'rail',
    'savedCourseIds' => $savedCourseIds ?? [],
    'courseProgressMap' => $courseProgressMap ?? [],
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php if(isset($landingPaths) && $landingPaths->isNotEmpty()): ?>
<section class="home-section home-section--alt" id="learning-paths" aria-labelledby="home-paths-title">
    <div class="edu-container">
        <div class="home-section__head reveal">
            <div>
                <span class="edu-sub-title"><?php echo e($tr('paths.badge')); ?></span>
                <h2 id="home-paths-title" class="home-section__title"><?php echo e($tr('sections.paths')); ?></h2>
                <p class="home-section__sub"><?php echo e($tr('sections.paths_sub')); ?></p>
            </div>
            <a href="<?php echo e(route('public.courses')); ?>" class="home-section__link"><?php echo e($tr('paths.view_all')); ?> <i class="fas fa-arrow-left" aria-hidden="true"></i></a>
        </div>
        <div class="home-rail" role="list">
            <?php $__currentLoopData = $landingPaths->take(8); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $path): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <article class="home-path-card reveal" role="listitem">
                <a href="<?php echo e(route('public.courses')); ?>" class="block">
                    <?php if($path->image_url ?? null): ?>
                        <img src="<?php echo e($path->image_url); ?>" alt="" class="home-path-card__img" loading="lazy" width="320" height="180">
                    <?php else: ?>
                        <div class="home-path-card__img flex items-center justify-center bg-gradient-to-br from-blue-50 to-violet-50 text-4xl text-[var(--edu-primary)]" style="aspect-ratio:16/9">
                            <i class="fas fa-route" aria-hidden="true"></i>
                        </div>
                    <?php endif; ?>
                    <div class="home-path-card__body">
                        <h3 class="home-path-card__title"><?php echo e($path->name); ?></h3>
                        <p class="home-path-card__meta">
                            <?php echo e((int) ($path->courses_count ?? 0)); ?> <?php echo e($tr('paths.courses')); ?>

                            <?php if(($path->price ?? 0) > 0): ?>
                                · <?php echo e(number_format((float) $path->price, 0)); ?> <?php echo e(__('public.currency')); ?>

                            <?php endif; ?>
                        </p>
                    </div>
                </a>
            </article>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="home-section" id="instructors" aria-labelledby="home-instructors-title">
    <div class="edu-container">
        <div class="home-section__head reveal">
            <div>
                <span class="edu-sub-title"><?php echo e($tr('instructors.badge')); ?></span>
                <h2 id="home-instructors-title" class="home-section__title"><?php echo e($tr('sections.instructors')); ?></h2>
                <p class="home-section__sub"><?php echo e($tr('sections.instructors_sub')); ?></p>
            </div>
            <a href="<?php echo e(route('public.instructors.index')); ?>" class="home-section__link"><?php echo e($tr('instructors.view_all')); ?> <i class="fas fa-arrow-left" aria-hidden="true"></i></a>
        </div>
        <div class="home-rail" role="list">
            <?php $__empty_1 = true; $__currentLoopData = ($homeInstructors ?? collect())->take(8); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php
                $name = $p->user->name ?? '';
                $photo = $p->photo_path ? $p->photo_url : 'https://images.unsplash.com/photo-1560250097-0b93528c311a?w=400&auto=format&fit=crop&q=80';
            ?>
            <article class="home-instructor-card reveal" role="listitem">
                <a href="<?php echo e(route('public.instructors.show', $p->user)); ?>" class="block no-underline text-inherit">
                    <img src="<?php echo e($photo); ?>" alt="" class="home-instructor-card__photo" width="88" height="88" loading="lazy">
                    <h3 class="home-instructor-card__name"><?php echo e($name); ?></h3>
                    <?php if($p->headline): ?><p class="home-instructor-card__headline"><?php echo e(Str::limit($p->headline, 48)); ?></p><?php endif; ?>
                    <p class="home-instructor-card__stats"><?php echo e((int) ($p->courses_count ?? 0)); ?> <?php echo e($tr('instructors.courses')); ?></p>
                </a>
            </article>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="home-empty reveal w-full"><?php echo e(__('public.no_instructors')); ?></div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php echo $__env->make('landing.eduvalt.partials.home-section-courses', [
    'tr' => $tr,
    'id' => 'new-courses',
    'headingId' => 'home-new-title',
    'title' => $tr('sections.new'),
    'subtitle' => $tr('sections.new_sub'),
    'courses' => $newCourses ?? collect(),
    'viewAllUrl' => route('public.courses'),
    'alt' => true,
    'layout' => 'rail',
    'savedCourseIds' => $savedCourseIds ?? [],
    'courseProgressMap' => $courseProgressMap ?? [],
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php echo $__env->make('landing.eduvalt.partials.home-section-courses', [
    'tr' => $tr,
    'id' => 'most-enrolled',
    'headingId' => 'home-enrolled-title',
    'title' => $tr('sections.enrolled'),
    'subtitle' => $tr('sections.enrolled_sub'),
    'courses' => $mostEnrolledCourses ?? collect(),
    'viewAllUrl' => route('public.courses'),
    'layout' => 'grid',
    'savedCourseIds' => $savedCourseIds ?? [],
    'courseProgressMap' => $courseProgressMap ?? [],
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<section class="home-section home-section--alt" id="categories" aria-labelledby="home-categories-title">
    <div class="edu-container">
        <div class="home-section__head reveal">
            <div>
                <span class="edu-sub-title"><?php echo e($tr('categories.badge')); ?></span>
                <h2 id="home-categories-title" class="home-section__title"><?php echo e($tr('sections.categories')); ?></h2>
                <p class="home-section__sub"><?php echo e($tr('sections.categories_sub')); ?></p>
            </div>
            <a href="<?php echo e(route('public.courses')); ?>" class="home-section__link"><?php echo e($tr('categories.view_all')); ?> <i class="fas fa-arrow-left" aria-hidden="true"></i></a>
        </div>
        <div class="home-grid">
            <?php
                $catThemes = [
                    ['icon' => 'fa-laptop-code', 'color' => config('brand.colors.blue'), 'bg' => config('brand.colors.blue_light')],
                    ['icon' => 'fa-brain', 'color' => config('brand.colors.purple'), 'bg' => config('brand.colors.purple_light')],
                    ['icon' => 'fa-chart-line', 'color' => config('brand.colors.yellow_dark'), 'bg' => config('brand.colors.yellow_light')],
                    ['icon' => 'fa-language', 'color' => config('brand.colors.purple'), 'bg' => config('brand.colors.purple_light')],
                    ['icon' => 'fa-code', 'color' => config('brand.colors.blue_dark'), 'bg' => config('brand.colors.blue_light')],
                    ['icon' => 'fa-palette', 'color' => config('brand.colors.purple_dark'), 'bg' => config('brand.colors.purple_light')],
                ];
            ?>
            <?php $__empty_1 = true; $__currentLoopData = ($courseCategories ?? collect()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ci => $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php $theme = $catThemes[$ci % count($catThemes)]; ?>
            <a href="<?php echo e($cat['url']); ?>" class="home-cat-card reveal" style="--cat-accent:<?php echo e($theme['color']); ?>;--cat-bg:<?php echo e($theme['bg']); ?>">
                <span class="home-cat-card__icon"><i class="fas <?php echo e($theme['icon']); ?>" aria-hidden="true"></i></span>
                <span>
                    <span class="home-cat-card__name"><?php echo e($cat['name']); ?></span>
                    <span class="home-cat-card__count"><?php echo e($cat['count']); ?> <?php echo e($tr('categories.courses')); ?></span>
                </span>
            </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="home-empty"><?php echo e($tr('discover.empty_courses')); ?></div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php if(isset($homeTestimonials) && $homeTestimonials->isNotEmpty()): ?>
<section class="home-section" id="testimonials" aria-labelledby="home-testimonials-title">
    <div class="edu-container">
        <div class="home-section__head reveal">
            <div>
                <span class="edu-sub-title"><?php echo e($tr('testimonials.badge')); ?></span>
                <h2 id="home-testimonials-title" class="home-section__title"><?php echo e($tr('sections.testimonials')); ?></h2>
                <p class="home-section__sub"><?php echo e($tr('sections.testimonials_sub')); ?></p>
            </div>
            <a href="<?php echo e(route('public.testimonials')); ?>" class="home-section__link"><?php echo e($tr('testimonials.view_all')); ?> <i class="fas fa-arrow-left" aria-hidden="true"></i></a>
        </div>
        <div class="home-rail" role="list">
            <?php $__currentLoopData = $homeTestimonials->take(8); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <article class="home-testimonial-card reveal" role="listitem">
                <?php if($tItem->body): ?>
                    <p class="text-sm text-slate-600 leading-7 mb-4">«<?php echo e(Str::limit(strip_tags($tItem->body), 180)); ?>»</p>
                <?php endif; ?>
                <div class="flex items-center gap-3 pt-3 border-t border-slate-100">
                    <?php if($tItem->isImageType() && $tItem->publicImageUrl()): ?>
                        <img src="<?php echo e($tItem->publicImageUrl()); ?>" alt="" class="w-12 h-12 rounded-full object-cover" width="48" height="48" loading="lazy">
                    <?php else: ?>
                        <div class="w-12 h-12 rounded-full bg-[var(--edu-primary)] text-white flex items-center justify-center font-bold"><?php echo e($tItem->author_name ? mb_substr($tItem->author_name, 0, 1) : '؟'); ?></div>
                    <?php endif; ?>
                    <div>
                        <?php if($tItem->author_name): ?><p class="font-bold text-slate-900 text-sm"><?php echo e($tItem->author_name); ?></p><?php endif; ?>
                        <?php if($tItem->role_label): ?><p class="text-xs text-slate-500"><?php echo e($tItem->role_label); ?></p><?php endif; ?>
                    </div>
                </div>
            </article>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if(($homeBadges ?? collect())->isNotEmpty() || ($certificateShowcase ?? collect())->isNotEmpty()): ?>
<section class="home-section home-section--alt" id="certificates" aria-labelledby="home-certs-title">
    <div class="edu-container">
        <div class="home-section__head reveal">
            <div>
                <span class="edu-sub-title"><?php echo e($tr('sections.certificates_badge')); ?></span>
                <h2 id="home-certs-title" class="home-section__title"><?php echo e($tr('sections.certificates')); ?></h2>
                <p class="home-section__sub"><?php echo e($tr('sections.certificates_sub')); ?></p>
            </div>
            <a href="<?php echo e(route('public.certificates')); ?>" class="home-section__link"><?php echo e($tr('sections.certificates_link')); ?> <i class="fas fa-arrow-left" aria-hidden="true"></i></a>
        </div>
        <div class="home-rail" role="list">
            <?php $__currentLoopData = ($homeBadges ?? collect())->take(4); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $badge): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <article class="home-badge-card reveal" role="listitem">
                <div class="home-badge-card__icon"><i class="fas <?php echo e($badge->icon ?: 'fa-award'); ?>" aria-hidden="true"></i></div>
                <p class="font-bold text-slate-900 text-sm mb-1"><?php echo e($badge->name); ?></p>
                <p class="text-xs text-slate-500 line-clamp-2"><?php echo e(Str::limit($badge->description ?? '', 80)); ?></p>
            </article>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php $__currentLoopData = ($certificateShowcase ?? collect())->take(4); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cert): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <article class="home-cert-card reveal" role="listitem">
                <p class="text-xs font-bold text-[var(--edu-primary)] mb-2"><i class="fas fa-certificate" aria-hidden="true"></i> <?php echo e(__('public.certificate') ?? 'شهادة'); ?></p>
                <p class="font-bold text-slate-900 text-sm mb-1"><?php echo e($cert->course?->title ?? $cert->course_name ?? $cert->title); ?></p>
                <p class="text-xs text-slate-500"><?php echo e($cert->user?->name); ?></p>
            </article>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <p class="text-center text-sm text-slate-500 mt-6 reveal">
            <?php echo e(number_format((int) ($homeStats['certificates'] ?? 0))); ?> <?php echo e($tr('sections.certificates_issued')); ?>

        </p>
    </div>
</section>
<?php endif; ?>

<?php echo $__env->make('landing.eduvalt.partials.home-section-courses', [
    'tr' => $tr,
    'id' => 'recommended',
    'headingId' => 'home-recommended-title',
    'title' => auth()->check() && auth()->user()->isStudent() ? $tr('sections.recommended') : $tr('sections.recommended_guest'),
    'subtitle' => auth()->check() && auth()->user()->isStudent() ? $tr('sections.recommended_sub') : $tr('sections.recommended_guest_sub'),
    'courses' => $recommendedCourses ?? collect(),
    'viewAllUrl' => route('public.courses'),
    'layout' => 'rail',
    'savedCourseIds' => $savedCourseIds ?? [],
    'courseProgressMap' => $courseProgressMap ?? [],
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<section class="py-12">
    <div class="edu-container reveal">
        <div class="edu-cta-wrap px-8 py-10 lg:py-12 flex flex-col lg:flex-row items-center justify-between gap-6 text-white rounded-3xl">
            <div class="relative z-10 max-w-lg text-center lg:text-start">
                <h2 class="text-xl lg:text-2xl font-extrabold mb-2 text-white"><?php echo e($tr('cta_banner.title')); ?></h2>
                <p class="text-white/90 text-sm leading-7"><?php echo e($tr('cta_banner.subtitle')); ?></p>
            </div>
            <div class="relative z-10 flex flex-wrap gap-3 justify-center">
                <a href="<?php echo e(route('register')); ?>" class="edu-btn-white text-sm"><?php echo e($tr('cta_banner.btn_parent')); ?></a>
                <a href="<?php echo e(route('public.courses')); ?>" class="edu-btn-ghost-light text-sm"><?php echo e($tr('hero.cta_courses')); ?></a>
            </div>
        </div>
    </div>
</section>
<?php /**PATH C:\xampp\htdocs\sana\resources\views/landing/eduvalt/home.blade.php ENDPATH**/ ?>