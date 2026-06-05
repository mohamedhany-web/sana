@extends('layouts.app')

@section('title', 'اشتراكي - الباقة والمدة')
@section('header', 'اشتراكي')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 py-6 space-y-6">
    @php
        $sub = $subscription;
        $durationLabel = \App\Models\Subscription::getDurationLabel($sub->billing_cycle);
        $currency = __('public.currency');
    @endphp

    {{-- بطاقة الباقة --}}
    <div class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="bg-gradient-to-l from-sky-50 via-white to-white p-6 border-b border-slate-100">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2 mb-2">
                        @if(!empty($planPackage['card_badge']))
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                {{ $planPackage['card_badge'] }}
                            </span>
                        @endif
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700 border border-emerald-200">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            نشط
                        </span>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-black text-slate-800">{{ $displayPlanName }}</h1>
                    @if(!empty($planPackage['card_subtitle']))
                        <p class="text-sm text-slate-600 mt-1">{{ $planPackage['card_subtitle'] }}</p>
                    @endif
                    @if(!empty($planPackage['card_price_hint']))
                        <p class="text-xs text-slate-500 mt-1">{{ $planPackage['card_price_hint'] }}</p>
                    @else
                        <p class="text-sm text-slate-600 mt-1">مدة الباقة: <strong>{{ $durationLabel }}</strong></p>
                    @endif
                </div>
                @if($displayPrice > 0)
                    <div class="text-left shrink-0 rounded-xl border border-slate-200 bg-white px-4 py-3 shadow-sm">
                        <p class="text-[10px] font-semibold text-slate-500 uppercase">قيمة الباقة</p>
                        <p class="text-2xl font-black text-slate-900">{{ number_format($displayPrice, 0) }} <span class="text-sm font-bold text-slate-500">{{ $currency }}</span></p>
                        <p class="text-[10px] text-slate-500 mt-0.5">{{ \App\Models\Subscription::billingCycleLabel($sub->billing_cycle) }}</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50">
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">تاريخ التفعيل</p>
                    <p class="mt-1 text-base font-bold text-slate-900">{{ $sub->start_date?->format('Y-m-d') ?? '—' }}</p>
                </div>
                <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50">
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">مدة الباقة</p>
                    <p class="mt-1 text-base font-bold text-slate-900">{{ $durationLabel }}</p>
                </div>
                <div class="p-4 rounded-xl border border-rose-100 bg-rose-50/50">
                    <p class="text-xs font-semibold text-rose-600 uppercase tracking-wide">ينتهي في</p>
                    <p class="mt-1 text-base font-bold text-slate-900">{{ $sub->end_date?->format('Y-m-d') ?? '—' }}</p>
                    @if($daysRemaining !== null)
                        <p class="text-[11px] mt-1 {{ $daysRemaining < 0 ? 'text-rose-600' : ($daysRemaining <= 7 ? 'text-amber-600' : 'text-slate-500') }}">
                            @if($daysRemaining < 0)
                                انتهى منذ {{ abs($daysRemaining) }} يوم
                            @elseif($daysRemaining === 0)
                                ينتهي اليوم
                            @else
                                متبقٍ {{ $daysRemaining }} يوم
                            @endif
                        </p>
                    @endif
                </div>
                <div class="p-4 rounded-xl border border-violet-100 bg-violet-50/50">
                    <p class="text-xs font-semibold text-violet-600 uppercase tracking-wide">نوع الاشتراك</p>
                    <p class="mt-1 text-base font-bold text-slate-900">{{ \App\Models\Subscription::typeLabel($sub->subscription_type) }}</p>
                    @if($planKey)
                        <p class="text-[10px] text-slate-500 mt-1 font-mono">{{ $planKey }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- محتوى الباقة (حصص + دعم) --}}
    @if($isStudentPlan || $tutorHoursQuota > 0 || $hasSupport)
        <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6 space-y-5">
            <h2 class="text-lg font-black text-slate-900">محتوى باقتك</h2>

            @if($tutorHoursQuota > 0 || $tutorHoursFromLimits !== null)
                <div class="rounded-xl border border-violet-200 bg-violet-50/40 p-4 space-y-3">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <p class="text-sm font-bold text-slate-800 flex items-center gap-2">
                                <i class="fas fa-chalkboard-user text-violet-500"></i>
                                حصص مع المعلمين
                            </p>
                            <p class="text-xs text-slate-600 mt-1">رصيد ساعات الحصص المشمول في باقتك لهذه الفترة.</p>
                        </div>
                        @if(Route::has('student.tutor-lessons.hub'))
                            <a href="{{ route('student.tutor-lessons.hub') }}" class="shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-violet-600 hover:bg-violet-700 text-white text-xs font-semibold">
                                <i class="fas fa-calendar-check"></i>
                                حجز حصة
                            </a>
                        @endif
                    </div>
                    <div class="grid grid-cols-3 gap-3 text-center">
                        <div class="rounded-lg bg-white border border-violet-100 px-3 py-2">
                            <p class="text-[10px] text-slate-500">الرصيد</p>
                            <p class="text-lg font-black text-violet-700">{{ $tutorHoursQuota }}</p>
                            <p class="text-[10px] text-slate-500">ساعة</p>
                        </div>
                        <div class="rounded-lg bg-white border border-amber-100 px-3 py-2">
                            <p class="text-[10px] text-slate-500">مستهلكة</p>
                            <p class="text-lg font-black text-amber-700">{{ $tutorHoursUsed }}</p>
                            <p class="text-[10px] text-slate-500">ساعة</p>
                        </div>
                        <div class="rounded-lg bg-white border border-emerald-100 px-3 py-2">
                            <p class="text-[10px] text-slate-500">متبقية</p>
                            <p class="text-lg font-black text-emerald-700">{{ $tutorHoursRemaining }}</p>
                            <p class="text-[10px] text-slate-500">ساعة</p>
                        </div>
                    </div>
                    @if($tutorHoursQuota > 0)
                        <div>
                            <div class="flex justify-between text-[10px] text-slate-500 mb-1">
                                <span>نسبة الاستهلاك</span>
                                <span>{{ $tutorHoursPercent }}%</span>
                            </div>
                            <div class="h-2 rounded-full bg-slate-200 overflow-hidden">
                                <div class="h-full rounded-full {{ $tutorHoursPercent >= 90 ? 'bg-rose-500' : ($tutorHoursPercent >= 70 ? 'bg-amber-500' : 'bg-violet-500') }}"
                                     style="width: {{ $tutorHoursPercent }}%"></div>
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            @if($hasSupport)
                <div class="flex items-center gap-3 rounded-xl border border-sky-200 bg-sky-50/50 px-4 py-3">
                    <span class="w-10 h-10 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center shrink-0">
                        <i class="fas fa-headset"></i>
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-bold text-slate-800">الدعم الفني</p>
                        <p class="text-xs text-slate-600">مشمول في باقتك — يمكنك فتح تذكرة دعم من لوحة الطالب.</p>
                    </div>
                    @if(Route::has('student.support.index'))
                        <a href="{{ route('student.support.index') }}" class="shrink-0 text-xs font-semibold text-sky-700 hover:underline">فتح الدعم</a>
                    @endif
                </div>
            @endif
        </div>
    @endif

    {{-- مزايا إضافية --}}
    @if(count($displayFeatures) > 0)
        <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6">
            <h2 class="text-lg font-black text-slate-900 mb-4">المزايا المتاحة في باقتك</h2>
            <ul class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                @foreach($displayFeatures as $featureKey)
                    <li class="flex items-center gap-2 text-sm text-slate-700">
                        <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-check text-[10px]"></i>
                        </span>
                        {{ __("student.subscription_feature.{$featureKey}") }}
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- ترقية باقة الطالب --}}
    @if($studentUpgradeOptions->count() > 0)
        <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6">
            <h2 class="text-lg font-black text-slate-900 mb-2">ترقية الباقة</h2>
            <p class="text-sm text-slate-600 mb-4">
                يمكنك الترقية إلى باقة أعلى للحصول على ساعات حصص أكثر. تواصل مع الدعم أو الإدارة لطلب الترقية.
            </p>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($studentUpgradeOptions as $plan)
                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50">
                        <div class="flex items-center justify-between gap-2 mb-2">
                            <div class="text-sm font-black text-slate-900">{{ $plan['label'] ?? $plan['key'] }}</div>
                            @if(!empty($plan['card_badge']))
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-100 text-amber-800">{{ $plan['card_badge'] }}</span>
                            @endif
                        </div>
                        @if(!empty($plan['card_subtitle']))
                            <p class="text-xs text-slate-600 mb-2">{{ $plan['card_subtitle'] }}</p>
                        @endif
                        <p class="text-sm font-bold text-violet-700">
                            {{ (int) ($plan['limits']['tutor_lesson_hours'] ?? 0) }} ساعة حصص
                        </p>
                        @if(($plan['price'] ?? 0) > 0)
                            <p class="text-xs text-slate-500 mt-1">{{ number_format((float) $plan['price'], 0) }} {{ $currency }} / {{ \App\Models\Subscription::billingCycleLabel($plan['billing_cycle'] ?? 'monthly') }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
            @if(Route::has('student.support.index'))
                <a href="{{ route('student.support.index') }}" class="inline-flex items-center gap-2 mt-4 px-4 py-2 rounded-xl bg-violet-600 hover:bg-violet-700 text-white text-sm font-semibold">
                    <i class="fas fa-arrow-up"></i>
                    طلب ترقية عبر الدعم
                </a>
            @endif
        </div>
    @endif

    {{-- ترقية باقة المدرب (إن وُجدت) --}}
    @if($teacherUpgradeOptions->count() > 0)
        <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6">
            <h2 class="text-lg font-black text-slate-900 mb-2">ترقية الباقة</h2>
            <p class="text-sm text-slate-600 mb-4">
                يمكنك الترقية إلى باقة أعلى. سيتم إرسال طلب ترقية للمراجعة وبعد الموافقة يتم تفعيل الباقة الأعلى.
            </p>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                @foreach($teacherUpgradeOptions as $planKey)
                    <a href="{{ route('public.subscription.checkout', $planKey) . '?upgrade=1&from=' . $sub->id }}"
                       class="group p-4 rounded-xl border border-slate-200 hover:border-sky-300 hover:bg-sky-50/60 transition">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <div class="text-sm font-black text-slate-900">
                                    {{ $planKey === 'teacher_pro' ? 'الباقة الشاملة' : 'الباقة الأساسية' }}
                                </div>
                                <div class="text-xs text-slate-500 mt-1">ترقية إلى مستوى أعلى</div>
                            </div>
                            <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-sky-600 text-white group-hover:bg-sky-700 transition">
                                <i class="fas fa-arrow-up text-xs"></i>
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    <p class="text-sm text-slate-500">
        عند انتهاء المدة سيُغلق الاشتراك تلقائياً ولن تظهر لك روابط القسم المدفوع حتى تجدد.
        <a href="{{ route('public.pricing') }}" class="text-sky-600 font-semibold hover:underline">عرض الباقات والتجديد</a>
    </p>
</div>
@endsection
