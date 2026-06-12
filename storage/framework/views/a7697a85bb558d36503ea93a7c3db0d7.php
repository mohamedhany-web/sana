<?php $__env->startSection('title', __('tutor.my_lessons')); ?>
<?php $__env->startSection('header', __('tutor.my_lessons')); ?>
<?php echo $__env->make('instructor.tutor-lessons.partials.dashboard-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->startSection('content'); ?>
<div class="id-tutor-page space-y-6 pb-6 w-full">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-black text-slate-900 m-0"><?php echo e(__('tutor.my_lessons')); ?></h2>
            <p class="text-sm text-slate-500 mt-1 mb-0">كل طلبات الحجز والحصص المؤكدة</p>
        </div>
        <a href="<?php echo e(route('instructor.tutor-lessons.hub')); ?>" class="id-btn-ghost"><i class="fas fa-arrow-right text-xs"></i> <?php echo e(__('tutor.hub_title')); ?></a>
    </div>
    <div class="id-panel">
        <div class="id-panel-body">
            <?php $__empty_1 = true; $__currentLoopData = $bookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <a href="<?php echo e(route('instructor.tutor-lessons.bookings.show', $b)); ?>" class="id-row block">
                    <div class="id-avatar"><?php echo e(mb_substr($b->student?->name ?? '?', 0, 1)); ?></div>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-slate-800 m-0 truncate"><?php echo e($b->student?->name); ?> <?php if($b->is_trial): ?><span class="id-badge id-badge-pending">تجريبي</span><?php endif; ?></p>
                        <p class="text-xs text-slate-500 m-0"><?php echo e($b->scheduled_at?->format('Y-m-d H:i')); ?> · <?php echo e($b->statusLabel()); ?></p>
                    </div>
                    <i class="fas fa-chevron-left text-slate-300 text-sm"></i>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p class="text-center text-slate-500 py-10 text-sm">لا توجد حجوزات.</p>
            <?php endif; ?>
            <div class="mt-4"><?php echo e($bookings->links()); ?></div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sana\resources\views\instructor\tutor-lessons\bookings\index.blade.php ENDPATH**/ ?>