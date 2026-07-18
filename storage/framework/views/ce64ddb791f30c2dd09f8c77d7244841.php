<?php
    $formSteps = $formSteps ?? collect();
    $oldWeekly = old('weekly_availability', []);
?>

<?php $__currentLoopData = $formSteps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stepIndex => $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<?php
    $stepNum = $stepIndex + 1;
    $isLast = $stepNum === $formSteps->count();
    $isIntro = $step->step_type === 'intro';
?>
<div x-show="step === <?php echo e($stepNum); ?>" x-cloak class="ix-step-panel space-y-4" data-tutor-step="<?php echo e($stepNum); ?>">
    <h2 class="ta-headline" style="font-size:1.5rem"><?php echo e($step->title); ?></h2>
    <?php if($step->description): ?>
        <p class="text-sm text-slate-600 m-0"><?php echo e($step->description); ?></p>
    <?php endif; ?>

    <?php if($isIntro): ?>
        <div class="rounded-xl bg-sky-50 border border-sky-100 p-4 text-sm text-sky-900 space-y-2">
            <ul class="m-0 pr-4 space-y-1">
                <li>بياناتك الشخصية والمؤهلات</li>
                <li>التخصصات والمناهج والتوفر</li>
                <li>فيديو الشرح والمستندات</li>
            </ul>
            <p class="m-0 text-xs"><a href="<?php echo e(route('tutor.policy')); ?>" class="text-sky-700 font-bold" target="_blank" rel="noopener">اطّلع على سياسة انضمام المعلمين</a></p>
        </div>
        <div class="ta-actions">
            <button type="button" class="ta-btn-primary" @click="next()">ابدأ التقديم</button>
        </div>
    <?php else: ?>
        <div class="grid sm:grid-cols-2 gap-4">
            <?php $__currentLoopData = $step->activeFields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php echo $__env->make('tutor.partials.field-renderer', [
                    'field' => $field,
                    'subjects' => $subjects,
                    'years' => $years,
                    'phoneCountries' => $phoneCountries,
                    'defaultCountry' => $defaultCountry,
                    'formOptions' => $formOptions,
                    'oldWeekly' => $oldWeekly,
                ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <div class="ta-actions flex flex-wrap gap-2">
            <?php if($stepNum > 1): ?>
                <button type="button" class="ta-btn-ghost" @click="prev()">السابق</button>
            <?php endif; ?>
            <?php if($isLast): ?>
                <button type="submit" class="ta-btn-primary" :disabled="submitting">
                    <span x-text="submitting ? 'جاري الإرسال...' : 'إرسال الطلب'"></span>
                    <i class="fas fa-paper-plane" x-show="!submitting"></i>
                </button>
            <?php else: ?>
                <button type="button" class="ta-btn-primary" @click="next()">التالي</button>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php /**PATH C:\xampp\htdocs\sana\resources\views/tutor/partials/apply-steps-dynamic.blade.php ENDPATH**/ ?>