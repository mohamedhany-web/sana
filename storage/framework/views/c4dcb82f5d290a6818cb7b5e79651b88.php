<?php if (! $__env->hasRenderedOnce('55ee2aef-86bf-4ce7-8ebf-b38f6a5c4208')): $__env->markAsRenderedOnce('55ee2aef-86bf-4ce7-8ebf-b38f6a5c4208'); ?>
<style>
    <?php echo $__env->make('landing.eduvalt.brand-vars', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    .platform-brand {
        display: inline-flex;
        align-items: center;
        gap: 0.65rem;
        transition: opacity 0.2s;
    }
    .platform-brand:hover { opacity: 0.92; }
    .platform-brand__mark {
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        overflow: hidden;
    }
    .platform-brand__mark--img {
        width: 2.5rem;
        height: 2.5rem;
        box-shadow: 0 4px 14px -6px rgba(var(--edu-primary-rgb), 0.45);
    }
    .platform-brand__mark--img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .platform-brand__mark--letter {
        width: 2.5rem;
        height: 2.5rem;
        font-weight: 900;
        font-size: 1.05rem;
        color: #fff;
        background: linear-gradient(135deg, var(--edu-primary), var(--edu-accent-dark));
        box-shadow: 0 4px 14px -6px rgba(var(--edu-primary-rgb), 0.4);
    }
    .platform-brand__name {
        display: block;
        font-weight: 800;
        font-size: 1rem;
        line-height: 1.2;
        color: #0f172a;
    }
    .dark .platform-brand__name { color: #f1f5f9; }
    .platform-brand__tagline {
        display: block;
        font-size: 0.6875rem;
        font-weight: 600;
        color: var(--edu-muted);
        margin-top: 0.1rem;
        line-height: 1.3;
    }
    .platform-brand--sidebar .platform-brand__mark--img,
    .platform-brand--sidebar .platform-brand__mark--letter {
        width: 2.75rem;
        height: 2.75rem;
        border-radius: 14px;
    }
    .platform-brand--header .platform-brand__name { font-size: 0.9375rem; }
    .platform-brand--nav .platform-brand__mark--img,
    .platform-brand--nav .platform-brand__mark--letter {
        width: 2.5rem;
        height: 2.5rem;
    }
    .platform-brand--nav .platform-brand__name { font-size: 1.05rem; }
    .platform-brand--nav-stack .platform-brand__name { font-size: 1.125rem; }
    @media (min-width: 1024px) {
        .platform-brand--nav-stack .platform-brand__name { font-size: 1.25rem; }
    }
    @media (max-width: 639px) {
        .platform-brand--nav .platform-brand__text { display: none; }
    }
</style>
<?php endif; ?>

<?php
    $brandName = config('app.name', 'Sana');
    $logoUrl = $logoUrl ?? \App\Services\AdminPanelBranding::logoPublicUrl();
    $tagline = $tagline ?? trim((string) (\App\Services\PublicFooterSettings::payload()['brand_tagline'] ?? ''));
    $href = $href ?? route('home');
    $variant = $variant ?? 'sidebar';
    $showTagline = $showTagline ?? in_array($variant, ['sidebar', 'nav-stack'], true);
    $subtitle = $subtitle ?? '';
    $displayLine = '';
    if ($showTagline) {
        $displayLine = trim((string) $subtitle) !== '' ? trim((string) $subtitle) : $tagline;
    }
    $initial = mb_substr($brandName, 0, 1);
?>

<a href="<?php echo e($href); ?>" class="platform-brand platform-brand--<?php echo e($variant); ?> group no-underline text-inherit">
    <?php if($logoUrl): ?>
        <span class="platform-brand__mark platform-brand__mark--img">
            <img src="<?php echo e($logoUrl); ?>" alt="<?php echo e($brandName); ?>" decoding="async">
        </span>
    <?php else: ?>
        <span class="platform-brand__mark platform-brand__mark--letter"><?php echo e($initial); ?></span>
    <?php endif; ?>
    <span class="platform-brand__text min-w-0">
        <span class="platform-brand__name"><?php echo e($brandName); ?></span>
        <?php if($showTagline && $displayLine !== ''): ?>
            <span class="platform-brand__tagline"><?php echo e($displayLine); ?></span>
        <?php endif; ?>
    </span>
</a>
<?php /**PATH C:\xampp\htdocs\sana\resources\views/partials/platform-brand.blade.php ENDPATH**/ ?>