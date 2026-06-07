
<?php
    $fmtPrice = $fmtPrice ?? fn ($v) => number_format((float) $v, 0);
    $studentPlans = $studentPlans ?? [];
?>
<div class="md:col-span-2 space-y-4" x-show="isStudent()" x-cloak>
    <input type="hidden" name="teacher_plan_key" :disabled="!isStudent()"
           :value="studentPackageMode === 'custom' ? '' : selectedPlan">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h2 class="text-sm font-bold text-slate-900">باقة حصص الطالب مع المعلم</h2>
            <p class="text-xs text-slate-600 mt-1">اختر قالباً جاهزاً أو أنشئ باقة مخصصة (ساعات، سعر، مدة). تُفعَّل الساعات على ملف الطالب عند الحفظ.</p>
        </div>
        <a href="<?php echo e(route('admin.tutor-lessons.settings')); ?>" class="text-xs font-semibold text-violet-700 hover:underline shrink-0">
            تعديل القوالب في الإعدادات
        </a>
    </div>

    <div class="flex flex-wrap gap-2">
        <button type="button" @click="setStudentPackageMode('template')"
                :class="studentPackageMode === 'template' ? 'bg-violet-600 text-white border-violet-600' : 'bg-white text-slate-700 border-slate-200 hover:border-violet-300'"
                class="px-4 py-2 rounded-xl text-sm font-bold border transition-colors">
            باقات أساسية
        </button>
        <button type="button" @click="setStudentPackageMode('custom')"
                :class="studentPackageMode === 'custom' ? 'bg-violet-600 text-white border-violet-600' : 'bg-white text-slate-700 border-slate-200 hover:border-violet-300'"
                class="px-4 py-2 rounded-xl text-sm font-bold border transition-colors">
            باقة مخصصة
        </button>
    </div>

    <div x-show="studentPackageMode === 'template'" class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <?php $__currentLoopData = $studentPlans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $planKey => $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $hours = (int) ($plan['limits']['tutor_lesson_hours'] ?? 0);
                $badge = trim((string) ($plan['card_badge'] ?? ''));
            ?>
            <button type="button" @click="applyStudentPlan('<?php echo e($planKey); ?>')"
                    :class="selectedPlan === '<?php echo e($planKey); ?>' ? 'ring-2 ring-violet-500 border-violet-400 bg-violet-50/80' : 'border-slate-200 bg-white hover:border-violet-300'"
                    class="text-right rounded-2xl border p-4 shadow-sm transition-all">
                <?php if($badge !== ''): ?>
                    <span class="inline-block mb-2 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800"><?php echo e($badge); ?></span>
                <?php endif; ?>
                <p class="text-base font-black text-slate-900"><?php echo e($plan['label'] ?? $planKey); ?></p>
                <p class="text-xs text-slate-600 mt-1 leading-relaxed"><?php echo e($plan['card_subtitle'] ?? ''); ?></p>
                <p class="text-2xl font-black text-violet-700 mt-3">
                    <?php echo e($fmtPrice($plan['price'] ?? 0)); ?>

                    <span class="text-sm font-semibold text-slate-500"><?php echo e(__('public.currency')); ?></span>
                </p>
                <p class="text-xs text-slate-500 mt-1"><?php echo e($plan['card_price_hint'] ?? ''); ?></p>
                <p class="text-xs font-bold text-violet-800 mt-3 flex items-center gap-1">
                    <i class="fas fa-clock"></i>
                    <?php echo e($hours); ?> ساعة حصص / شهر
                </p>
            </button>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <div x-show="studentPackageMode === 'custom'" class="rounded-xl border border-dashed border-violet-300 bg-violet-50/30 p-4">
        <p class="text-sm font-bold text-violet-900 mb-1">باقة مخصصة</p>
        <p class="text-xs text-violet-800/90">عدّل اسم الخطة، السعر، ساعات الحصص، وتواريخ البداية والانتهاء في الحقول أدناه.</p>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\sana\resources\views\admin\subscriptions\_student-package-picker.blade.php ENDPATH**/ ?>