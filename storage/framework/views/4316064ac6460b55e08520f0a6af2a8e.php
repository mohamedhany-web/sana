<?php $__env->startSection('title', __('tutor.my_lessons')); ?>
<?php $__env->startSection('header', __('tutor.my_lessons')); ?>
<?php echo $__env->make('student.tutor-lessons.partials.dashboard-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->startSection('content'); ?>
<div class="sd-page space-y-6 w-full pb-8">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="font-heading text-xl font-black text-slate-800"><?php echo e(__('tutor.my_lessons')); ?></h1>
        <a href="<?php echo e(route('student.tutor-lessons.hub')); ?>" class="sd-btn-outline text-sm">← الرئيسية</a>
    </div>
    <div class="sd-panel">
        <div class="sd-panel-body">
            <?php $__empty_1 = true; $__currentLoopData = $bookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <a href="<?php echo e(route('student.tutor-lessons.bookings.show',$b)); ?>" class="sd-lesson-row block no-underline text-inherit hover:bg-slate-50/80 -mx-2 px-2 rounded-xl">
                    <div class="sd-avatar"><?php echo e(mb_substr($b->instructor?->name ?? '?',0,1)); ?></div>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-slate-800"><?php echo e($b->instructor?->name); ?></p>
                        <p class="text-xs text-slate-500"><?php echo e($b->scheduled_at?->format('Y-m-d H:i')); ?> · <?php echo e($b->statusLabel()); ?></p>
                    </div>
                    <i class="fas fa-chevron-left text-slate-300 text-sm"></i>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p class="text-slate-500 text-sm text-center py-8">لا توجد حصص.</p>
            <?php endif; ?>
            <div class="mt-4"><?php echo e($bookings->links()); ?></div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sana\resources\views\student\tutor-lessons\bookings\index.blade.php ENDPATH**/ ?>