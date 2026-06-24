<?php $__env->startSection('title', 'طلب '.$assisted->code); ?>
<?php $__env->startSection('header', 'طلب مساعدة'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6 max-w-xl">
    <?php echo $__env->make('admin.tutor-lessons._nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<div class="bg-white rounded-xl border p-6 space-y-4">
    <p><strong>الطالب:</strong> <?php echo e($assisted->student?->name); ?></p>
    <p><strong>ولي الأمر:</strong> <?php echo e($assisted->parent?->name ?? '—'); ?></p>
    <p class="text-sm"><?php echo e($assisted->message); ?></p>
    <form method="post" action="<?php echo e(route('admin.tutor-lessons.assisted.assign', $assisted)); ?>" class="space-y-3">
        <?php echo csrf_field(); ?>
        <div><label class="block text-sm font-bold mb-1">المعلم</label>
            <select name="assigned_instructor_id" class="w-full border rounded-lg p-2" required>
                <?php $__currentLoopData = $instructors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ip): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($ip->user_id); ?>"><?php echo e($ip->user?->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div><label class="block text-sm font-bold mb-1">الموعد</label><input type="datetime-local" name="scheduled_at" class="w-full border rounded-lg p-2" required></div>
        <button class="bg-blue-700 text-white px-4 py-2 rounded-lg font-bold">تعيين وإنشاء حجز</button>
    </form>
</div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sana\resources\views\admin\tutor-lessons\assisted-show.blade.php ENDPATH**/ ?>