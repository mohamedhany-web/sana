<?php
    $steps = [
        ['icon' => '📝', 'title' => 'سجّل حسابك'],
        ['icon' => '📚', 'title' => 'اختر الدورة'],
        ['icon' => '🎬', 'title' => 'ابدأ التعلّم'],
        ['icon' => '💬', 'title' => 'تفاعل وتمرّن'],
        ['icon' => '📜', 'title' => 'احصل على الشهادة'],
        ['icon' => '🏆', 'title' => 'حقّق هدفك'],
    ];
?>
<section class="sana-section sana-section--white" id="journey">
    <div class="sana-container">
        <div class="sana-head sana-reveal">
            <h2 class="sana-head__title">رحلتك التعليمية <span class="hl">في 6 خطوات</span></h2>
            <span class="sana-head__line"></span>
        </div>
        <div class="sana-journey-m sana-reveal">
            <div class="sana-journey-m__line" aria-hidden="true"></div>
            <?php $__currentLoopData = $steps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="sana-journey-m__step">
                <div class="sana-journey-m__icon"><?php echo e($step['icon']); ?></div>
                <span><?php echo e($step['title']); ?></span>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>
<?php /**PATH C:\xampp\htdocs\sana\resources\views\landing\sana\sections\journey.blade.php ENDPATH**/ ?>