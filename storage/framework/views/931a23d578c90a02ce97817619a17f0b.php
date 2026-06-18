<?php
    $compact = !empty($compact);
    $cta = \App\Support\PublicSiteCta::payload();
    $studentSteps = [
        ['icon' => 'fa-clipboard-check', 'text' => __('public.audience_student_step_1')],
        ['icon' => 'fa-user-check', 'text' => __('public.audience_student_step_2')],
        ['icon' => 'fa-graduation-cap', 'text' => __('public.audience_student_step_3')],
    ];
?>

<?php if($compact): ?>
<div class="sana-audience sana-audience--compact">
    <div class="sana-audience__grid sana-audience__grid--single sana-reveal">
        <article class="sana-audience__card sana-audience__card--student">
            <div class="sana-audience__card-head">
                <span class="sana-audience__badge"><i class="fas fa-child-reaching"></i> <?php echo e(__('public.audience_student_badge')); ?></span>
                <h3><?php echo e(__('public.audience_student_title')); ?></h3>
                <p><?php echo e(__('public.audience_student_desc')); ?></p>
            </div>
            <ol class="sana-audience__steps" aria-label="خطوات العائلات">
                <?php $__currentLoopData = $studentSteps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><i class="fas fa-check" aria-hidden="true"></i> <?php echo e($step['text']); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ol>
            <div class="sana-audience__card-foot">
                <?php echo $__env->make('landing.sana.partials.site-cta-buttons', ['hero' => true, 'class' => 'sana-site-cta--stack sana-audience__cta'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </div>
        </article>
    </div>
</div>
<?php else: ?>
<div class="sana-paths-band sana-paths-band--wide sana-reveal">
    <div class="sana-paths-band__dots" aria-hidden="true"></div>
    <div class="sana-paths-band__glow sana-paths-band__glow--1" aria-hidden="true"></div>
    <div class="sana-paths-band__glow sana-paths-band__glow--2" aria-hidden="true"></div>

    <div class="sana-paths-band__shell">
        <div class="sana-paths-band__row-top">
            <div class="sana-paths-band__main">
                <span class="sana-paths-band__eyebrow"><i class="fas fa-child-reaching"></i> <?php echo e(__('public.audience_student_badge')); ?></span>
                <h2 class="sana-paths-band__title"><?php echo e(__('public.audience_section_title')); ?></h2>
                <p class="sana-paths-band__sub"><?php echo e(__('public.audience_section_sub')); ?></p>
                <div class="sana-paths-band__meta">
                    <h3><?php echo e(__('public.audience_student_title')); ?></h3>
                    <p><?php echo e(__('public.audience_student_desc')); ?></p>
                </div>
            </div>

            <div class="sana-paths-band__actions">
                <?php echo $__env->make('landing.sana.partials.site-cta-buttons', ['hero' => true, 'class' => 'sana-paths-band__cta'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <a href="<?php echo e($cta['how_it_works_url']); ?>" class="sana-paths-band__link">
                    <i class="fas fa-circle-info"></i> <?php echo e(__('public.how_it_works_page_title')); ?>

                </a>
            </div>
        </div>

        <div class="sana-paths-band__steps" aria-label="خطوات العائلات">
            <?php $__currentLoopData = $studentSteps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <article class="sana-paths-band__step">
                <span class="sana-paths-band__step-num">الخطوة <?php echo e($index + 1); ?></span>
                <div class="sana-paths-band__step-icon"><i class="fas <?php echo e($step['icon']); ?>"></i></div>
                <p class="sana-paths-band__step-text"><?php echo e($step['text']); ?></p>
            </article>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</div>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\sana\resources\views\landing\sana\partials\audience-paths.blade.php ENDPATH**/ ?>