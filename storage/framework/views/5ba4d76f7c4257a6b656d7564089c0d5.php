<?php $__env->startSection('title', 'حصة الابن'); ?>
<?php echo $__env->make('partials.tutor-lesson-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->startSection('content'); ?>
<div class="tl-page max-w-xl mx-auto py-2"><div class="tl-card space-y-2">
<p><strong><?php echo e($booking->student?->name); ?></strong> مع <?php echo e($booking->instructor?->name); ?></p>
<p><?php echo e($booking->scheduled_at?->format('Y-m-d H:i')); ?> — <?php echo e($booking->statusLabel()); ?></p>
<?php if($booking->classroomMeeting): ?><a href="<?php echo e(url('classroom/join/'.$booking->classroomMeeting->code)); ?>" class="tl-btn tl-btn-primary"><?php echo e(__('tutor.enter_lesson')); ?></a><?php endif; ?>
</div></div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sana\resources\views\parent\tutor-lessons\booking-show.blade.php ENDPATH**/ ?>