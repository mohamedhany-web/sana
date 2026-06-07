<?php
    $studentPlans = $studentPlans ?? \App\Services\StudentSubscriptionPlansService::getPlans();
    $cycleLabels = \App\Models\Subscription::billingCycleLabels();
?>
<section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden mt-6">
    <div class="px-6 py-4 border-b border-slate-200 bg-gradient-to-l from-violet-50 to-white">
        <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
            <i class="fas fa-layer-group text-violet-500"></i>
            قوالب باقات اشتراك الطلاب
        </h3>
        <p class="text-xs text-slate-600 mt-1">تظهر في صفحة «إضافة اشتراك» عند اختيار طالب. الساعات تُنسخ إلى ملف الطالب عند تفعيل الاشتراك.</p>
    </div>

    <form method="post" action="<?php echo e(route('admin.tutor-lessons.student-plans.update')); ?>" class="p-6 space-y-6" data-turbo="false">
        <?php echo csrf_field(); ?>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
            <?php $__currentLoopData = $studentPlans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $planKey => $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="rounded-xl border border-slate-200 p-4 space-y-3 bg-slate-50/40">
                    <p class="text-xs font-bold text-violet-700 uppercase tracking-wide"><?php echo e($planKey); ?></p>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">اسم الباقة</label>
                        <input type="text" name="plans[<?php echo e($planKey); ?>][label]" required maxlength="120"
                               value="<?php echo e(old("plans.{$planKey}.label", $plan['label'] ?? '')); ?>"
                               class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm">
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">السعر</label>
                            <input type="number" name="plans[<?php echo e($planKey); ?>][price]" min="0" step="0.01" required
                                   value="<?php echo e(old("plans.{$planKey}.price", $plan['price'] ?? 0)); ?>"
                                   class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">ساعات الحصص</label>
                            <input type="number" name="plans[<?php echo e($planKey); ?>][limits][tutor_lesson_hours]" min="0" max="10000" required
                                   value="<?php echo e(old("plans.{$planKey}.limits.tutor_lesson_hours", $plan['limits']['tutor_lesson_hours'] ?? 0)); ?>"
                                   class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">دورة الفوترة</label>
                        <select name="plans[<?php echo e($planKey); ?>][billing_cycle]" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm">
                            <?php $__currentLoopData = $cycleLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $lbl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($val); ?>" <?php if(old("plans.{$planKey}.billing_cycle", $plan['billing_cycle'] ?? 'monthly') === $val): echo 'selected'; endif; ?>><?php echo e($lbl); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">وصف البطاقة</label>
                        <input type="text" name="plans[<?php echo e($planKey); ?>][card_subtitle]" maxlength="200"
                               value="<?php echo e(old("plans.{$planKey}.card_subtitle", $plan['card_subtitle'] ?? '')); ?>"
                               class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm">
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">شارة (اختياري)</label>
                            <input type="text" name="plans[<?php echo e($planKey); ?>][card_badge]" maxlength="40"
                                   value="<?php echo e(old("plans.{$planKey}.card_badge", $plan['card_badge'] ?? '')); ?>"
                                   class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">تلميح السعر</label>
                            <input type="text" name="plans[<?php echo e($planKey); ?>][card_price_hint]" maxlength="80"
                                   value="<?php echo e(old("plans.{$planKey}.card_price_hint", $plan['card_price_hint'] ?? '')); ?>"
                                   class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm">
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-violet-600 text-white text-sm font-bold hover:bg-violet-700 transition-colors">
            <i class="fas fa-save"></i>
            حفظ قوالب باقات الطلاب
        </button>
    </form>
</section>
<?php /**PATH C:\xampp\htdocs\sana\resources\views\admin\tutor-lessons\_student-plans-form.blade.php ENDPATH**/ ?>