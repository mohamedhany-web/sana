<?php $__env->startSection('title', 'طلب '.$assisted->code); ?>
<?php $__env->startSection('header', 'طلب المساعدة'); ?>
<?php echo $__env->make('student.tutor-lessons.partials.dashboard-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->startSection('content'); ?>
<div class="sd-page w-full pb-8 max-w-2xl">
    <div class="sd-panel">
        <div class="sd-panel-head"><h2 class="font-bold"><?php echo e($assisted->code); ?></h2><span class="sd-badge sd-badge-pending"><?php echo e($assisted->statusLabel()); ?></span></div>
        <div class="sd-panel-body">
            <p class="text-sm text-slate-600 whitespace-pre-wrap"><?php echo e($assisted->message); ?></p>
            <?php if($assisted->assignedInstructor): ?>
                <p class="mt-4 text-emerald-700 font-semibold"><i class="fas fa-user-check ms-1"></i> المعلم: <?php echo e($assisted->assignedInstructor->name); ?></p>
            <?php endif; ?>
            <a href="<?php echo e(route('student.tutor-lessons.hub')); ?>" class="sd-btn-outline mt-6 inline-flex">العودة</a>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sana\resources\views\student\tutor-lessons\assisted-show.blade.php ENDPATH**/ ?>