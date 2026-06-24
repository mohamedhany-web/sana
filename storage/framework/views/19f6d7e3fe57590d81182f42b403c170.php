<?php $__env->startSection('title', __('student.orders_page_title')); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('dashboard.partials.sanua-theme', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<?php
    $collection = $orders->getCollection();
    $pendingCount = $collection->where('status', 'pending')->count();
    $approvedCount = $collection->where('status', 'approved')->count();
    $rejectedCount = $collection->where('status', 'rejected')->count();
?>

<div class="sanua-dash">

    <header class="sanua-page-head">
        <div>
            <h1 class="sanua-page-head__title"><?php echo e(__('student.orders_page_title')); ?></h1>
            <p class="sanua-page-head__sub"><?php echo e(__('student.orders_subtitle')); ?></p>
        </div>
        <div class="sanua-page-head__actions">
            <a href="<?php echo e(route('academic-years')); ?>" class="sanua-page-head__btn">
                <i class="fas fa-search"></i>
                <?php echo e(__('student.browse_courses_btn')); ?>

            </a>
        </div>
    </header>

    <?php if($orders->count() > 0): ?>
        <div class="sanua-stats-row">
            <div class="sanua-stat-pill">
                <span class="sanua-stat-pill__icon sanua-stat-pill__icon--purple" aria-hidden="true">
                    <i class="fas fa-shopping-cart"></i>
                </span>
                <div class="sanua-stat-pill__body">
                    <strong><?php echo e($orders->total()); ?></strong>
                    <span>إجمالي الطلبات</span>
                </div>
            </div>
            <div class="sanua-stat-pill">
                <span class="sanua-stat-pill__icon sanua-stat-pill__icon--amber" aria-hidden="true">
                    <i class="fas fa-clock"></i>
                </span>
                <div class="sanua-stat-pill__body">
                    <strong><?php echo e($pendingCount); ?></strong>
                    <span>قيد المراجعة</span>
                </div>
            </div>
            <div class="sanua-stat-pill">
                <span class="sanua-stat-pill__icon sanua-stat-pill__icon--green" aria-hidden="true">
                    <i class="fas fa-check-circle"></i>
                </span>
                <div class="sanua-stat-pill__body">
                    <strong><?php echo e($approvedCount); ?></strong>
                    <span>مقبولة</span>
                </div>
            </div>
            <div class="sanua-stat-pill">
                <span class="sanua-stat-pill__icon sanua-stat-pill__icon--red" aria-hidden="true">
                    <i class="fas fa-times-circle"></i>
                </span>
                <div class="sanua-stat-pill__body">
                    <strong><?php echo e($rejectedCount); ?></strong>
                    <span>مرفوضة</span>
                </div>
            </div>
        </div>

        <section class="sanua-section">
            <h2 class="sanua-section-title">🛒 <?php echo e(__('student.orders_page_title')); ?></h2>

            <?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="sanua-order-card">
                    <div class="sanua-order-card__row">
                        <div class="sanua-order-card__main">
                            <div class="sanua-live-card__badges">
                                <span class="sanua-badge <?php echo e($order->status == 'pending' ? 'sanua-badge--pending' : ($order->status == 'approved' ? 'sanua-badge--approved' : 'sanua-badge--rejected')); ?>">
                                    <?php if($order->status == 'pending'): ?><i class="fas fa-clock"></i>
                                    <?php elseif($order->status == 'approved'): ?><i class="fas fa-check-circle"></i>
                                    <?php else: ?><i class="fas fa-times-circle"></i>
                                    <?php endif; ?>
                                    <?php echo e($order->status_text); ?>

                                </span>
                            </div>
                            <h3 class="sanua-order-card__title">
                                <?php if($order->academic_year_id && $order->learningPath): ?>
                                    <?php echo e($order->learningPath->name ?? __('student.learning_path_label')); ?>

                                <?php else: ?>
                                    <?php echo e($order->course->title ?? __('student.course_undefined')); ?>

                                <?php endif; ?>
                            </h3>
                            <p class="sanua-order-card__sub">
                                <?php if($order->academic_year_id && $order->learningPath): ?>
                                    <?php echo e(__('student.learning_path_label')); ?>

                                    <?php if($order->learningPath->price): ?>
                                        · <?php echo e(number_format($order->learningPath->price, 2)); ?> <?php echo e(__('public.currency')); ?>

                                    <?php endif; ?>
                                <?php elseif($order->course && ($order->course->academicYear || $order->course->academicSubject)): ?>
                                    <?php if($order->course->academicYear): ?><?php echo e($order->course->academicYear->name); ?><?php endif; ?>
                                    <?php if($order->course->academicSubject): ?> · <?php echo e($order->course->academicSubject->name); ?><?php endif; ?>
                                <?php endif; ?>
                                · <?php echo e($order->created_at->diffForHumans()); ?>

                            </p>

                            <div class="sanua-metric-grid">
                                <div class="sanua-metric">
                                    <span class="sanua-metric__label"><?php echo e(__('student.amount_label')); ?></span>
                                    <span class="sanua-metric__value"><?php echo e(number_format($order->amount, 2)); ?> <?php echo e(__('public.currency')); ?></span>
                                </div>
                                <div class="sanua-metric">
                                    <span class="sanua-metric__label"><?php echo e(__('student.payment_method_label')); ?></span>
                                    <span class="sanua-metric__value">
                                        <?php if($order->payment_method == 'bank_transfer'): ?> <?php echo e(__('student.bank_transfer')); ?>

                                        <?php elseif($order->payment_method == 'cash'): ?> <?php echo e(__('student.cash_label')); ?>

                                        <?php else: ?> <?php echo e(__('student.other_label')); ?>

                                        <?php endif; ?>
                                    </span>
                                </div>
                                <div class="sanua-metric">
                                    <span class="sanua-metric__label"><?php echo e(__('student.order_date_label')); ?></span>
                                    <span class="sanua-metric__value"><?php echo e($order->created_at->format('d/m/Y')); ?></span>
                                </div>
                                <?php if($order->approved_at): ?>
                                    <div class="sanua-metric">
                                        <span class="sanua-metric__label"><?php echo e(__('student.approved_date_label')); ?></span>
                                        <span class="sanua-metric__value"><?php echo e($order->approved_at->format('d/m/Y')); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <?php if($order->notes): ?>
                                <div class="sanua-order-card__note">
                                    <strong><?php echo e(__('student.your_notes')); ?>:</strong> <?php echo e($order->notes); ?>

                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="sanua-order-card__actions">
                            <a href="<?php echo e(route('orders.show', $order)); ?>" class="sanua-btn sanua-btn--purple">
                                <i class="fas fa-eye"></i>
                                <?php echo e(__('student.view_details')); ?>

                            </a>
                            <?php if($order->status == 'approved' && $order->course): ?>
                                <a href="<?php echo e(route('courses.show', $order->course)); ?>" class="sanua-btn sanua-btn--green">
                                    <i class="fas fa-play"></i>
                                    <?php echo e(__('student.enter_course')); ?>

                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </section>

        <?php if($orders->hasPages()): ?>
            <div class="sanua-pagination"><?php echo e($orders->links()); ?></div>
        <?php endif; ?>
    <?php else: ?>
        <div class="sanua-empty">
            <div class="sanua-empty__icon"><i class="fas fa-shopping-cart"></i></div>
            <h3><?php echo e(__('student.no_orders')); ?></h3>
            <p><?php echo e(__('student.no_orders_desc')); ?></p>
            <a href="<?php echo e(route('academic-years')); ?>" class="sanua-empty__btn">
                <i class="fas fa-plus"></i>
                <?php echo e(__('student.browse_courses_btn')); ?>

            </a>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sana\resources\views\student\orders\index.blade.php ENDPATH**/ ?>