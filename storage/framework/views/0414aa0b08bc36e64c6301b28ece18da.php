<?php if(session('success')): ?>
    <div class="admin-alert admin-alert--success">
        <span class="admin-alert__icon"><i class="fas fa-check"></i></span>
        <p class="font-semibold"><?php echo e(session('success')); ?></p>
    </div>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\sana\resources\views\admin\partials\alert-success.blade.php ENDPATH**/ ?>