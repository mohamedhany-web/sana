<?php
    $steps = [
        ['icon' => 'fa-clipboard-check', 'title' => __('public.journey_step_1')],
        ['icon' => 'fa-user-check', 'title' => __('public.journey_step_2')],
        ['icon' => 'fa-video', 'title' => __('public.journey_step_3')],
        ['icon' => 'fa-chart-line', 'title' => __('public.journey_step_4')],
    ];
?>
<section class="sana-section sana-section--white" id="journey">
    <div class="sana-container">
        <div class="sana-head sana-reveal">
            <h2 class="sana-head__title"><?php echo e(__('public.journey_title')); ?> <span class="hl"><?php echo e(__('public.journey_highlight')); ?></span></h2>
            <span class="sana-head__line"></span>
        </div>
        <div class="sana-journey-m sana-reveal">
            <div class="sana-journey-m__line" aria-hidden="true"></div>
            <?php $__currentLoopData = $steps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="sana-journey-m__step">
                <div class="sana-journey-m__icon"><i class="fas <?php echo e($step['icon']); ?>"></i></div>
                <span><?php echo e($step['title']); ?></span>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <div class="sana-reveal" style="margin-top:28px;display:flex;flex-direction:column;align-items:center;gap:12px">
            <?php echo $__env->make('landing.sana.partials.site-cta-buttons', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <a href="<?php echo e(route('public.how_it_works')); ?>" class="sana-link-more"><?php echo e(__('public.how_it_works_page_title')); ?> <i class="fas fa-arrow-left"></i></a>
        </div>
    </div>
</section>
<?php /**PATH C:\xampp\htdocs\sana\resources\views\landing\sana\sections\journey.blade.php ENDPATH**/ ?>