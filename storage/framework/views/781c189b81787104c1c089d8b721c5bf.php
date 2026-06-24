<?php $__env->startSection('title', __('tutor.rate_lesson')); ?>
<?php $__env->startSection('header', __('tutor.rate_lesson')); ?>
<?php echo $__env->make('instructor.tutor-lessons.partials.dashboard-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->startSection('content'); ?>
<div class="id-tutor-page pb-6 w-full max-w-md">
    <form method="post" action="<?php echo e(route('instructor.tutor-lessons.bookings.rate.store',$booking)); ?>" class="id-panel id-form">
        <div class="id-panel-head"><h3 class="font-bold m-0"><?php echo e(__('tutor.rate_lesson')); ?></h3></div>
        <div class="id-panel-body space-y-4"><?php echo csrf_field(); ?>
            <div><label>التقييم (1-5)</label><input type="number" name="rating" min="1" max="5" required></div>
            <div><label>تعليق</label><textarea name="comment" rows="3"></textarea></div>
            <button type="submit" class="id-btn-primary w-full justify-center">إرسال</button>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sana\resources\views\instructor\tutor-lessons\bookings\rate.blade.php ENDPATH**/ ?>