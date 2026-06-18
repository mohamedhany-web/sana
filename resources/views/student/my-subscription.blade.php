@extends('layouts.app')

@section('title', 'اشتراكي - الباقة والمدة')

@push('styles')
@include('dashboard.partials.sanua-theme')
@endpush

@section('content')
@php
    $sub = $subscription;
    $durationLabel = \App\Models\Subscription::getDurationLabel($sub->billing_cycle);
    $currency = __('public.currency');
@endphp

<div class="sanua-dash">

    <header class="sanua-page-head">
        <div>
            <h1 class="sanua-page-head__title">اشتراكي</h1>
            <p class="sanua-page-head__sub">تفاصيل باقتك الحالية والمزايا المتاحة</p>
        </div>
        <div class="sanua-page-head__actions">
            <a href="{{ route('public.pricing') }}" class="sanua-page-head__btn sanua-page-head__btn--ghost">
                <i class="fas fa-gem"></i>
                الباقات
            </a>
            @if(Route::has('student.tutor-lessons.hub'))
                <a href="{{ route('student.tutor-lessons.hub') }}" class="sanua-page-head__btn">
                    <i class="fas fa-calendar-check"></i>
                    حجز حصة
                </a>
            @endif
        </div>
    </header>

    <div class="sanua-stats-row">
        <div class="sanua-stat-pill">
            <span class="sanua-stat-pill__icon sanua-stat-pill__icon--purple" aria-hidden="true">
                <i class="fas fa-gem"></i>
            </span>
            <div class="sanua-stat-pill__body">
                <strong>{{ $daysRemaining !== null ? max(0, $daysRemaining) : '—' }}</strong>
                <span>يوم متبقٍ</span>
            </div>
        </div>
        <div class="sanua-stat-pill">
            <span class="sanua-stat-pill__icon sanua-stat-pill__icon--gold" aria-hidden="true">
                <i class="fas fa-clock"></i>
            </span>
            <div class="sanua-stat-pill__body">
                <strong>{{ $tutorHoursRemaining ?? 0 }}</strong>
                <span>ساعات حصص متبقية</span>
            </div>
        </div>
        <div class="sanua-stat-pill">
            <span class="sanua-stat-pill__icon sanua-stat-pill__icon--green" aria-hidden="true">
                <i class="fas fa-check-circle"></i>
            </span>
            <div class="sanua-stat-pill__body">
                <strong>نشط</strong>
                <span>حالة الاشتراك</span>
            </div>
        </div>
        <div class="sanua-stat-pill">
            <span class="sanua-stat-pill__icon sanua-stat-pill__icon--amber" aria-hidden="true">
                <i class="fas fa-tags"></i>
            </span>
            <div class="sanua-stat-pill__body">
                <strong>{{ $displayPrice > 0 ? number_format($displayPrice, 0) : '—' }}</strong>
                <span>{{ $currency }}</span>
            </div>
        </div>
    </div>

    <div class="sanua-subscription-hero">
        <div class="sanua-subscription-hero__top">
            <div class="sanua-live-card__badges" style="margin-bottom:10px;">
                @if(!empty($planPackage['card_badge']))
                    <span class="sanua-badge sanua-badge--pending">{{ $planPackage['card_badge'] }}</span>
                @endif
                <span class="sanua-badge sanua-badge--approved"><span class="sanua-badge__dot"></span> نشط</span>
            </div>
            <h2 class="sanua-subscription-hero__title">{{ $displayPlanName }}</h2>
            @if(!empty($planPackage['card_subtitle']))
                <p class="sanua-subscription-hero__sub">{{ $planPackage['card_subtitle'] }}</p>
            @else
                <p class="sanua-subscription-hero__sub">مدة الباقة: {{ $durationLabel }}</p>
            @endif
            @if($displayPrice > 0)
                <div class="sanua-subscription-hero__price">
                    {{ number_format($displayPrice, 0) }} {{ $currency }}
                    <span style="font-size:0.55em;opacity:0.85;">/ {{ \App\Models\Subscription::billingCycleLabel($sub->billing_cycle) }}</span>
                </div>
            @endif
        </div>
        <div class="sanua-subscription-hero__body">
            <div class="sanua-metric-grid">
                <div class="sanua-metric">
                    <span class="sanua-metric__label">تاريخ التفعيل</span>
                    <span class="sanua-metric__value">{{ $sub->start_date?->format('Y-m-d') ?? '—' }}</span>
                </div>
                <div class="sanua-metric">
                    <span class="sanua-metric__label">مدة الباقة</span>
                    <span class="sanua-metric__value">{{ $durationLabel }}</span>
                </div>
                <div class="sanua-metric">
                    <span class="sanua-metric__label">ينتهي في</span>
                    <span class="sanua-metric__value">{{ $sub->end_date?->format('Y-m-d') ?? '—' }}</span>
                </div>
                <div class="sanua-metric">
                    <span class="sanua-metric__label">نوع الاشتراك</span>
                    <span class="sanua-metric__value">{{ \App\Models\Subscription::typeLabel($sub->subscription_type) }}</span>
                </div>
            </div>
            @if($daysRemaining !== null)
                <p class="text-sm font-bold mt-3 {{ $daysRemaining < 0 ? 'text-rose-600' : ($daysRemaining <= 7 ? 'text-amber-600' : 'text-slate-500') }}">
                    @if($daysRemaining < 0)
                        انتهى منذ {{ abs($daysRemaining) }} يوم
                    @elseif($daysRemaining === 0)
                        ينتهي اليوم
                    @else
                        متبقٍ {{ $daysRemaining }} يوم على انتهاء الاشتراك
                    @endif
                </p>
            @endif
        </div>
    </div>

    @if($isStudentPlan || $tutorHoursQuota > 0 || $hasSupport)
        <section class="sanua-section">
            <div class="sanua-panel">
                <div class="sanua-panel__head"><h3>محتوى باقتك</h3></div>
                <div class="sanua-panel__body space-y-4">

                    @if($tutorHoursQuota > 0 || $tutorHoursFromLimits !== null)
                        <div class="rounded-xl border border-violet-200 bg-violet-50/40 p-4 space-y-3">
                            <p class="text-sm font-bold text-slate-800 flex items-center gap-2 m-0">
                                <i class="fas fa-chalkboard-user text-violet-500"></i>
                                حصص مع المعلمين
                            </p>
                            <div class="sanua-metric-grid" style="grid-template-columns:repeat(3,minmax(0,1fr));">
                                <div class="sanua-metric"><span class="sanua-metric__label">الرصيد</span><span class="sanua-metric__value">{{ $tutorHoursQuota }} س</span></div>
                                <div class="sanua-metric"><span class="sanua-metric__label">مستهلكة</span><span class="sanua-metric__value">{{ $tutorHoursUsed }} س</span></div>
                                <div class="sanua-metric"><span class="sanua-metric__label">متبقية</span><span class="sanua-metric__value">{{ $tutorHoursRemaining }} س</span></div>
                            </div>
                            @if($tutorHoursQuota > 0)
                                <div>
                                    <div class="flex justify-between text-[10px] text-slate-500 mb-1">
                                        <span>نسبة الاستهلاك</span><span>{{ $tutorHoursPercent }}%</span>
                                    </div>
                                    <div class="sanua-course-card__bar">
                                        <div class="sanua-course-card__bar-fill" style="width:{{ $tutorHoursPercent }}%;"></div>
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
                                <p class="text-sm font-bold text-slate-800 m-0">الدعم الفني</p>
                                <p class="text-xs text-slate-600 m-0">مشمول في باقتك — يمكنك فتح تذكرة دعم.</p>
                            </div>
                            @if(Route::has('student.support.index'))
                                <a href="{{ route('student.support.index') }}" class="sanua-btn sanua-btn--purple" style="padding:8px 14px;font-size:0.75rem;">فتح الدعم</a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </section>
    @endif

    @if(count($displayFeatures) > 0)
        <section class="sanua-section">
            <div class="sanua-panel">
                <div class="sanua-panel__head"><h3>المزايا المتاحة في باقتك</h3></div>
                <div class="sanua-panel__body">
                    <ul class="sanua-feature-list">
                        @foreach($displayFeatures as $featureKey)
                            <li><i class="fas fa-check-circle"></i> {{ __("student.subscription_feature.{$featureKey}") }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </section>
    @endif

    @if($studentUpgradeOptions->count() > 0)
        <section class="sanua-section">
            <div class="sanua-panel">
                <div class="sanua-panel__head"><h3>ترقية الباقة</h3></div>
                <div class="sanua-panel__body">
                    <p class="text-sm text-slate-600 mb-4">يمكنك الترقية إلى باقة أعلى للحصول على ساعات حصص أكثر.</p>
                    <div class="sanua-courses-grid">
                        @foreach($studentUpgradeOptions as $plan)
                            <div class="sanua-recording-card" style="cursor:default;">
                                <span class="sanua-recording-card__icon"><i class="fas fa-arrow-up"></i></span>
                                <h3 class="sanua-recording-card__title">{{ $plan['label'] ?? $plan['key'] }}</h3>
                                @if(!empty($plan['card_subtitle']))
                                    <p class="sanua-recording-card__sub">{{ $plan['card_subtitle'] }}</p>
                                @endif
                                <div class="sanua-recording-card__meta">
                                    <span>{{ (int) ($plan['limits']['tutor_lesson_hours'] ?? 0) }} ساعة حصص</span>
                                    @if(($plan['price'] ?? 0) > 0)
                                        <span>{{ number_format((float) $plan['price'], 0) }} {{ $currency }}</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @if(Route::has('student.support.index'))
                        <a href="{{ route('student.support.index') }}" class="sanua-btn sanua-btn--purple mt-4">
                            <i class="fas fa-arrow-up"></i> طلب ترقية عبر الدعم
                        </a>
                    @endif
                </div>
            </div>
        </section>
    @endif

    @if($teacherUpgradeOptions->count() > 0)
        <section class="sanua-section">
            <div class="sanua-panel">
                <div class="sanua-panel__head"><h3>ترقية الباقة</h3></div>
                <div class="sanua-panel__body">
                    <div class="sanua-session-list">
                        @foreach($teacherUpgradeOptions as $planKey)
                            <a href="{{ route('public.subscription.checkout', $planKey) . '?upgrade=1&from=' . $sub->id }}" class="sanua-session-card">
                                <div class="sanua-session-card__row">
                                    <div>
                                        <h3 class="sanua-session-card__title">
                                            {{ $planKey === 'teacher_pro' ? 'الباقة الشاملة' : 'الباقة الأساسية' }}
                                        </h3>
                                        <p class="sanua-session-card__desc" style="margin:0;">ترقية إلى مستوى أعلى</p>
                                    </div>
                                    <span class="sanua-session-card__action"><i class="fas fa-arrow-up"></i></span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    @endif

    <p class="text-sm text-slate-500">
        عند انتهاء المدة سيُغلق الاشتراك تلقائياً.
        <a href="{{ route('public.pricing') }}" class="text-violet-600 font-bold hover:underline">عرض الباقات والتجديد</a>
    </p>
</div>
@endsection
