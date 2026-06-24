<?php $__env->startSection('title', 'واجباتي'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('dashboard.partials.sanua-theme', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<?php
    $pendingCount = $assignments->filter(fn ($a) => ! ($a->my_submission ?? null))->count();
    $submittedCount = $assignments->filter(fn ($a) => ($a->my_submission->status ?? null) === 'submitted')->count();
    $gradedCount = $assignments->filter(fn ($a) => ($a->my_submission->status ?? null) === 'graded')->count();
    $returnedCount = $assignments->filter(fn ($a) => ($a->my_submission->status ?? null) === 'returned')->count();
?>

<div class="sanua-dash">

    <header class="sanua-page-head">
        <div>
            <h1 class="sanua-page-head__title">واجباتي</h1>
            <p class="sanua-page-head__sub">الواجبات المنشورة في كورساتك المسجّل بها</p>
        </div>
        <div class="sanua-page-head__actions">
            <a href="<?php echo e(route('my-courses.index')); ?>" class="sanua-page-head__btn">
                <i class="fas fa-book-open"></i>
                كورساتي
            </a>
        </div>
    </header>

    <div class="sanua-stats-row">
        <div class="sanua-stat-pill">
            <span class="sanua-stat-pill__icon sanua-stat-pill__icon--purple" aria-hidden="true">
                <i class="fas fa-tasks"></i>
            </span>
            <div class="sanua-stat-pill__body">
                <strong><?php echo e($assignments->count()); ?></strong>
                <span>إجمالي الواجبات</span>
            </div>
        </div>
        <div class="sanua-stat-pill">
            <span class="sanua-stat-pill__icon sanua-stat-pill__icon--amber" aria-hidden="true">
                <i class="fas fa-hourglass-half"></i>
            </span>
            <div class="sanua-stat-pill__body">
                <strong><?php echo e($pendingCount); ?></strong>
                <span>لم يُسلَّم</span>
            </div>
        </div>
        <div class="sanua-stat-pill">
            <span class="sanua-stat-pill__icon sanua-stat-pill__icon--gold" aria-hidden="true">
                <i class="fas fa-paper-plane"></i>
            </span>
            <div class="sanua-stat-pill__body">
                <strong><?php echo e($submittedCount); ?></strong>
                <span>قيد التصحيح</span>
            </div>
        </div>
        <div class="sanua-stat-pill">
            <span class="sanua-stat-pill__icon sanua-stat-pill__icon--green" aria-hidden="true">
                <i class="fas fa-check-circle"></i>
            </span>
            <div class="sanua-stat-pill__body">
                <strong><?php echo e($gradedCount + $returnedCount); ?></strong>
                <span>مُقيَّم / مُعاد</span>
            </div>
        </div>
    </div>

    <?php if($assignments->isEmpty()): ?>
        <div class="sanua-empty">
            <div class="sanua-empty__icon">
                <i class="fas fa-tasks"></i>
            </div>
            <h3>لا توجد واجبات متاحة حالياً</h3>
            <p>ستظهر الواجبات هنا عند نشرها في كورساتك</p>
            <a href="<?php echo e(route('my-courses.index')); ?>" class="sanua-empty__btn">
                <i class="fas fa-book-open"></i>
                كورساتي
            </a>
        </div>
    <?php else: ?>
        <section class="sanua-section">
            <h2 class="sanua-section-title">📝 قائمة الواجبات</h2>
            <div class="sanua-session-list">
                <?php $__currentLoopData = $assignments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assignment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $sub = $assignment->my_submission ?? null;
                        $courseTitle = $assignment->course->title ?? 'كورس';
                    ?>
                    <a href="<?php echo e(route('student.assignments.show', $assignment)); ?>" class="sanua-session-card">
                        <div class="sanua-session-card__row">
                            <div class="sanua-session-card__main">
                                <div class="sanua-live-card__badges">
                                    <?php if(! $sub): ?>
                                        <span class="sanua-badge sanua-badge--pending">لم يُسلَّم</span>
                                    <?php elseif($sub->status === 'submitted'): ?>
                                        <span class="sanua-badge sanua-badge--submitted">قيد التصحيح</span>
                                    <?php elseif($sub->status === 'graded'): ?>
                                        <span class="sanua-badge sanua-badge--graded">
                                            مُقيَّم<?php echo e($sub->score !== null ? ' — '.$sub->score.'/'.$assignment->max_score : ''); ?>

                                        </span>
                                    <?php elseif($sub->status === 'returned'): ?>
                                        <span class="sanua-badge sanua-badge--returned">مُعاد للتعديل</span>
                                    <?php endif; ?>
                                    <span class="sanua-badge sanua-badge--course"><?php echo e($courseTitle); ?></span>
                                </div>
                                <h3 class="sanua-session-card__title"><?php echo e($assignment->title); ?></h3>
                                <?php if($assignment->due_date): ?>
                                    <div class="sanua-session-card__details">
                                        <span>
                                            <i class="fas fa-clock"></i>
                                            التسليم: <?php echo e($assignment->due_date->timezone(config('app.timezone'))->format('Y-m-d H:i')); ?>

                                        </span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <span class="sanua-session-card__action">
                                فتح الواجب
                                <i class="fas fa-chevron-left"></i>
                            </span>
                        </div>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </section>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sana\resources\views\student\assignments\index.blade.php ENDPATH**/ ?>