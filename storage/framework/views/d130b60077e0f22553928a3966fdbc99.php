<?php $__env->startSection('header', __('tutor.parent_hub_title')); ?>
<?php $__env->startSection('title', __('tutor.parent_hub_title')); ?>
<?php $__env->startSection('content'); ?>
<?php echo $__env->make('partials.tutor-lesson-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<div class="tl-page max-w-4xl mx-auto py-4 space-y-4">
    <div class="tl-hero"><h1><?php echo e(__('tutor.parent_hub_title')); ?></h1><p>متابعة حصص الأبناء وطلب مساعدة في إيجاد معلم.</p>
        <a href="<?php echo e(route('parent.tutor-lessons.assisted')); ?>" class="tl-btn tl-btn-primary mt-3 inline-flex">طلب مساعدة</a>
    </div>
    <?php $__currentLoopData = $bookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e(route('parent.tutor-lessons.bookings.show',$b)); ?>" class="tl-card block no-underline text-inherit"><?php echo e($b->student?->name); ?> — <?php echo e($b->instructor?->name); ?> — <?php echo e($b->scheduled_at?->format('Y-m-d H:i')); ?></a>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sana\resources\views\parent\tutor-lessons\hub.blade.php ENDPATH**/ ?>