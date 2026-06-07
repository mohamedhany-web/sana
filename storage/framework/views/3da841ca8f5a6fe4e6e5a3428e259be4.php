<?php $__env->startSection('title', 'حجوزات الحصص'); ?>
<?php $__env->startSection('header', 'حجوزات الحصص'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <?php echo $__env->make('admin.tutor-lessons._nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <form method="get" class="flex gap-2 items-end">
        <div>
            <label class="text-xs font-bold text-slate-600">الحالة</label>
            <select name="status" class="border rounded-lg px-3 py-2 text-sm">
                <option value="">الكل</option>
                <?php $__currentLoopData = ['pending','confirmed','in_progress','completed','cancelled']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($s); ?>" <?php if(request('status') === $s): echo 'selected'; endif; ?>><?php echo e($s); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <button class="px-4 py-2 bg-slate-800 text-white rounded-lg text-sm font-bold">تصفية</button>
    </form>
    <div class="bg-white rounded-2xl border overflow-hidden shadow-sm">
        <table class="w-full text-sm">
            <thead class="bg-slate-50"><tr>
                <th class="p-3 text-right">الرمز</th>
                <th class="p-3 text-right">طالب</th>
                <th class="p-3 text-right">معلم</th>
                <th class="p-3">موعد</th>
                <th class="p-3">نمط</th>
                <th class="p-3">حالة</th>
            </tr></thead>
            <tbody>
            <?php $__currentLoopData = $bookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr class="border-t">
                    <td class="p-3"><a href="<?php echo e(route('admin.tutor-lessons.bookings.show', $b)); ?>" class="text-violet-600 font-mono"><?php echo e($b->code); ?></a></td>
                    <td class="p-3"><?php echo e($b->student?->name); ?></td>
                    <td class="p-3"><?php echo e($b->instructor?->name); ?></td>
                    <td class="p-3"><?php echo e($b->scheduled_at?->format('Y-m-d H:i')); ?></td>
                    <td class="p-3"><?php echo e($b->matching_mode); ?></td>
                    <td class="p-3"><?php echo e($b->status); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
        <div class="p-4"><?php echo e($bookings->links()); ?></div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sana\resources\views\admin\tutor-lessons\bookings.blade.php ENDPATH**/ ?>