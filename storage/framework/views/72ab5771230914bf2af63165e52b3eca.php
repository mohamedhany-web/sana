<?php $__env->startSection('title', __('student.my_courses_active_title')); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('dashboard.partials.sanua-theme', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="sanua-dash">

    <header class="sanua-page-head">
        <div>
            <h1 class="sanua-page-head__title"><?php echo e(__('student.my_courses_active_title')); ?></h1>
            <p class="sanua-page-head__sub"><?php echo e(__('student.my_courses_subtitle')); ?></p>
        </div>
        <div class="sanua-page-head__actions">
            <a href="<?php echo e(route('public.courses')); ?>" class="sanua-page-head__btn">
                <i class="fas fa-th-large"></i>
                <?php echo e(__('student.browse_courses')); ?>

            </a>
        </div>
    </header>

    <div class="sanua-stats-row">
        <div class="sanua-stat-pill">
            <span class="sanua-stat-pill__icon sanua-stat-pill__icon--purple" aria-hidden="true">
                <i class="fas fa-book-open"></i>
            </span>
            <div class="sanua-stat-pill__body">
                <strong><?php echo e($stats['total_active']); ?></strong>
                <span><?php echo e(__('student.active_label')); ?></span>
            </div>
        </div>
        <div class="sanua-stat-pill">
            <span class="sanua-stat-pill__icon sanua-stat-pill__icon--green" aria-hidden="true">
                <i class="fas fa-check-circle"></i>
            </span>
            <div class="sanua-stat-pill__body">
                <strong><?php echo e($stats['total_completed']); ?></strong>
                <span><?php echo e(__('student.completed')); ?></span>
            </div>
        </div>
        <div class="sanua-stat-pill">
            <span class="sanua-stat-pill__icon sanua-stat-pill__icon--gold" aria-hidden="true">
                <i class="fas fa-clock"></i>
            </span>
            <div class="sanua-stat-pill__body">
                <strong><?php echo e($stats['total_hours']); ?></strong>
                <span><?php echo e(__('student.hours_label')); ?></span>
            </div>
        </div>
        <div class="sanua-stat-pill">
            <span class="sanua-stat-pill__icon sanua-stat-pill__icon--amber" aria-hidden="true">
                <i class="fas fa-chart-line"></i>
            </span>
            <div class="sanua-stat-pill__body">
                <strong><?php echo e($stats['avg_progress']); ?>%</strong>
                <span><?php echo e(__('student.avg_progress_label')); ?></span>
            </div>
        </div>
    </div>

    <?php if($activeCourses->count() > 0): ?>
        <section class="sanua-section">
            <h2 class="sanua-section-title">📚 <?php echo e(__('student.my_courses_active_title')); ?></h2>

            <div class="sanua-courses-grid">
                <?php $__currentLoopData = $activeCourses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $progress = (int) ($course->pivot->progress ?? 0);
                        $isCompleted = $progress >= 100;
                    ?>
                    <a href="<?php echo e(route('my-courses.show', $course)); ?>" class="sanua-course-card">
                        <div class="sanua-course-card__thumb">
                            <?php if($course->thumbnail): ?>
                                <img src="<?php echo e(public_storage_url($course->thumbnail)); ?>" alt="<?php echo e($course->title); ?>" loading="lazy">
                            <?php else: ?>
                                <div class="sanua-course-card__thumb-fallback">
                                    <i class="fas fa-graduation-cap"></i>
                                    <span><?php echo e($course->academicSubject->name ?? __('student.course_fallback')); ?></span>
                                </div>
                            <?php endif; ?>
                            <span class="sanua-course-card__badge <?php echo e($isCompleted ? 'sanua-course-card__badge--done' : 'sanua-course-card__badge--active'); ?>">
                                <i class="fas <?php echo e($isCompleted ? 'fa-check-circle' : 'fa-play-circle'); ?>"></i>
                                <?php echo e($isCompleted ? __('student.completed_badge') : __('student.active_badge')); ?>

                            </span>
                        </div>

                        <div class="sanua-course-card__body">
                            <h3 class="sanua-course-card__title"><?php echo e($course->title); ?></h3>
                            <p class="sanua-course-card__meta">
                                <?php echo e($course->academicSubject->name ?? '—'); ?>

                                · <?php echo e($course->teacher->name ?? '—'); ?>

                                · <?php echo e($course->lessons->count()); ?> <?php echo e(__('student.lesson_singular')); ?>

                            </p>

                            <div class="sanua-course-card__stats">
                                <span><?php echo e(__('student.progress')); ?> <strong><?php echo e($progress); ?>%</strong></span>
                                <span class="points">
                                    <i class="fas fa-star"></i>
                                    <?php echo e(number_format((float) ($course->student_points ?? 0), 0)); ?>

                                </span>
                            </div>

                            <div class="sanua-course-card__bar" role="progressbar" aria-valuenow="<?php echo e(min($progress, 100)); ?>" aria-valuemin="0" aria-valuemax="100">
                                <div class="sanua-course-card__bar-fill" style="width: <?php echo e(min($progress, 100)); ?>%;"></div>
                            </div>

                            <span class="sanua-course-card__cta">
                                <i class="fas fa-play"></i>
                                <?php echo e(__('student.continue_learning')); ?>

                            </span>
                        </div>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <?php if($activeCourses->hasPages()): ?>
                <div class="sanua-pagination">
                    <?php echo e($activeCourses->links()); ?>

                </div>
            <?php endif; ?>
        </section>
    <?php else: ?>
        <div class="sanua-empty">
            <div class="sanua-empty__icon">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <h3><?php echo e(__('student.no_active_courses_my')); ?></h3>
            <p><?php echo e(__('student.no_active_courses_desc')); ?></p>
            <a href="<?php echo e(route('public.courses')); ?>" class="sanua-empty__btn">
                <i class="fas fa-search"></i>
                <?php echo e(__('student.browse_courses_btn')); ?>

            </a>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sana\resources\views/student/my-courses/index.blade.php ENDPATH**/ ?>