<?php
    $tr = $tr ?? fn (string $key) => str_replace(':brand', config('app.name'), __('sana_home.'.$key));
    $courses = $courses ?? collect();
    $layout = $layout ?? 'rail';
    $emptyText = $emptyText ?? $tr('discover.empty_courses');
?>
<?php if($courses->isNotEmpty() || ($showSkeleton ?? false)): ?>
<section class="home-section <?php echo e($alt ?? false ? 'home-section--alt' : ''); ?>" <?php if(!empty($id)): ?> id="<?php echo e($id); ?>" <?php endif; ?> aria-labelledby="<?php echo e($headingId ?? 'section-'.Str::slug($title)); ?>">
    <div class="edu-container">
        <div class="home-section__head reveal">
            <div>
                <?php if(!empty($badge)): ?><span class="edu-sub-title"><?php echo e($badge); ?></span><?php endif; ?>
                <h2 id="<?php echo e($headingId ?? 'section-'.Str::slug($title)); ?>" class="home-section__title"><?php echo e($title); ?></h2>
                <?php if(!empty($subtitle)): ?><p class="home-section__sub"><?php echo e($subtitle); ?></p><?php endif; ?>
            </div>
            <?php if(!empty($viewAllUrl)): ?>
                <a href="<?php echo e($viewAllUrl); ?>" class="home-section__link"><?php echo e($viewAllLabel ?? $tr('courses.view_all')); ?> <i class="fas fa-arrow-left" aria-hidden="true"></i></a>
            <?php endif; ?>
        </div>
        <?php if($courses->isNotEmpty()): ?>
            <div class="<?php echo e($layout === 'grid' ? 'home-grid' : 'home-rail'); ?>" role="list">
                <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php echo $__env->make('landing.eduvalt.partials.home-course-card', [
                        'course' => $course,
                        'savedCourseIds' => $savedCourseIds ?? [],
                        'courseProgressMap' => $courseProgressMap ?? [],
                    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php else: ?>
            <div class="home-empty reveal" role="status"><?php echo e($emptyText); ?></div>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\sana\resources\views/landing/eduvalt/partials/home-section-courses.blade.php ENDPATH**/ ?>