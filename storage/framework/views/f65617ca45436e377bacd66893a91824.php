<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'course',
    'size' => 'default',
    'asButton' => false,
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
    'course',
    'size' => 'default',
    'asButton' => false,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
    /** @var \App\Models\AdvancedCourse $course */
    $small = $size === 'sm';
    if ($course->usesContactSupportPricing()) {
        $waUrl = $course->supportWhatsAppUrl();
        $btnClass = $small
            ? 'inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-[#25D366] text-white text-xs font-bold hover:bg-[#1da851] transition-colors'
            : 'inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-[#25D366] text-white text-sm font-bold hover:bg-[#1da851] transition-colors shadow-sm';
    } else {
        $list = (float) ($course->price ?? 0);
        $pay = $course->effectivePurchasePrice();
        $promo = $course->hasPromotionalPrice();
        $isFree = ($course->is_free ?? false) || ($list <= 0 && $pay <= 0);
    }
?>
<?php if($course->usesContactSupportPricing()): ?>
    <a href="<?php echo e($waUrl); ?>" target="_blank" rel="noopener noreferrer"
       <?php echo e($attributes->class([$btnClass])); ?>

       onclick="event.stopPropagation();">
        <i class="fab fa-whatsapp <?php echo e($small ? 'text-sm' : 'text-base'); ?>"></i>
        <?php echo e(__('public.course_contact_support')); ?>

    </a>
<?php elseif($isFree): ?>
    <span <?php echo e($attributes->class(['inline-flex items-center gap-1 font-bold text-emerald-600', $small ? 'text-xs' : 'text-sm'])); ?>>
        <i class="fas fa-gift <?php echo e($small ? 'text-[9px]' : 'text-[10px]'); ?>"></i>
        <?php echo e(__('public.free_price')); ?>

    </span>
<?php elseif($promo): ?>
    <span <?php echo e($attributes->class(['inline-flex flex-col items-end gap-0.5'])); ?>>
        <span class="<?php echo e($small ? 'text-[10px]' : 'text-xs'); ?> text-slate-400 line-through tabular-nums"><?php echo e(number_format($list, 0)); ?> <?php echo e(__('public.currency')); ?></span>
        <span class="<?php echo e($small ? 'text-xs font-bold' : 'text-sm font-black'); ?> text-mx-orange tabular-nums"><?php echo e(number_format($pay, 0)); ?> <?php echo e(__('public.currency')); ?></span>
    </span>
<?php else: ?>
    <span <?php echo e($attributes->class(['font-bold text-mx-orange tabular-nums', $small ? 'text-xs' : 'text-sm'])); ?>>
        <?php echo e(number_format($pay, 0)); ?> <?php echo e(__('public.currency')); ?>

    </span>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\sana\resources\views\components\advanced-course-card-price.blade.php ENDPATH**/ ?>