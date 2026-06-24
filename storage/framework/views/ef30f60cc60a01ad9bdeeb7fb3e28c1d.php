<?php $__env->startSection('title', 'معلمو الحصص'); ?>
<?php $__env->startSection('header', 'معلمو الحصص'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <?php echo $__env->make('admin.tutor-lessons._nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <div class="bg-white rounded-xl border overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50"><tr><th class="p-3 text-right">المعلم</th><th class="p-3">الحالة</th><th class="p-3">تفعيل</th></tr></thead>
            <tbody>
            <?php $__currentLoopData = $profiles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr class="border-t">
                    <td class="p-3 font-semibold"><?php echo e($p->user?->name); ?></td>
                    <td class="p-3"><?php echo e($p->isTutorActivated() ? 'مفعّل' : 'قيد التفعيل'); ?></td>
                    <td class="p-3">
                        <?php if(!$p->isTutorActivated()): ?>
                        <form method="post" action="<?php echo e(route('admin.tutor-lessons.instructors.activate', $p)); ?>"><?php echo csrf_field(); ?><button class="text-blue-700 font-bold">تفعيل</button></form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
        <?php echo e($profiles->links()); ?>

    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sana\resources\views\admin\tutor-lessons\instructors.blade.php ENDPATH**/ ?>