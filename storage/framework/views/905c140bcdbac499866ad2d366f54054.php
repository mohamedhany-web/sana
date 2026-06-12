<?php $__env->startSection('title', 'طلبات المساعدة'); ?>
<?php $__env->startSection('header', 'طلبات المساعدة في إيجاد معلم'); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <?php echo $__env->make('admin.tutor-lessons._nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<div class="space-y-3">
    <?php $__currentLoopData = $requests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e(route('admin.tutor-lessons.assisted.show', $r)); ?>" class="block bg-white border rounded-xl p-4 hover:border-blue-300">
            <strong><?php echo e($r->code); ?></strong> — <?php echo e($r->student?->name); ?> <span class="text-slate-500 text-sm"><?php echo e($r->statusLabel()); ?></span>
        </a>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php echo e($requests->links()); ?>

</div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sana\resources\views\admin\tutor-lessons\assisted-index.blade.php ENDPATH**/ ?>