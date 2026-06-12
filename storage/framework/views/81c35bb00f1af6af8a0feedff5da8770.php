<?php
    $pricingMode = old('pricing_mode', ($advancedCourse->contact_support_for_pricing ?? false) ? 'contact_support' : 'price');
?>
<div class="md:col-span-2 rounded-2xl border border-slate-200 bg-slate-50/80 p-5 space-y-4"
     x-data="{ pricingMode: <?php echo \Illuminate\Support\Js::from($pricingMode)->toHtml() ?> }">
    <div>
        <p class="text-sm font-bold text-slate-800 mb-1">طريقة عرض السعر على بطاقة الكورس</p>
        <p class="text-xs text-slate-500">اختر إما إظهار السعر للشراء المباشر، أو زر واتساب للتواصل مع الدعم الفني.</p>
    </div>

    <div class="flex flex-col sm:flex-row gap-3">
        <label class="flex-1 flex items-start gap-3 p-4 rounded-xl border-2 cursor-pointer transition-colors"
               :class="pricingMode === 'price' ? 'border-sky-500 bg-white' : 'border-slate-200 bg-white/60'">
            <input type="radio" name="pricing_mode" value="price" class="mt-1 accent-sky-600"
                   x-model="pricingMode" <?php echo e($pricingMode === 'price' ? 'checked' : ''); ?>>
            <span>
                <span class="block text-sm font-bold text-slate-800">عرض السعر</span>
                <span class="block text-xs text-slate-500 mt-1">يظهر السعر على البطاقة ويمكن الشراء من الموقع.</span>
            </span>
        </label>
        <label class="flex-1 flex items-start gap-3 p-4 rounded-xl border-2 cursor-pointer transition-colors"
               :class="pricingMode === 'contact_support' ? 'border-emerald-500 bg-white' : 'border-slate-200 bg-white/60'">
            <input type="radio" name="pricing_mode" value="contact_support" class="mt-1 accent-emerald-600"
                   x-model="pricingMode" <?php echo e($pricingMode === 'contact_support' ? 'checked' : ''); ?>>
            <span>
                <span class="block text-sm font-bold text-slate-800">
                    <i class="fab fa-whatsapp text-emerald-600 ml-1"></i>
                    التواصل مع الدعم (واتساب)
                </span>
                <span class="block text-xs text-slate-500 mt-1">يُخفى السعر ويظهر زر واتساب على بطاقة الكورس وصفحة التفاصيل.</span>
            </span>
        </label>
    </div>

    <div x-show="pricingMode === 'price'" x-cloak class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="space-y-2">
            <label class="block text-sm font-semibold text-slate-700">سعر الكورس الأساسي (قبل الخصم — <?php echo e(__('public.currency_name')); ?>)</label>
            <input type="number" name="price" value="<?php echo e(old('price', $advancedCourse->price ?? 0)); ?>" min="0" step="0.01"
                   class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-slate-800 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition"
                   placeholder="0 للمجاني">
            <?php $__errorArgs = ['price'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-rose-500 mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <div class="space-y-2">
            <label class="block text-sm font-semibold text-slate-700">سعر بعد الخصم (اختياري)</label>
            <input type="number" name="price_after_discount" value="<?php echo e(old('price_after_discount', $advancedCourse->price_after_discount ?? null)); ?>" min="0" step="0.01"
                   class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-slate-800 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition"
                   placeholder="اتركه فارغاً إن لم يكن هناك عرض">
            <p class="text-xs text-slate-500">يُعرض على البطاقات كسعر قبل وبعد. الدفع يُحسب على سعر الشراء الفعلي.</p>
            <?php $__errorArgs = ['price_after_discount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-rose-500 mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
    </div>

    <div x-show="pricingMode === 'contact_support'" x-cloak
         class="flex items-start gap-3 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-sm text-emerald-900">
        <i class="fab fa-whatsapp text-2xl text-emerald-600 shrink-0 mt-0.5"></i>
        <p>سيُستخدم رابط واتساب الدعم من إعدادات الموقع (الفوتر). عند الضغط على البطاقة يفتح واتساب مع رسالة تتضمن اسم الكورس.</p>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\sana\resources\views\admin\advanced-courses\partials\pricing-mode.blade.php ENDPATH**/ ?>