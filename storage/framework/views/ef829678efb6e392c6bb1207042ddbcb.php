<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'title',
    'subtitle' => null,
    'icon' => 'fas fa-layer-group',
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'title',
    'subtitle' => null,
    'icon' => 'fas fa-layer-group',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<div class="admin-dashboard-hero animate-fade-in">
    <div class="admin-dashboard-hero-inner flex flex-col lg:flex-row lg:items-center justify-between gap-5">
        <div class="flex items-start gap-4 min-w-0">
            <span class="admin-page-hero__icon shrink-0">
                <i class="<?php echo e($icon); ?>"></i>
            </span>
            <div class="min-w-0">
                <h1 class="hero-title text-xl sm:text-2xl font-heading font-bold"><?php echo e($title); ?></h1>
                <?php if($subtitle): ?>
                    <p class="hero-sub text-sm mt-1.5 max-w-2xl leading-relaxed"><?php echo e($subtitle); ?></p>
                <?php endif; ?>
            </div>
        </div>
        <?php if(trim($slot) !== ''): ?>
            <div class="flex flex-wrap items-center gap-2 shrink-0">
                <?php echo e($slot); ?>

            </div>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\sana\resources\views\components\admin\page-hero.blade.php ENDPATH**/ ?>