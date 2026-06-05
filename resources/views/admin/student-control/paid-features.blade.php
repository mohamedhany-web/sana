@extends('layouts.admin')

@section('title', 'باقات واشتراكات الطلاب')
@section('header', 'باقات واشتراكات الطلاب')

@section('content')
@php
    $fmtPrice = fn ($v) => number_format((float) $v, 0);
    $statCards = [
        ['label' => 'طلاب باشتراك نشط', 'value' => $stats['active_students_with_subscription'] ?? 0, 'icon' => 'fa-user-check', 'bg' => 'bg-emerald-100', 'text' => 'text-emerald-600', 'desc' => 'اشتراك ساري المفعول'],
        ['label' => 'بدون اشتراك', 'value' => $stats['students_without_subscription'] ?? 0, 'icon' => 'fa-user-clock', 'bg' => 'bg-amber-100', 'text' => 'text-amber-700', 'desc' => 'من إجمالي ' . number_format($stats['total_students'] ?? 0) . ' طالب'],
        ['label' => 'اشتراكات نشطة', 'value' => $stats['active_subscriptions'] ?? 0, 'icon' => 'fa-credit-card', 'bg' => 'bg-violet-100', 'text' => 'text-violet-600', 'desc' => 'سجلات اشتراك فعّالة'],
        ['label' => 'تذاكر دعم مفتوحة', 'value' => $stats['open_support_tickets'] ?? 0, 'icon' => 'fa-headset', 'bg' => 'bg-sky-100', 'text' => 'text-sky-600', 'desc' => 'للطلاب'],
    ];
@endphp

