<?php $__env->startSection('title', 'حجز '.$booking->code); ?>
<?php $__env->startSection('header', 'تفاصيل الحجز'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6 max-w-3xl">
    <?php echo $__env->make('admin.tutor-lessons._nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <div class="bg-white rounded-2xl border p-6 shadow-sm space-y-3 text-sm">
        <p><strong>الرمز:</strong> <?php echo e($booking->code); ?></p>
        <p><strong>الطالب:</strong> <?php echo e($booking->student?->name); ?></p>
        <p><strong>المعلم:</strong> <?php echo e($booking->instructor?->name); ?></p>
        <p><strong>الموعد:</strong> <?php echo e($booking->scheduled_at?->format('Y-m-d H:i')); ?> (<?php echo e($booking->duration_minutes); ?> د)</p>
        <p><strong>الحالة:</strong> <?php echo e($booking->status); ?></p>
        <p><strong>نمط الحجز:</strong> <?php echo e($booking->matching_mode); ?></p>
        <p><strong>دقائق محسوبة:</strong> <?php echo e((int) $booking->billable_minutes); ?></p>
        <?php if($booking->student_notes): ?><p><strong>ملاحظات الطالب:</strong> <?php echo e($booking->student_notes); ?></p><?php endif; ?>
        <?php if($booking->classroomMeeting): ?>
            <p><strong>غرفة Classroom:</strong> <?php echo e($booking->classroomMeeting->code); ?></p>
        <?php endif; ?>
    </div>
    <?php if($booking->student): ?>
    <form method="post" action="<?php echo e(route('admin.tutor-lessons.students.quota', $booking->student)); ?>" class="bg-white rounded-2xl border p-6 shadow-sm space-y-3">
        <?php echo csrf_field(); ?>
        <h3 class="font-bold">تعديل باقة ساعات الطالب (يدوي)</h3>
        <?php $sp = \App\Models\StudentLearningProfile::firstOrCreate(['user_id' => $booking->student_id]); ?>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="text-xs font-bold">ساعات الباقة (-1 = غير محدود)</label>
                <input type="number" name="lesson_hours_quota" value="<?php echo e($sp->lesson_hours_quota); ?>" class="w-full border rounded-lg px-3 py-2">
            </div>
            <div>
                <label class="text-xs font-bold">ساعات مستهلكة</label>
                <input type="number" name="lesson_hours_used" value="<?php echo e($sp->lesson_hours_used); ?>" class="w-full border rounded-lg px-3 py-2">
            </div>
        </div>
        <button class="px-4 py-2 bg-violet-600 text-white rounded-lg font-bold text-sm">حفظ الباقة</button>
    </form>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sana\resources\views\admin\tutor-lessons\booking-show.blade.php ENDPATH**/ ?>