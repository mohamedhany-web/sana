<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['title', 'subtitle' => null, 'icon' => 'fas fa-chart-pie', 'tone' => 'primary']));

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

foreach (array_filter((['title', 'subtitle' => null, 'icon' => 'fas fa-chart-pie', 'tone' => 'primary']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<div class="dash-section__head">
    <span class="dash-section__icon dash-section__icon--<?php echo e($tone); ?>">
        <i class="<?php echo e($icon); ?>"></i>
    </span>
    <div class="min-w-0">
        <h2 class="dash-section__title"><?php echo e($title); ?></h2>
        <?php if($subtitle): ?>
            <p class="dash-section__sub"><?php echo e($subtitle); ?></p>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\sana\resources\views\admin\dashboard\partials\section-heading.blade.php ENDPATH**/ ?>