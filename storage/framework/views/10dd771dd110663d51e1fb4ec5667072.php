<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'courseId',
    'saved' => false,
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
    'courseId',
    'saved' => false,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<button
    type="button"
    class="edu-course-fav-btn <?php echo e($saved ? 'is-saved' : ''); ?>"
    data-course-fav
    data-course-id="<?php echo e((int) $courseId); ?>"
    data-saved="<?php echo e($saved ? '1' : '0'); ?>"
    aria-pressed="<?php echo e($saved ? 'true' : 'false'); ?>"
    aria-label="<?php echo e($saved ? __('public.course_unsave') : __('public.course_save')); ?>"
    title="<?php echo e($saved ? __('public.course_unsave') : __('public.course_save')); ?>"
    onclick="SanaCourseFavorites.handleClick(event)"
>
    <i class="fa-heart <?php echo e($saved ? 'fas' : 'far'); ?>"></i>
</button>
<?php /**PATH C:\xampp\htdocs\sana\resources\views\components\course-favorite-button.blade.php ENDPATH**/ ?>