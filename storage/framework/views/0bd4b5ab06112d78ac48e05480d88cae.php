<?php $__env->startSection('title', __('tutor.rate_lesson')); ?>
<?php $__env->startSection('header', __('tutor.rate_lesson')); ?>
<?php echo $__env->make('student.tutor-lessons.partials.dashboard-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->startSection('content'); ?>
<div class="sd-page w-full pb-8 max-w-md">
    <form method="post" action="<?php echo e(route('student.tutor-lessons.bookings.rate.store',$booking)); ?>" class="sd-panel sd-form">
        <div class="sd-panel-head"><h2 class="font-bold"><?php echo e(__('tutor.rate_lesson')); ?></h2></div>
        <div class="sd-panel-body space-y-4"><?php echo csrf_field(); ?>
            <div><label>التقييم (1-5)</label><input type="number" name="rating" min="1" max="5" required></div>
            <div><label>تعليق</label><textarea name="comment" rows="3"></textarea></div>
            <button type="submit" class="sd-btn-primary w-full">إرسال</button>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sana\resources\views\student\tutor-lessons\bookings\rate.blade.php ENDPATH**/ ?>