<?php
    $thumb = $course->thumbnail
        ? asset('storage/'.str_replace('\\', '/', $course->thumbnail))
        : \App\Support\PublicCourseCatalog::defaultCardImage();
    $inst = $course->instructor->name ?? __('public.instructor_fallback');
    $rating = $course->rating ? number_format((float) $course->rating, 1) : null;
    $enrollments = (int) ($course->enrollments_count ?? $course->students_count ?? 0);
    $hours = (int) ($course->duration_hours ?? 0);
    $mins = (int) ($course->duration_minutes ?? 0);
    if ($hours > 0 || $mins > 0) {
        $durationLabel = trim(($hours > 0 ? $hours.' س ' : '').($mins > 0 ? $mins.' د' : ''));
    } else {
        $durationLabel = max(1, (int) ($course->lessons_count ?? 1)).' '.__('sana_home.courses.lessons');
    }
    $catName = $course->courseCategory->name ?? null;
    $progress = isset($courseProgressMap[(int) $course->id]) ? (int) $courseProgressMap[(int) $course->id] : null;
    $isSaved = in_array((int) $course->id, $savedCourseIds ?? [], true);
    $contactSupport = $course->usesContactSupportPricing();
    $isFree = ! $contactSupport && (($course->is_free ?? false) || ($course->listPriceAmount() <= 0 && $course->effectivePurchasePrice() <= 0));
    $ctaLabel = $progress !== null && $progress > 0
        ? __('sana_home.discover.cta_continue')
        : ($isFree ? __('sana_home.courses.free') : __('sana_home.discover.cta_explore'));
    $ctaClass = $progress !== null && $progress > 0 ? 'home-course-card__cta--ghost' : '';
?>
<article class="home-course-card reveal" role="listitem">
    <div class="home-course-card__media">
        <a href="<?php echo e(route('public.course.show', $course->id)); ?>" tabindex="-1" aria-hidden="true">
            <img src="<?php echo e($thumb); ?>" alt="" width="320" height="200" loading="lazy" decoding="async">
        </a>
        <?php if($catName): ?>
            <span class="home-course-card__tag"><?php echo e($catName); ?></span>
        <?php endif; ?>
        <?php if (isset($component)) { $__componentOriginal1bcdbb4473df18087eeee20994007123 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1bcdbb4473df18087eeee20994007123 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.course-favorite-button','data' => ['courseId' => $course->id,'saved' => $isSaved]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('course-favorite-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['course-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($course->id),'saved' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($isSaved)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal1bcdbb4473df18087eeee20994007123)): ?>
<?php $attributes = $__attributesOriginal1bcdbb4473df18087eeee20994007123; ?>
<?php unset($__attributesOriginal1bcdbb4473df18087eeee20994007123); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal1bcdbb4473df18087eeee20994007123)): ?>
<?php $component = $__componentOriginal1bcdbb4473df18087eeee20994007123; ?>
<?php unset($__componentOriginal1bcdbb4473df18087eeee20994007123); ?>
<?php endif; ?>
    </div>
    <div class="home-course-card__body">
        <div class="home-course-card__meta">
            <span><i class="far fa-clock" aria-hidden="true"></i> <?php echo e($durationLabel); ?></span>
            <?php if($enrollments > 0): ?>
                <span><i class="fas fa-user-group" aria-hidden="true"></i> <?php echo e(number_format($enrollments)); ?></span>
            <?php endif; ?>
        </div>
        <h3 class="home-course-card__title">
            <a href="<?php echo e(route('public.course.show', $course->id)); ?>"><?php echo e($course->title); ?></a>
        </h3>
        <div class="home-course-card__instructor">
            <span class="home-course-card__avatar" aria-hidden="true"><?php echo e(mb_substr($inst, 0, 1)); ?></span>
            <span><?php echo e($inst); ?></span>
        </div>
        <?php if($progress !== null): ?>
            <div class="home-course-card__progress" role="progressbar" aria-valuenow="<?php echo e($progress); ?>" aria-valuemin="0" aria-valuemax="100">
                <div class="home-course-card__progress-label">
                    <span><?php echo e(__('sana_home.discover.progress')); ?></span>
                    <span><?php echo e($progress); ?>%</span>
                </div>
                <div class="home-course-card__progress-bar">
                    <div class="home-course-card__progress-fill" style="width:<?php echo e($progress); ?>%"></div>
                </div>
            </div>
        <?php endif; ?>
        <div class="home-course-card__foot">
            <?php if($rating): ?>
                <span class="home-course-card__rating" aria-label="<?php echo e(__('sana_home.discover.rating', ['value' => $rating])); ?>">
                    <i class="fas fa-star" aria-hidden="true"></i> <?php echo e($rating); ?>

                </span>
            <?php else: ?>
                <span class="home-course-card__rating text-slate-400">—</span>
            <?php endif; ?>
            <a href="<?php echo e(route('public.course.show', $course->id)); ?>" class="home-course-card__cta <?php echo e($ctaClass); ?>"><?php echo e($ctaLabel); ?></a>
        </div>
    </div>
</article>
<?php /**PATH C:\xampp\htdocs\sana\resources\views/landing/eduvalt/partials/home-course-card.blade.php ENDPATH**/ ?>