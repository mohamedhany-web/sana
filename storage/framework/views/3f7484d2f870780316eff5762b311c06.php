<?php if($errors->any()): ?>
    <div class="admin-alert" style="background: #fef2f2; border-color: #fecaca; color: #991b1b;">
        <span class="admin-alert__icon" style="background: #fee2e2; color: #dc2626;"><i class="fas fa-exclamation-circle"></i></span>
        <div>
            <p class="font-bold mb-1">يرجى تصحيح ما يلي:</p>
            <ul class="list-disc list-inside space-y-0.5 text-sm font-medium">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $err): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($err); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    </div>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\sana\resources\views/admin/partials/alert-errors.blade.php ENDPATH**/ ?>