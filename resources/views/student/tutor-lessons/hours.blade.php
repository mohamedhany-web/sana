@extends('layouts.app')

@section('title', 'ساعات الحصص')
@section('header', 'ساعات الحصص')

@include('student.tutor-lessons.partials.dashboard-styles')

@section('content')
@php
    $brandBlue = config('brand.colors.blue');
    $brandPurple = config('brand.colors.purple');
    $currency = __('public.currency');
@endphp

<div class="sd-page space-y-6 pb-8 w-full">
    <div class="sd-hero">
        <div class="sd-hero-main relative z-[1]">
            <div class="min-w-0">
                <p class="text-xs font-bold sd-tag mb-2">رصيد الباقة</p>
                <h1 class="font-heading text-2xl sm:text-3xl font-black text-slate-800 leading-tight">
                    ساعات الحصص
                </h1>
                <p class="text-slate-600 text-sm mt-2 max-w-2xl leading-relaxed">
                    تابع ساعاتك المتبقية، واشترِ باقة إضافية من داخل المنصة عند نفاد الرصيد.
                </p>
                <div class="flex flex-wrap gap-2 mt-4">
                    <a href="{{ route('student.tutor-lessons.hub') }}" class="sd-btn-outline">
                        <i class="fas fa-home"></i> دروس المعلمين
                    </a>
                    <a href="{{ route('student.tutor-lessons.teachers') }}" class="sd-btn-primary">
                        <i class="fas fa-calendar-plus"></i> احجز حصة
                    </a>
                </div>
            </div>
        </div>
        <div class="sd-motivation">
            <span class="w-11 h-11 rounded-xl bg-white/20 flex items-center justify-center text-xl">
                <i class="fas fa-clock"></i>
            </span>
            <p class="font-bold text-sm leading-relaxed">المتبقي الآن</p>
            <p class="text-2xl font-black">{{ $remainingLabel }} @if($remaining !== PHP_INT_MAX)<span class="text-sm font-bold opacity-90">ساعة</span>@endif</p>
        </div>
    </div>

    @if(session('success'))
        <div class="sd-alert sd-alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
    @endif
    @if(session('info'))
        <div class="sd-alert"><i class="fas fa-info-circle"></i> {{ session('info') }}</div>
    @endif
    @if(session('error'))
        <div class="sd-alert sd-alert-error"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
    @endif

    <div>
        <h2 class="text-sm font-bold text-slate-700 mb-3">ملخص الرصيد</h2>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="sd-kpi">
                <div class="flex items-start justify-between gap-2 mb-3">
                    <span class="sd-kpi-icon" style="background:linear-gradient(135deg,{{ $brandBlue }},#2563eb)"><i class="fas fa-box"></i></span>
                </div>
                <p class="text-2xl font-black text-slate-800 tabular-nums">{{ $base < 0 ? '∞' : $base }}</p>
                <p class="text-xs font-bold text-slate-600 mt-0.5">ساعات الاشتراك</p>
            </div>
            <div class="sd-kpi">
                <div class="flex items-start justify-between gap-2 mb-3">
                    <span class="sd-kpi-icon" style="background:linear-gradient(135deg,#10b981,#059669)"><i class="fas fa-plus"></i></span>
                </div>
                <p class="text-2xl font-black text-slate-800 tabular-nums">{{ $bonus }}</p>
                <p class="text-xs font-bold text-slate-600 mt-0.5">ساعات مشتراة</p>
            </div>
            <div class="sd-kpi">
                <div class="flex items-start justify-between gap-2 mb-3">
                    <span class="sd-kpi-icon" style="background:linear-gradient(135deg,#f59e0b,#ea580c)"><i class="fas fa-hourglass-half"></i></span>
                </div>
                <p class="text-2xl font-black text-slate-800 tabular-nums">{{ (int) $profile->lesson_hours_used }}</p>
                <p class="text-xs font-bold text-slate-600 mt-0.5">مستهلكة</p>
            </div>
            <div class="sd-kpi">
                <div class="flex items-start justify-between gap-2 mb-3">
                    <span class="sd-kpi-icon" style="background:linear-gradient(135deg,{{ $brandPurple }},#6d28d9)"><i class="fas fa-clock"></i></span>
                </div>
                <p class="text-2xl font-black text-slate-800 tabular-nums">{{ $remainingLabel }}</p>
                <p class="text-xs font-bold text-slate-600 mt-0.5">متبقية</p>
            </div>
        </div>
    </div>

    @if($remaining !== PHP_INT_MAX && $remaining < 2)
        <div class="sd-alert sd-alert-error flex flex-wrap items-center justify-between gap-3">
            <p class="m-0"><i class="fas fa-exclamation-triangle"></i> رصيدك منخفض — اشترِ ساعات إضافية لتتمكن من الحجز.</p>
        </div>
    @endif

    <div class="sd-panel">
        <div class="sd-panel-head">
            <h2 class="font-heading font-bold text-slate-800 m-0">باقات الساعات المتاحة</h2>
        </div>
        <div class="sd-panel-body">
            @if(empty($plans))
                <div class="text-center py-10 text-slate-500">
                    <i class="fas fa-layer-group text-3xl mb-3 opacity-40 block"></i>
                    <p class="text-sm">لا توجد باقات متاحة للشراء حالياً.</p>
                    <p class="text-xs mt-2">يُفعّل الأدمن السعر من إعدادات باقات الطلاب ويُلغي «تواصل لمعرفة السعر».</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach($plans as $key => $plan)
                        @php
                            $hours = (int) ($plan['limits']['tutor_lesson_hours'] ?? 0);
                            $price = (float) ($plan['price'] ?? 0);
                        @endphp
                        <div class="rounded-2xl border border-slate-200 p-5 flex flex-col gap-3 bg-slate-50/40">
                            @if(!empty($plan['card_badge']))
                                <span class="inline-flex self-start text-[10px] font-black px-2 py-0.5 rounded-full bg-violet-100 text-violet-700">{{ $plan['card_badge'] }}</span>
                            @endif
                            <h3 class="font-heading font-black text-slate-800 text-lg m-0">{{ $plan['label'] ?? $key }}</h3>
                            @if(!empty($plan['card_subtitle']))
                                <p class="text-xs text-slate-600 m-0">{{ $plan['card_subtitle'] }}</p>
                            @endif
                            <p class="text-3xl font-black text-slate-900 tabular-nums m-0">
                                {{ number_format($price, 0) }}
                                <span class="text-sm font-bold text-slate-500">{{ $currency }}</span>
                            </p>
                            <p class="text-sm font-bold text-emerald-700 m-0">
                                <i class="fas fa-clock"></i> {{ $hours }} ساعة حصص
                            </p>
                            <a href="{{ route('student.tutor-lessons.hours.checkout', $key) }}" class="sd-btn-primary mt-auto justify-center">
                                شراء وإضافة للرصيد
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <div class="sd-panel">
        <div class="sd-panel-head">
            <h2 class="font-heading font-bold text-slate-800 m-0">طلبات الشراء</h2>
        </div>
        <div class="sd-panel-body">
            @forelse($purchases as $p)
                <div class="sd-lesson-row">
                    <div class="sd-avatar"><i class="fas fa-receipt text-sm"></i></div>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-slate-800 truncate">{{ $p->plan_name }} · {{ $p->hours }} ساعة</p>
                        <p class="text-xs text-slate-500 mt-0.5">
                            {{ number_format((float) $p->price, 0) }} {{ $currency }}
                            · {{ $p->created_at?->timezone(config('app.timezone'))->format('Y-m-d H:i') }}
                        </p>
                    </div>
                    <span class="sd-badge {{ $p->status === 'approved' ? 'sd-badge-confirmed' : ($p->status === 'rejected' ? 'sd-badge-pending' : 'sd-badge-pending') }}">
                        {{ $p->statusLabel() }}
                    </span>
                </div>
            @empty
                <p class="text-sm text-slate-500 text-center py-6 m-0">لا توجد طلبات شراء بعد.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