<div class="space-y-6">
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-5 bg-gradient-to-l from-violet-50 to-white border-b border-slate-200 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-violet-600 to-indigo-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-layer-group text-lg"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-black text-slate-900">باقات واشتراكات الطلاب</h2>
                    <p class="text-sm text-slate-600 mt-0.5">نظامنا الحالي: <strong>باقات حصص مع المعلم</strong> (ساعات في الاشتراك) — وليس مزايا باقات المدرب (AI، Classroom، إلخ)</p>
                </div>
            </div>
            <div class="flex flex-wrap gap-2">
                @if(Route::has('admin.subscriptions.create'))
                    <a href="{{ route('admin.subscriptions.create') }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-bold text-white bg-violet-600 rounded-xl hover:bg-violet-700">
                        <i class="fas fa-plus"></i>
                        إضافة اشتراك
                    </a>
                @endif
                @if(Route::has('admin.tutor-lessons.settings'))
                    <a href="{{ route('admin.tutor-lessons.settings') }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-violet-700 bg-violet-50 border border-violet-200 rounded-xl hover:bg-violet-100">
                        <i class="fas fa-cog"></i>
                        قوالب الباقات
                    </a>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 p-6">
            @foreach($statCards as $card)
                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-xs font-semibold text-slate-600">{{ $card['label'] }}</p>
                            <p class="text-2xl font-black text-slate-900 mt-1">{{ number_format($card['value']) }}</p>
                            <p class="text-[11px] text-slate-500 mt-1">{{ $card['desc'] }}</p>
                        </div>
                        <div class="w-10 h-10 rounded-lg {{ $card['bg'] }} {{ $card['text'] }} flex items-center justify-center shrink-0">
                            <i class="fas {{ $card['icon'] }}"></i>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="px-6 py-3 bg-slate-50 border-t border-slate-100 text-xs text-slate-600 flex flex-wrap gap-4">
            <span><i class="fas fa-clock text-violet-500 ml-1"></i> الساعات الافتراضية بدون اشتراك: <strong>{{ (int) ($tutorDefaults['default_student_lesson_hours'] ?? 0) }}</strong> س/شهر</span>
            @if(($stats['students_with_support_feature'] ?? 0) > 0)
                <span><i class="fas fa-headset text-sky-500 ml-1"></i> طلاب بميزة دعم في الاشتراك: <strong>{{ $stats['students_with_support_feature'] }}</strong></span>
            @endif
        </div>
    </section>

    {{-- قوالب الباقات --}}
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
            <div>
                <h3 class="text-base font-black text-slate-900">قوالب الباقات (أساسية / قياسية / مميزة)</h3>
                <p class="text-xs text-slate-500 mt-0.5">تُعرَّف في إعدادات حصص الطلاب وتُطبَّق من «إضافة اشتراك»</p>
            </div>
        </div>
        <div class="p-5 grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach($planCards as $plan)
                <a href="{{ route('admin.students-control.paid-features.show', $plan['key']) }}"
                   class="rounded-2xl border border-slate-200 p-5 hover:border-violet-400 hover:shadow-md transition-all block bg-white">
                    <p class="text-lg font-black text-slate-900">{{ $plan['label'] }}</p>
                    <p class="text-xs text-slate-500 mt-1 line-clamp-2">{{ $plan['card_subtitle'] }}</p>
                    <p class="text-2xl font-black text-violet-700 mt-3">
                        {{ $fmtPrice($plan['price']) }}
                        <span class="text-sm font-semibold text-slate-500">{{ __('public.currency') }}</span>
                    </p>
                    <p class="text-xs font-bold text-violet-800 mt-2">
                        <i class="fas fa-clock ml-1"></i>
                        {{ $plan['hours'] }} ساعة حصص / شهر
                    </p>
                    <p class="mt-4 inline-flex items-center gap-2 text-sm font-bold text-slate-700 bg-slate-100 px-3 py-1.5 rounded-lg">
                        <i class="fas fa-users text-violet-600"></i>
                        {{ number_format($plan['students_count']) }} طالب نشط
                    </p>
                </a>
            @endforeach
        </div>
    </section>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- باقة مخصصة + قدرات --}}
        <div class="lg:col-span-1 space-y-4">
            <a href="{{ route('admin.students-control.paid-features.show', 'custom') }}"
               class="block rounded-2xl border border-dashed border-violet-300 bg-violet-50/40 p-5 hover:bg-violet-50 transition-colors">
                <p class="text-sm font-black text-violet-900">باقات مخصصة</p>
                <p class="text-xs text-violet-800/90 mt-1">اشتراكات يدوية بدون قالب student_*</p>
                <p class="text-xl font-black text-slate-900 mt-3">{{ number_format($customPlanStats['students_count'] ?? 0) }} طالب</p>
                <p class="text-[11px] text-slate-500">{{ number_format($customPlanStats['subscriptions_count'] ?? 0) }} اشتراك</p>
            </a>

            @foreach($capabilityCards as $cap)
                <a href="{{ route('admin.students-control.paid-features.show', $cap['key']) }}"
                   class="flex items-center gap-3 rounded-xl border border-slate-200 bg-white p-4 hover:border-sky-300 transition-colors">
                    <span class="w-10 h-10 rounded-lg {{ $cap['icon_bg'] }} {{ $cap['icon_text'] }} flex items-center justify-center shrink-0">
                        <i class="fas {{ $cap['icon'] }}"></i>
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-bold text-slate-900">{{ $cap['label'] }}</p>
                        <p class="text-[11px] text-slate-500 line-clamp-2">{{ $cap['description'] }}</p>
                    </div>
                    <span class="text-sm font-black text-slate-800">{{ number_format($cap['students_count']) }}</span>
                </a>
            @endforeach
        </div>

        {{-- آخر الاشتراكات --}}
        <section class="lg:col-span-2 rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
                <h3 class="text-base font-black text-slate-900">آخر اشتراكات الطلاب النشطة</h3>
                @if(Route::has('admin.subscriptions.index'))
                    <a href="{{ route('admin.subscriptions.index') }}" class="text-xs font-bold text-violet-600 hover:underline">كل الاشتراكات</a>
                @endif
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-right text-sm">
                    <thead class="bg-slate-50 text-xs font-bold text-slate-600">
                        <tr>
                            <th class="px-4 py-3">الطالب</th>
                            <th class="px-4 py-3">الخطة</th>
                            <th class="px-4 py-3">ساعات</th>
                            <th class="px-4 py-3">ينتهي</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($recentSubscriptions as $sub)
                            @php
                                $hours = is_array($sub->feature_limits) ? (int) ($sub->feature_limits['tutor_lesson_hours'] ?? 0) : 0;
                            @endphp
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3 font-semibold text-slate-900">{{ $sub->user->name ?? '—' }}</td>
                                <td class="px-4 py-3 text-xs">
                                    <span class="font-medium">{{ $sub->plan_name }}</span>
                                    @if($sub->teacher_plan_key)
                                        <span class="block text-slate-400 font-mono text-[10px]">{{ $sub->teacher_plan_key }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 font-bold text-violet-700">{{ $hours }} س</td>
                                <td class="px-4 py-3 text-xs text-slate-600">{{ $sub->end_date?->format('Y-m-d') ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-10 text-center text-slate-500 text-sm">لا توجد اشتراكات نشطة للطلاب.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <section class="rounded-xl border border-amber-200 bg-amber-50/60 p-4 text-sm text-amber-900">
        <p class="font-bold mb-1"><i class="fas fa-info-circle ml-1"></i> ملاحظة للإدارة</p>
        <p class="text-xs leading-relaxed">مزايا مثل <strong>أدوات AI</strong> و<strong>Classroom</strong> مخصّصة لاشتراكات <strong>المدربين</strong> (teacher_starter / teacher_pro). للطلاب ركّز على ساعات الحصص والدعم الفني عند تفعيله في الاشتراك.</p>
    </section>
</div>
@endsection
