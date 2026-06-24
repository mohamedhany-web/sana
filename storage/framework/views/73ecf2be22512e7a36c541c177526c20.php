<?php
    $cta = $cta ?? \App\Support\PublicSiteCta::payload();
    $size = $size ?? 'lg';
    $hero = !empty($hero);
    $primaryLabel = $hero ? $cta['primary_label_hero'] : $cta['primary_label'];
    $sizeClass = $size === 'lg' ? ' sana-btn--lg' : '';
    $waExternal = $cta['has_whatsapp'];
?>
<div class="sana-site-cta <?php echo e($class ?? ''); ?>">
    <a href="<?php echo e($cta['assessment_url']); ?>" class="sana-btn sana-btn--yellow<?php echo e($sizeClass); ?>">
        <i class="fas fa-clipboard-check"></i> <?php echo e($primaryLabel); ?>

    </a>
    <a href="<?php echo e($cta['whatsapp_url']); ?>" <?php if($waExternal): ?> target="_blank" rel="noopener noreferrer" <?php endif; ?> class="sana-btn sana-btn--wa<?php echo e($sizeClass); ?>">
        <i class="fab fa-whatsapp"></i> <?php echo e($cta['secondary_label']); ?>

    </a>
</div>
<?php /**PATH C:\xampp\htdocs\sana\resources\views\landing\sana\partials\site-cta-buttons.blade.php ENDPATH**/ ?>