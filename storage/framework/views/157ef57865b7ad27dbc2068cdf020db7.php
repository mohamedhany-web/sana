<?php
    $geoBrandLogoUrl = $adminPanelLogoUrl ?? \App\Services\AdminPanelBranding::logoPublicUrl();
    $geoBrandName = config('app.name', 'Sana');
    $geoBrandSize = $geoBrandSize ?? 'mark';
    $geoBrandLink = $geoBrandLink ?? ($geoBrandSize === 'nav');
    $geoBrandShowName = $geoBrandShowName ?? ($geoBrandSize === 'nav');
?>

<?php if($geoBrandSize === 'mark'): ?>
    <div class="geo-brand-mark" aria-hidden="true">
        <?php if($geoBrandLogoUrl): ?>
            <img src="<?php echo e($geoBrandLogoUrl); ?>" alt="<?php echo e($geoBrandName); ?>" class="geo-brand-img geo-brand-img--mark" loading="eager" decoding="async">
        <?php else: ?>
            <div class="geo-login-hero">
                <div class="geo-login-hero-ring"></div>
                <div class="geo-login-hero-ring"></div>
                <div class="geo-login-hero-core"></div>
            </div>
        <?php endif; ?>
    </div>
<?php else: ?>
    <?php if($geoBrandLink): ?>
        <a href="<?php echo e(route('home')); ?>" class="geo-logo geo-logo--brand">
    <?php else: ?>
        <span class="geo-logo geo-logo--brand">
    <?php endif; ?>
        <?php if($geoBrandLogoUrl): ?>
            <span class="geo-brand-mark geo-brand-mark--nav">
                <img src="<?php echo e($geoBrandLogoUrl); ?>" alt="<?php echo e($geoBrandName); ?>" class="geo-brand-img geo-brand-img--nav" loading="eager" decoding="async">
            </span>
        <?php endif; ?>
        <?php if($geoBrandShowName): ?>
            <span><?php echo e($geoBrandName); ?></span>
        <?php endif; ?>
    <?php if($geoBrandLink): ?>
        </a>
    <?php else: ?>
        </span>
    <?php endif; ?>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\sana\resources\views\auth\partials\geo-brand-logo.blade.php ENDPATH**/ ?>