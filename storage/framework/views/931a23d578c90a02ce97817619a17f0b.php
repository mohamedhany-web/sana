<?php
    $compact = !empty($compact);
    $cta = \App\Support\PublicSiteCta::payload();
    $studentSteps = [
        __('public.audience_student_step_1'),
        __('public.audience_student_step_2'),
        __('public.audience_student_step_3'),
    ];
    $teacherSteps = [
        __('public.audience_teacher_step_1'),
        __('public.audience_teacher_step_2'),
        __('public.audience_teacher_step_3'),
    ];
?>
<div class="sana-audience <?php echo e($compact ? 'sana-audience--compact' : ''); ?>">
    <?php if (! ($compact)): ?>
    <div class="sana-head sana-head--center sana-reveal" style="margin-bottom:28px">
        <h2 class="sana-head__title"><?php echo e(__('public.audience_section_title')); ?></h2>
        <span class="sana-head__line"></span>
        <p class="sana-head__sub"><?php echo e(__('public.audience_section_sub')); ?></p>
    </div>
    <?php endif; ?>
    <div class="sana-audience__grid sana-reveal">
        <article class="sana-audience__card sana-audience__card--student">
            <div class="sana-audience__card-head">
                <span class="sana-audience__badge"><i class="fas fa-child-reaching"></i> <?php echo e(__('public.audience_student_badge')); ?></span>
                <h3><?php echo e(__('public.audience_student_title')); ?></h3>
                <p><?php echo e(__('public.audience_student_desc')); ?></p>
            </div>
            <ol class="sana-audience__steps" aria-label="خطوات العائلات">
                <?php $__currentLoopData = $studentSteps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><i class="fas fa-check" aria-hidden="true"></i> <?php echo e($step); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ol>
            <div class="sana-audience__card-foot">
                <?php echo $__env->make('landing.sana.partials.site-cta-buttons', ['hero' => true, 'class' => 'sana-site-cta--stack sana-audience__cta'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <p class="sana-audience__foot-link">
                    <a href="<?php echo e($cta['how_it_works_url']); ?>"><i class="fas fa-circle-info"></i> <?php echo e(__('public.how_it_works_page_title')); ?></a>
                </p>
            </div>
        </article>
        <article class="sana-audience__card sana-audience__card--teacher">
            <div class="sana-audience__card-head">
                <span class="sana-audience__badge"><i class="fas fa-chalkboard-user"></i> <?php echo e(__('public.audience_teacher_badge')); ?></span>
                <h3><?php echo e(__('public.audience_teacher_title')); ?></h3>
                <p><?php echo e(__('public.audience_teacher_desc')); ?></p>
            </div>
            <ol class="sana-audience__steps" aria-label="خطوات المعلّمين">
                <?php $__currentLoopData = $teacherSteps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><i class="fas fa-check" aria-hidden="true"></i> <?php echo e($step); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ol>
            <div class="sana-audience__card-foot">
                <a href="<?php echo e(route('tutor.apply')); ?>" class="sana-btn sana-btn--purple sana-btn--lg sana-audience__cta">
                    <i class="fas fa-chalkboard-teacher"></i> <?php echo e(__('public.audience_teacher_cta')); ?>
                </a>
                <p class="sana-audience__foot-link">
                    <a href="<?php echo e(route('tutor.policy')); ?>"><i class="fas fa-file-contract"></i> <?php echo e(__('public.audience_teacher_link_policy')); ?></a>
                </p>
            </div>
        </article>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\sana\resources\views\landing\sana\partials\audience-paths.blade.php ENDPATH**/ ?>