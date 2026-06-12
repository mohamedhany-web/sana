<?php $__env->startSection('title', 'طلب '.$assisted->code); ?>
<?php echo $__env->make('partials.tutor-lesson-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->startSection('content'); ?>
<div class="tl-page max-w-xl mx-auto py-2"><div class="tl-card"><p class="font-bold"><?php echo e($assisted->student?->name); ?> — <?php echo e($assisted->code); ?></p><p><?php echo e($assisted->statusLabel()); ?></p><p class="mt-3 text-sm"><?php echo e($assisted->message); ?></p></div></div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sana\resources\views\parent\tutor-lessons\assisted-show.blade.php ENDPATH**/ ?>