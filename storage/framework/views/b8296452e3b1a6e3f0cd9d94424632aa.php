<?php $__env->startSection('title', __('student.my_certificates_title')); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('dashboard.partials.sanua-theme', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<?php $isRtl = app()->getLocale() === 'ar'; ?>

<div class="sanua-dash">

    <header class="sanua-page-head">
        <div>
            <h1 class="sanua-page-head__title"><?php echo e(__('student.my_certificates_title')); ?></h1>
            <p class="sanua-page-head__sub"><?php echo e(__('student.certificates_subtitle')); ?></p>
        </div>
        <div class="sanua-page-head__actions">
            <a href="<?php echo e(route('my-courses.index')); ?>" class="sanua-page-head__btn">
                <i class="fas fa-book-open"></i>
                <?php echo e(__('student.view_my_courses')); ?>

            </a>
        </div>
    </header>

    <?php if(isset($stats)): ?>
        <div class="sanua-stats-row">
            <div class="sanua-stat-pill">
                <span class="sanua-stat-pill__icon sanua-stat-pill__icon--purple" aria-hidden="true">
                    <i class="fas fa-certificate"></i>
                </span>
                <div class="sanua-stat-pill__body">
                    <strong><?php echo e($stats['total'] ?? 0); ?></strong>
                    <span><?php echo e(__('student.total_certificates')); ?></span>
                </div>
            </div>
            <div class="sanua-stat-pill">
                <span class="sanua-stat-pill__icon sanua-stat-pill__icon--green" aria-hidden="true">
                    <i class="fas fa-check-circle"></i>
                </span>
                <div class="sanua-stat-pill__body">
                    <strong><?php echo e($stats['issued'] ?? 0); ?></strong>
                    <span><?php echo e(__('student.issued_label')); ?></span>
                </div>
            </div>
            <div class="sanua-stat-pill">
                <span class="sanua-stat-pill__icon sanua-stat-pill__icon--gold" aria-hidden="true">
                    <i class="fas fa-award"></i>
                </span>
                <div class="sanua-stat-pill__body">
                    <strong><?php echo e($certificates->count()); ?></strong>
                    <span>في هذه الصفحة</span>
                </div>
            </div>
            <div class="sanua-stat-pill">
                <span class="sanua-stat-pill__icon sanua-stat-pill__icon--amber" aria-hidden="true">
                    <i class="fas fa-layer-group"></i>
                </span>
                <div class="sanua-stat-pill__body">
                    <strong><?php echo e($certificates->currentPage()); ?>/<?php echo e(max(1, $certificates->lastPage())); ?></strong>
                    <span>صفحة العرض</span>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if(isset($certificates) && $certificates->count() > 0): ?>
        <section class="sanua-section">
            <h2 class="sanua-section-title">🎓 <?php echo e(__('student.my_certificates_title')); ?></h2>
            <div class="sanua-courses-grid">
                <?php $__currentLoopData = $certificates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $certificate): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(route('student.certificates.show', $certificate)); ?>" class="sanua-cert-card">
                        <div class="sanua-cert-card__hero">
                            <i class="fas fa-certificate"></i>
                        </div>
                        <div class="sanua-cert-card__body">
                            <h3 class="sanua-cert-card__title">
                                <?php echo e($certificate->title ?? $certificate->course_name ?? __('student.completion_certificate')); ?>

                            </h3>
                            <?php if($certificate->course): ?>
                                <p class="sanua-cert-card__course"><?php echo e($certificate->course->title); ?></p>
                            <?php endif; ?>
                            <div class="sanua-cert-card__meta">
                                <span>
                                    <i class="fas fa-calendar"></i>
                                    <?php echo e(($certificate->issued_at ? $certificate->issued_at->format('Y-m-d') : ($certificate->issue_date ? $certificate->issue_date->format('Y-m-d') : '—'))); ?>

                                </span>
                                <?php if($certificate->certificate_number): ?>
                                    <span class="sanua-cert-card__num">#<?php echo e(substr($certificate->certificate_number, -6)); ?></span>
                                <?php endif; ?>
                            </div>
                            <span class="sanua-cert-card__cta">
                                <?php echo e(__('student.view_certificate')); ?>

                                <i class="fas fa-arrow-<?php echo e($isRtl ? 'left' : 'right'); ?>"></i>
                            </span>
                        </div>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </section>

        <?php if($certificates->hasPages()): ?>
            <div class="sanua-pagination">
                <?php echo e($certificates->links()); ?>

            </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="sanua-empty">
            <div class="sanua-empty__icon">
                <i class="fas fa-certificate"></i>
            </div>
            <h3><?php echo e(__('student.no_certificates')); ?></h3>
            <p><?php echo e(__('student.no_certificates_desc')); ?></p>
            <a href="<?php echo e(route('my-courses.index')); ?>" class="sanua-empty__btn">
                <i class="fas fa-book-open"></i>
                <?php echo e(__('student.view_my_courses')); ?>

            </a>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sana\resources\views\student\certificates\index.blade.php ENDPATH**/ ?>