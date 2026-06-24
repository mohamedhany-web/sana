<?php $__env->startSection('title', __('student.achievements_title')); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('dashboard.partials.sanua-theme', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="sanua-dash">

    <header class="sanua-page-head">
        <div>
            <h1 class="sanua-page-head__title"><?php echo e(__('student.achievements_title')); ?></h1>
            <p class="sanua-page-head__sub"><?php echo e(__('student.total_points')); ?> · <?php echo e(__('student.achievements_title')); ?></p>
        </div>
        <div class="sanua-page-head__actions">
            <a href="<?php echo e(route('my-courses.index')); ?>" class="sanua-page-head__btn">
                <i class="fas fa-book-open"></i>
                <?php echo e(__('student.my_courses_link')); ?>

            </a>
        </div>
    </header>

    <?php if(isset($stats)): ?>
        <div class="sanua-stats-row">
            <div class="sanua-stat-pill">
                <span class="sanua-stat-pill__icon sanua-stat-pill__icon--purple" aria-hidden="true">
                    <i class="fas fa-trophy"></i>
                </span>
                <div class="sanua-stat-pill__body">
                    <strong><?php echo e($stats['total'] ?? 0); ?></strong>
                    <span>إجمالي الإنجازات</span>
                </div>
            </div>
            <div class="sanua-stat-pill">
                <span class="sanua-stat-pill__icon sanua-stat-pill__icon--gold" aria-hidden="true">
                    <i class="fas fa-star"></i>
                </span>
                <div class="sanua-stat-pill__body">
                    <strong><?php echo e(number_format($stats['total_points'] ?? 0)); ?></strong>
                    <span><?php echo e(__('student.total_points')); ?></span>
                </div>
            </div>
            <div class="sanua-stat-pill">
                <span class="sanua-stat-pill__icon sanua-stat-pill__icon--green" aria-hidden="true">
                    <i class="fas fa-medal"></i>
                </span>
                <div class="sanua-stat-pill__body">
                    <strong><?php echo e($achievements->count()); ?></strong>
                    <span>في هذه الصفحة</span>
                </div>
            </div>
            <div class="sanua-stat-pill">
                <span class="sanua-stat-pill__icon sanua-stat-pill__icon--amber" aria-hidden="true">
                    <i class="fas fa-layer-group"></i>
                </span>
                <div class="sanua-stat-pill__body">
                    <strong><?php echo e($achievements->currentPage()); ?>/<?php echo e(max(1, $achievements->lastPage())); ?></strong>
                    <span>صفحة العرض</span>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if(isset($achievements) && $achievements->count() > 0): ?>
        <section class="sanua-section">
            <h2 class="sanua-section-title">🏆 <?php echo e(__('student.achievements_title')); ?></h2>
            <div class="sanua-achievements-grid">
                <?php $__currentLoopData = $achievements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $achievement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <article class="sanua-achievement-card">
                        <?php if($achievement->achievement && $achievement->achievement->icon): ?>
                            <div class="sanua-achievement-card__icon">
                                <i class="<?php echo e($achievement->achievement->icon); ?>"></i>
                            </div>
                        <?php else: ?>
                            <div class="sanua-achievement-card__icon">
                                <i class="fas fa-trophy"></i>
                            </div>
                        <?php endif; ?>
                        <h3 class="sanua-achievement-card__title">
                            <?php echo e($achievement->achievement->name ?? __('student.achievement_default')); ?>

                        </h3>
                        <?php if($achievement->achievement->description ?? null): ?>
                            <p class="sanua-achievement-card__desc"><?php echo e($achievement->achievement->description); ?></p>
                        <?php endif; ?>
                        <?php if($achievement->points_earned): ?>
                            <span class="sanua-achievement-card__points">
                                <i class="fas fa-bolt"></i>
                                +<?php echo e($achievement->points_earned); ?> <?php echo e(__('student.points_earned')); ?>

                            </span>
                        <?php endif; ?>
                    </article>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </section>

        <?php if($achievements->hasPages()): ?>
            <div class="sanua-pagination">
                <?php echo e($achievements->links()); ?>

            </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="sanua-empty">
            <div class="sanua-empty__icon">
                <i class="fas fa-trophy"></i>
            </div>
            <h3><?php echo e(__('student.no_achievements')); ?></h3>
            <p>استمر في التعلّم لفتح إنجازات جديدة</p>
            <a href="<?php echo e(route('my-courses.index')); ?>" class="sanua-empty__btn">
                <i class="fas fa-book-open"></i>
                <?php echo e(__('student.view_my_courses')); ?>

            </a>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sana\resources\views\student\achievements\index.blade.php ENDPATH**/ ?>