@php
    $pricingMode = old('pricing_mode', ($advancedCourse->contact_support_for_pricing ?? false) ? 'contact_support' : 'price');
@endphp
<div class="md:col-span-2 rounded-2xl border border-slate-200 dark:border-slate-600 bg-slate-50/80 dark:bg-slate-800/50 p-5 space-y-4"
     x-data="{ pricingMode: @js($pricingMode) }">
    <div>
        <p class="text-sm font-bold text-slate-800 dark:text-slate-100 mb-1">طريقة عرض السعر على بطاقة الكورس</p>
        <p class="text-xs text-slate-500 dark:text-slate-400">اختر إما إظهار السعر للشراء المباشر، أو زر واتساب للتواصل مع الدعم الفني.</p>
    </div>

    <div class="flex flex-col sm:flex-row gap-3">
        <label class="flex-1 flex items-start gap-3 p-4 rounded-xl border-2 cursor-pointer transition-colors"
               :class="pricingMode === 'price' ? 'border-sky-500 bg-white dark:bg-slate-700' : 'border-slate-200 dark:border-slate-600 bg-white/60 dark:bg-slate-700/40'">
            <input type="radio" name="pricing_mode" value="price" class="mt-1 accent-sky-600"
                   x-model="pricingMode" {{ $pricingMode === 'price' ? 'checked' : '' }}>
            <span>
                <span class="block text-sm font-bold text-slate-800 dark:text-slate-100">عرض السعر</span>
                <span class="block text-xs text-slate-500 dark:text-slate-400 mt-1">يظهر السعر على البطاقة ويمكن الشراء من الموقع.</span>
            </span>
        </label>
        <label class="flex-1 flex items-start gap-3 p-4 rounded-xl border-2 cursor-pointer transition-colors"
               :class="pricingMode === 'contact_support' ? 'border-emerald-500 bg-white dark:bg-slate-700' : 'border-slate-200 dark:border-slate-600 bg-white/60 dark:bg-slate-700/40'">
            <input type="radio" name="pricing_mode" value="contact_support" class="mt-1 accent-emerald-600"
                   x-model="pricingMode" {{ $pricingMode === 'contact_support' ? 'checked' : '' }}>
            <span>
                <span class="block text-sm font-bold text-slate-800 dark:text-slate-100">
                    <i class="fab fa-whatsapp text-emerald-600 ml-1"></i>
                    التواصل مع الدعم (واتساب)
                </span>
                <span class="block text-xs text-slate-500 dark:text-slate-400 mt-1">يُخفى السعر ويظهر زر واتساب على بطاقة الكورس وصفحة التفاصيل.</span>
            </span>
        </label>
    </div>

    <div x-show="pricingMode === 'price'" x-cloak class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="space-y-2">
            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">سعر الكورس الأساسي (قبل الخصم — {{ __('public.currency_name') }})</label>
            <input type="number" name="price" value="{{ old('price', $advancedCourse->price ?? 0) }}" min="0" step="0.01"
                   class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-3 text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition"
                   placeholder="0 للمجاني">
            @error('price') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
        </div>
        <div class="space-y-2">
            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">سعر بعد الخصم (اختياري)</label>
            <input type="number" name="price_after_discount" value="{{ old('price_after_discount', $advancedCourse->price_after_discount ?? null) }}" min="0" step="0.01"
                   class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-3 text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition"
                   placeholder="اتركه فارغاً إن لم يكن هناك عرض">
            <p class="text-xs text-slate-500 dark:text-slate-400">يُعرض على البطاقات كسعر قبل وبعد. الدفع يُحسب على سعر الشراء الفعلي.</p>
            @error('price_after_discount') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
        </div>
    </div>

    <div x-show="pricingMode === 'contact_support'" x-cloak
         class="flex items-start gap-3 p-4 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 text-sm text-emerald-900 dark:text-emerald-100">
        <i class="fab fa-whatsapp text-2xl text-emerald-600 shrink-0 mt-0.5"></i>
        <p>سيُستخدم رابط واتساب الدعم من إعدادات الموقع (الفوتر). عند الضغط على البطاقة يفتح واتساب مع رسالة تتضمن اسم الكورس.</p>
    </div>
</div>
