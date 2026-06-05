@extends('layouts.admin')

@section('title', 'إضافة اشتراك')
@section('header', 'إضافة اشتراك')

@section('content')
@php
    $fmtPrice = fn ($v) => number_format((float) $v, 0);
    $starter = $starter ?? ($teacherPlans['teacher_starter'] ?? []);
    $pro = $pro ?? ($teacherPlans['teacher_pro'] ?? []);
@endphp
<div class="space-y-6" x-data="subscriptionAdminForm(@js($initialSubscriberRole ?? ''))">
    @include('admin.subscriptions._subscriptions-admin-nav')

    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">إضافة اشتراك جديد</h1>
        <p class="text-sm text-slate-600 mb-6">طالب → اختر <strong>باقة أساسية</strong> أو <strong>باقة مخصصة</strong> (ساعات حصص مع المعلم). مدرب → قالب باقة أو إدخال يدوي. القوالب من <a href="{{ route('admin.tutor-lessons.settings') }}" class="text-violet-600 font-bold underline">إعدادات حصص الطلاب</a>.</p>

        <form action="{{ route('admin.subscriptions.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">المشترك *</label>
                    <input type="text" id="mx-user-search" placeholder="ابحث بالاسم أو الهاتف..." class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg mb-2">
                    <select id="mx-user-select" name="user_id" required @change="onUserChange()" class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                        <option value="">اختر طالباً أو مدرباً</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" data-role="{{ $user->role }}" @selected(old('user_id') == $user->id)>
                                @if(in_array($user->role, ['instructor', 'teacher'], true)) [مدرب] @else [طالب] @endif
                                {{ $user->name }} — {{ $user->phone }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2" x-show="isInstructor()" x-cloak>
                    <label class="block text-sm font-medium text-gray-700 mb-2">قالب باقة مدرب (اختياري)</label>
                    <select name="teacher_plan_key" x-model="selectedPlan" @change="applyPlan" :disabled="!isInstructor()" class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                        <option value="">بدون — إدخال يدوي</option>
                        @if($starter)
                            <option value="teacher_starter">{{ $starter['label'] ?? 'أساسية' }} — {{ $fmtPrice($starter['price'] ?? 0) }} {{ __('public.currency') }}</option>
                        @endif
                        @if($pro)
                            <option value="teacher_pro">{{ $pro['label'] ?? 'شاملة' }} — {{ $fmtPrice($pro['price'] ?? 0) }} {{ __('public.currency') }}</option>
                        @endif
                    </select>
                </div>

                @include('admin.subscriptions._student-package-picker', ['fmtPrice' => $fmtPrice])

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">نوع الاشتراك *</label>
                    <select name="subscription_type" x-model="form.subscription_type" required class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                        @foreach($typeOptions as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">اسم الخطة *</label>
                    <input type="text" name="plan_name" x-model="form.plan_name" required class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">السعر *</label>
                    <input type="number" name="price" x-model.number="form.price" step="0.01" min="0" required class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">تاريخ البداية *</label>
                    <input type="date" name="start_date" required value="{{ old('start_date', date('Y-m-d')) }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">تاريخ الانتهاء *</label>
                    <input type="date" name="end_date" required value="{{ old('end_date', date('Y-m-d', strtotime('+1 month'))) }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">دورة الفوترة *</label>
                    <select name="billing_cycle" x-model="form.billing_cycle" required class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                        <option value="monthly">شهري</option>
                        <option value="quarterly">ربع سنوي</option>
                        <option value="yearly">سنوي</option>
                    </select>
                </div>
            </div>

            <label class="flex items-center gap-2">
                <input type="checkbox" name="auto_renew" value="1" class="rounded border-gray-300">
                <span class="text-sm">تجديد تلقائي</span>
            </label>

            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 space-y-3" x-show="isInstructor()" x-cloak>
                <h2 class="text-sm font-semibold text-gray-800">مزايا باقة المدرب</h2>
                @include('admin.subscriptions._subscription-feature-checkboxes', [
                    'featureKeysOrder' => $featureKeysOrder,
                    'featureDisplayLines' => $featureDisplayLines,
                    'checkedKeys' => $checkedFeatures,
                ])
            </div>

            @include('admin.subscriptions._subscription-limit-fields')

            <div class="flex gap-4">
                <button type="submit" class="bg-sky-600 hover:bg-sky-700 text-white px-6 py-3 rounded-lg font-medium">إنشاء الاشتراك</button>
                <a href="{{ route('admin.subscriptions.index') }}" class="bg-gray-200 text-gray-800 px-6 py-3 rounded-lg font-medium">إلغاء</a>
            </div>
        </form>
    </div>
</div>
@include('admin.subscriptions._subscription-form-script', ['subscription' => null])
@endsection
