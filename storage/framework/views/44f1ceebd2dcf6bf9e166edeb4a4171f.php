<?php $__env->startSection('title', 'تسجيلات جلسات البث'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('dashboard.partials.sanua-theme', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="sanua-dash">

    <header class="sanua-page-head">
        <div>
            <h1 class="sanua-page-head__title">تسجيلات جلسات البث</h1>
            <p class="sanua-page-head__sub">مشاهدة تسجيلات الجلسات المنتهية المنشورة من الإدارة</p>
        </div>
        <div class="sanua-page-head__actions">
            <a href="<?php echo e(route('student.live-sessions.index')); ?>" class="sanua-page-head__btn">
                <i class="fas fa-broadcast-tower"></i>
                جلسات البث
            </a>
        </div>
    </header>

    <div class="sanua-stats-row">
        <div class="sanua-stat-pill">
            <span class="sanua-stat-pill__icon sanua-stat-pill__icon--purple" aria-hidden="true">
                <i class="fas fa-film"></i>
            </span>
            <div class="sanua-stat-pill__body">
                <strong><?php echo e($recordings->total()); ?></strong>
                <span>إجمالي التسجيلات</span>
            </div>
        </div>
        <div class="sanua-stat-pill">
            <span class="sanua-stat-pill__icon sanua-stat-pill__icon--gold" aria-hidden="true">
                <i class="fas fa-play-circle"></i>
            </span>
            <div class="sanua-stat-pill__body">
                <strong><?php echo e($recordings->count()); ?></strong>
                <span>في هذه الصفحة</span>
            </div>
        </div>
        <div class="sanua-stat-pill">
            <span class="sanua-stat-pill__icon sanua-stat-pill__icon--green" aria-hidden="true">
                <i class="fas fa-layer-group"></i>
            </span>
            <div class="sanua-stat-pill__body">
                <strong><?php echo e($recordings->currentPage()); ?>/<?php echo e(max(1, $recordings->lastPage())); ?></strong>
                <span>صفحة العرض</span>
            </div>
        </div>
        <div class="sanua-stat-pill">
            <span class="sanua-stat-pill__icon sanua-stat-pill__icon--amber" aria-hidden="true">
                <i class="fas fa-video"></i>
            </span>
            <div class="sanua-stat-pill__body">
                <strong><?php echo e($recordings->count()); ?></strong>
                <span>جاهزة للمشاهدة</span>
            </div>
        </div>
    </div>

    <?php if($recordings->isEmpty()): ?>
        <div class="sanua-empty">
            <div class="sanua-empty__icon">
                <i class="fas fa-film"></i>
            </div>
            <h3>لا توجد تسجيلات متاحة حالياً</h3>
            <p>ستظهر هنا تسجيلات الجلسات بعد انتهائها ونشرها من الإدارة</p>
            <a href="<?php echo e(route('student.live-sessions.index')); ?>" class="sanua-empty__btn">
                <i class="fas fa-broadcast-tower"></i>
                عودة لجلسات البث
            </a>
        </div>
    <?php else: ?>
        <section class="sanua-section">
            <h2 class="sanua-section-title">🎬 التسجيلات المتاحة</h2>
            <div class="sanua-courses-grid">
                <?php $__currentLoopData = $recordings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rec): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(route('student.live-recordings.show', $rec)); ?>" class="sanua-recording-card">
                        <span class="sanua-recording-card__icon"><i class="fas fa-play"></i></span>
                        <h3 class="sanua-recording-card__title"><?php echo e($rec->title ?? 'تسجيل #' . $rec->id); ?></h3>
                        <p class="sanua-recording-card__sub"><?php echo e($rec->session?->title ?? '—'); ?></p>
                        <div class="sanua-recording-card__meta">
                            <span><i class="fas fa-clock"></i><?php echo e($rec->duration_for_humans); ?></span>
                            <span><i class="fas fa-hdd"></i><?php echo e($rec->file_size_for_humans); ?></span>
                        </div>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </section>

        <?php if($recordings->hasPages()): ?>
            <div class="sanua-pagination">
                <?php echo e($recordings->links()); ?>

            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sana\resources\views\student\live-recordings\index.blade.php ENDPATH**/ ?>