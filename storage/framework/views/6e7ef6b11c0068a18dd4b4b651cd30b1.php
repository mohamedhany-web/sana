<?php
    $logoUrl = $adminPanelLogoUrl ?? \App\Services\AdminPanelBranding::logoPublicUrl();
    $brand = config('app.name', 'Sana');
    $letter = mb_substr($brand, 0, 1);
?>
<a href="<?php echo e(route('home')); ?>" class="auth-brand">
    <span class="auth-brand-logo">
        <?php if($logoUrl): ?>
            <img src="<?php echo e($logoUrl); ?>" alt="<?php echo e($brand); ?>" width="48" height="48" loading="eager">
        <?php else: ?>
            <?php echo e($letter); ?>

        <?php endif; ?>
    </span>
    <span class="auth-brand-name"><?php echo e($brand); ?></span>
</a>
<?php /**PATH C:\xampp\htdocs\sana\resources\views/auth/partials/eduvalt-brand.blade.php ENDPATH**/ ?>