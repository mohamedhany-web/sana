<?php $__env->startSection('title', 'حصتي'); ?>
<?php $__env->startSection('header', 'تفاصيل الحصة'); ?>
<?php echo $__env->make('student.tutor-lessons.partials.dashboard-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->startSection('content'); ?>
<div class="sd-page w-full pb-8 max-w-2xl">
    <a href="<?php echo e(route('student.tutor-lessons.bookings.index')); ?>" class="text-sm sd-link mb-4 inline-block">← كل الحصص</a>
    <div class="sd-panel">
        <div class="sd-panel-head"><h2 class="font-bold text-slate-800">حجز #<?php echo e($booking->code); ?></h2></div>
        <div class="sd-panel-body space-y-3">
            <div class="flex justify-between gap-4 text-sm"><span class="text-slate-500">المعلم</span><strong><?php echo e($booking->instructor?->name); ?></strong></div>
            <div class="flex justify-between gap-4 text-sm"><span class="text-slate-500">الموعد</span><strong><?php echo e($booking->scheduled_at?->format('Y-m-d H:i')); ?></strong></div>
            <div class="flex justify-between gap-4 text-sm"><span class="text-slate-500">الحالة</span><strong><?php echo e($booking->statusLabel()); ?></strong></div>
            <?php if($booking->billable_minutes > 0): ?>
            <div class="flex justify-between gap-4 text-sm"><span class="text-slate-500">دقائق مشتركة</span><strong><?php echo e($booking->billable_minutes); ?></strong></div>
            <?php endif; ?>
            <div class="flex flex-wrap gap-2 pt-3">
                <?php if($booking->classroomMeeting && in_array($booking->status,['confirmed','in_progress'])): ?>
                    <a href="<?php echo e(url('classroom/join/'.$booking->classroomMeeting->code)); ?>" class="sd-btn-primary"><?php echo e(__('tutor.enter_lesson')); ?></a>
                <?php endif; ?>
                <?php if($booking->status==='pending'): ?>
                    <form method="post" action="<?php echo e(route('student.tutor-lessons.bookings.cancel', $booking)); ?>"><?php echo csrf_field(); ?><button type="submit" class="sd-btn-outline">إلغاء</button></form>
                <?php endif; ?>
                <?php if($booking->status==='completed'): ?>
                    <a href="<?php echo e(route('student.tutor-lessons.bookings.rate', $booking)); ?>" class="sd-btn-outline"><?php echo e(__('tutor.rate_lesson')); ?></a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sana\resources\views/student/tutor-lessons/bookings/show.blade.php ENDPATH**/ ?>