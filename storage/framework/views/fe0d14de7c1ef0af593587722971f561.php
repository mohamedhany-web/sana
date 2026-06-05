<?php $__env->startSection('title', 'حجز #'.$booking->code); ?>
<?php $__env->startSection('header', 'تفاصيل الحصة'); ?>
<?php echo $__env->make('instructor.tutor-lessons.partials.dashboard-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->startSection('content'); ?>
<div class="id-tutor-page space-y-6 pb-6 w-full max-w-3xl">
    <?php if(session('success')): ?><div class="ins-flash bg-emerald-50 border border-emerald-200 text-emerald-800"><?php echo e(session('success')); ?></div><?php endif; ?>
    <?php if(session('error')): ?><div class="ins-flash bg-rose-50 border border-rose-200 text-rose-800"><?php echo e(session('error')); ?></div><?php endif; ?>
    <a href="<?php echo e(route('instructor.tutor-lessons.bookings.index')); ?>" class="id-link inline-flex items-center gap-1"><i class="fas fa-arrow-right text-xs"></i> العودة للحجوزات</a>
    <div class="id-panel">
        <div class="id-panel-head">
            <h3 class="font-bold m-0">حجز #<?php echo e($booking->code); ?></h3>
            <span class="id-badge id-badge-<?php echo e($booking->status === 'confirmed' ? 'confirmed' : 'pending'); ?>"><?php echo e($booking->statusLabel()); ?></span>
        </div>
        <div class="id-panel-body space-y-3">
            <div class="flex justify-between text-sm"><span class="text-slate-500">الطالب</span><strong><?php echo e($booking->student?->name); ?></strong></div>
            <div class="flex justify-between text-sm"><span class="text-slate-500">الموعد</span><strong><?php echo e($booking->scheduled_at?->format('Y-m-d H:i')); ?></strong></div>
            <div class="flex justify-between text-sm"><span class="text-slate-500">دقائق مشتركة</span><strong><?php echo e($booking->billable_minutes); ?></strong></div>
            <?php if($booking->student_notes): ?><p class="text-sm bg-slate-50 p-3 rounded-xl m-0"><?php echo e($booking->student_notes); ?></p><?php endif; ?>
            <div class="flex flex-wrap gap-2 pt-3">
                <?php if(in_array($booking->status, ['confirmed', 'in_progress'], true)): ?>
                    <form method="post" action="<?php echo e(route('instructor.tutor-lessons.bookings.send-reminder', $booking)); ?>" data-turbo="false">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="id-btn-ghost inline-flex items-center gap-2"
                                onclick="return confirm('إرسال تذكير للطالب بموعد الحصة؟')">
                            <i class="fas fa-bell"></i>
                            <?php echo e(__('tutor.send_reminder')); ?>

                        </button>
                    </form>
                <?php endif; ?>
                <?php if($booking->status === 'pending'): ?>
                    <form method="post" action="<?php echo e(route('instructor.tutor-lessons.bookings.confirm', $booking)); ?>" data-turbo="false"><?php echo csrf_field(); ?><button type="submit" class="id-btn-primary">تأكيد وإنشاء الغرفة</button></form>
                    <form method="post" action="<?php echo e(route('instructor.tutor-lessons.bookings.cancel', $booking)); ?>" data-turbo="false"><?php echo csrf_field(); ?><button type="submit" class="id-btn-ghost">إلغاء</button></form>
                <?php endif; ?>
                <?php if(in_array($booking->status, ['confirmed','in_progress']) && $booking->classroomMeeting): ?>
                    <a href="<?php echo e(route('instructor.classroom.room', $booking->classroomMeeting)); ?>" class="id-btn-primary" data-turbo="false"><?php echo e(__('tutor.enter_lesson')); ?></a>
                    <form method="post" action="<?php echo e(route('instructor.tutor-lessons.bookings.complete', $booking)); ?>" data-turbo="false"><?php echo csrf_field(); ?><button type="submit" class="id-btn-ghost">إنهاء وتسجيل الدقائق</button></form>
                <?php endif; ?>
                <?php if($booking->status === 'completed'): ?>
                    <a href="<?php echo e(route('instructor.tutor-lessons.bookings.rate', $booking)); ?>" class="id-btn-ghost"><?php echo e(__('tutor.rate_lesson')); ?></a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sana\resources\views/instructor/tutor-lessons/bookings/show.blade.php ENDPATH**/ ?>