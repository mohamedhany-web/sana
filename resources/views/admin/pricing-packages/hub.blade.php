@extends('layouts.admin')

@section('title', 'الباقات والأسعار')
@section('header', 'الباقات والأسعار')

@section('content')
@php
    $currency = __('public.currency');
@endphp
<div class="space-y-6">
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-5 bg-gradient-to-l from-sky-50 to-white border-b border-slate-200">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-sky-500 to-cyan-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-tags text-lg"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-black text-slate-900">قسم الباقات والأسعار</h2>
                    <p class="text-sm text-slate-600 mt-0.5">كل ما يخص الاشتراكات، باقات الحصص، شراء الساعات، وأسعار الكورسات — من مكان واحد.</p>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 p-6">
            <div class="rounded-xl border border-slate-200 p-4">
                <p class="text-xs font-semibold text-slate-500">اشتراكات نشطة</p>
                <p class="text-2xl font-black text-slate-900 mt-1 tabular-nums">{{ number_format($stats['active_subscriptions']) }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 p-4">
                <p class="text-xs font-semibold text-slate-500">طلبات شراء ساعات</p>
                <p class="text-2xl font-black text-amber-700 mt-1 tabular-nums">{{ number_format($stats['pending_hour_purchases']) }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 p-4">
                <p class="text-xs font-semibold text-slate-500">باقات للشراء</p>
                <p class="text-2xl font-black text-emerald-700 mt-1 tabular-nums">{{ number_format($stats['buyable_plans']) }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 p-4">
                <p class="text-xs font-semibold text-slate-500">باقات كورسات</p>
                <p class="text-2xl font-black text-sky-700 mt-1 tabular-nums">{{ number_format($stats['course_packages']) }}</p>
            </div>
        </div>
    </section>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @foreach($cards as $card)
            <a href="{{ route($card['route']) }}"
               class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm hover:shadow-md hover:border-sky-300 transition-all no-underline text-inherit flex flex-col gap-3">
                <div class="flex items-start justify-between gap-3">
                    <span class="w-11 h-11 rounded-xl bg-gradient-to-br {{ $card['color'] }} text-white flex items-center justify-center shadow">
                        <i class="fas {{ $card['icon'] }}"></i>
                    </span>
                    <span class="text-[11px] font-bold text-slate-500 bg-slate-100 px-2 py-1 rounded-lg">{{ $card['meta'] }}</span>
                </div>
                <div>
                    <h3 class="font-black text-slate-900 text-lg m-0 group-hover:text-sky-700">{{ $card['title'] }}</h3>
                    <p class="text-sm text-slate-600 mt-1 m-0 leading-relaxed">{{ $card['desc'] }}</p>
                </div>
                <span class="mt-auto text-xs font-bold text-sky-600">فتح القسم ←</span>
            </a>
        @endforeach
    </div>

    @if(!empty($studentPlans))
        <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200 bg-slate-50 flex flex-wrap items-center justify-between gap-2">
                <div>
                    <h3 class="text-base font-black text-slate-900 m-0">قوالب باقات الطلاب (أسعار الحصص)</h3>
                    <p class="text-xs text-slate-500 mt-0.5 m-0">تظهر للطالب عند تفعيل السعر وإلغاء «تواصل لمعرفة السعر»</p>
                </div>
                @if(Route::has('admin.tutor-lessons.settings'))
                    <a href="{{ route('admin.tutor-lessons.settings') }}" class="text-xs font-bold text-violet-600 hover:underline">تعديل القوالب</a>
                @endif
            </div>
            <div class="p-5 grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($studentPlans as $key => $plan)
                    @php
                        $hours = (int) ($plan['limits']['tutor_lesson_hours'] ?? 0);
                        $price = (float) ($plan['price'] ?? 0);
                        $contact = \App\Services\StudentSubscriptionPlansService::requiresContactForPricing($plan);
                        $buyable = filter_var($plan['student_buyable'] ?? true, FILTER_VALIDATE_BOOLEAN) && ! $contact && $price > 0 && $hours > 0;
                    @endphp
                    <div class="rounded-xl border border-slate-200 p-4 bg-slate-50/40">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wide m-0">{{ $key }}</p>
                        <p class="font-black text-slate-900 mt-1 m-0">{{ $plan['label'] ?? $key }}</p>
                        <p class="text-2xl font-black text-slate-800 mt-2 m-0 tabular-nums">
                            @if($contact || $price <= 0)
                                <span class="text-base text-slate-500">تواصل للسعر</span>
                            @else
                                {{ number_format($price, 0) }} <span class="text-sm font-bold text-slate-500">{{ $currency }}</span>
                            @endif
                        </p>
                        <p class="text-sm font-bold text-emerald-700 mt-2 m-0">{{ $hours }} ساعة</p>
                        <p class="text-[11px] mt-2 m-0 {{ $buyable ? 'text-emerald-700 font-bold' : 'text-slate-500' }}">
                            {{ $buyable ? 'متاحة للشراء داخل المنصة' : 'غير معروضة للشراء حالياً' }}
                        </p>
                    </div>
                @endforeach
            </div>
        </section>
    @endif
</div>
@endsection
