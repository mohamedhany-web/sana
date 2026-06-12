@php
    $studentPlans = $studentPlans ?? \App\Services\StudentSubscriptionPlansService::getPlans();
    $cycleLabels = \App\Models\Subscription::billingCycleLabels();
@endphp
<section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden mt-6">
    <div class="px-6 py-4 border-b border-slate-200 bg-gradient-to-l from-violet-50 to-white">
        <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
            <i class="fas fa-layer-group text-violet-500"></i>
            قوالب باقات اشتراك الطلاب
        </h3>
        <p class="text-xs text-slate-600 mt-1">تظهر في صفحة «إضافة اشتراك» عند اختيار طالب. الساعات تُنسخ إلى ملف الطالب عند تفعيل الاشتراك.</p>
    </div>

    <form method="post" action="{{ route('admin.tutor-lessons.student-plans.update') }}" class="p-6 space-y-6" data-turbo="false">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
            @foreach($studentPlans as $planKey => $plan)
                <div class="rounded-xl border border-slate-200 p-4 space-y-3 bg-slate-50/40">
                    <p class="text-xs font-bold text-violet-700 uppercase tracking-wide">{{ $planKey }}</p>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">اسم الباقة</label>
                        <input type="text" name="plans[{{ $planKey }}][label]" required maxlength="120"
                               value="{{ old("plans.{$planKey}.label", $plan['label'] ?? '') }}"
                               class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm">
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">السعر</label>
                            <input type="number" name="plans[{{ $planKey }}][price]" min="0" step="0.01" required
                                   value="{{ old("plans.{$planKey}.price", $plan['price'] ?? 0) }}"
                                   class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">ساعات الحصص</label>
                            <input type="number" name="plans[{{ $planKey }}][limits][tutor_lesson_hours]" min="0" max="10000" required
                                   value="{{ old("plans.{$planKey}.limits.tutor_lesson_hours", $plan['limits']['tutor_lesson_hours'] ?? 0) }}"
                                   class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm">
                        </div>
                    </div>
                    <div class="rounded-lg border border-violet-100 bg-violet-50/50 p-3 space-y-2">
                        <label class="inline-flex items-center gap-2 text-xs font-bold text-slate-700">
                            <input type="hidden" name="plans[{{ $planKey }}][limits][tutor_group_enabled]" value="0">
                            <input type="checkbox" name="plans[{{ $planKey }}][limits][tutor_group_enabled]" value="1"
                                   @checked(filter_var(old("plans.{$planKey}.limits.tutor_group_enabled", $plan['limits']['tutor_group_enabled'] ?? false), FILTER_VALIDATE_BOOLEAN))>
                            تفعيل حجز المجموعات
                        </label>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">أقصى حجم مجموعة</label>
                            <input type="number" name="plans[{{ $planKey }}][limits][tutor_group_max_size]" min="0" max="30"
                                   value="{{ old("plans.{$planKey}.limits.tutor_group_max_size", $plan['limits']['tutor_group_max_size'] ?? 0) }}"
                                   class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm">
                            <p class="text-[10px] text-slate-500 mt-1">0 = بدون حد إضافي (يُطبَّق حد العرض نفسه).</p>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">دورة الفوترة</label>
                        <select name="plans[{{ $planKey }}][billing_cycle]" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm">
                            @foreach($cycleLabels as $val => $lbl)
                                <option value="{{ $val }}" @selected(old("plans.{$planKey}.billing_cycle", $plan['billing_cycle'] ?? 'monthly') === $val)>{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">وصف البطاقة</label>
                        <input type="text" name="plans[{{ $planKey }}][card_subtitle]" maxlength="200"
                               value="{{ old("plans.{$planKey}.card_subtitle", $plan['card_subtitle'] ?? '') }}"
                               class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm">
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">شارة (اختياري)</label>
                            <input type="text" name="plans[{{ $planKey }}][card_badge]" maxlength="40"
                                   value="{{ old("plans.{$planKey}.card_badge", $plan['card_badge'] ?? '') }}"
                                   class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">تلميح السعر</label>
                            <input type="text" name="plans[{{ $planKey }}][card_price_hint]" maxlength="80"
                                   value="{{ old("plans.{$planKey}.card_price_hint", $plan['card_price_hint'] ?? '') }}"
                                   class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm">
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-violet-600 text-white text-sm font-bold hover:bg-violet-700 transition-colors">
            <i class="fas fa-save"></i>
            حفظ قوالب باقات الطلاب
        </button>
    </form>
</section>
