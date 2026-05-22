<?php if(session('info')): ?>
    <div class="admin-alert" style="background: rgba(29, 78, 219, 0.08); border-color: rgba(29, 78, 219, 0.2); color: #1e40af;">
        <span class="admin-alert__icon" style="background: rgba(29, 78, 219, 0.12); color: var(--admin-primary);"><i class="fas fa-info-circle"></i></span>
        <p class="font-semibold"><?php echo e(session('info')); ?></p>
    </div>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\sana\resources\views/admin/partials/alert-info.blade.php ENDPATH**/ ?>