@extends('layouts.employee')

@section('title', __('تفاصيل الاتفاقية'))
@section('header', __('تفاصيل الاتفاقية'))

@section('content')
<div class="space-y-6">
    <!-- معلومات الاتفاقية -->
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $agreement->title }}</h1>
                <p class="text-gray-600 mt-1">{{ __('رقم الاتفاقية:') }} {{ $agreement->agreement_number }}</p>
            </div>
            <span class="px-4 py-2 text-sm font-semibold rounded-full
                @if($agreement->status === 'active') bg-green-100 text-green-800
                @elseif($agreement->status === 'suspended') bg-yellow-100 text-yellow-800
                @elseif($agreement->status === 'terminated') bg-red-100 text-red-800
                @elseif($agreement->status === 'completed') bg-blue-100 text-blue-800
                @else bg-gray-100 text-gray-800
                @endif">
                @if($agreement->status === 'active') {{ __('نشطة') }}
                @elseif($agreement->status === 'suspended') {{ __('معلقة') }}
                @elseif($agreement->status === 'terminated') {{ __('منتهية') }}
                @elseif($agreement->status === 'completed') {{ __('مكتملة') }}
                @else {{ __('مسودة') }}
                @endif
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <p class="text-sm font-semibold text-gray-600 mb-2">{{ __('الراتب الأساسي') }}</p>
                <p class="text-3xl font-bold text-green-600">{{ number_format($agreement->salary, 2) }} {{ __('public.currency') }}</p>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-600 mb-2">{{ __('تاريخ البدء') }}</p>
                <p class="text-xl font-semibold text-gray-900">{{ $agreement->start_date->format('Y-m-d') }}</p>
            </div>
            @if($agreement->end_date)
            <div>
                <p class="text-sm font-semibold text-gray-600 mb-2">{{ __('تاريخ الانتهاء') }}</p>
                <p class="text-xl font-semibold text-gray-900">{{ $agreement->end_date->format('Y-m-d') }}</p>
            </div>
            @endif
        </div>

        @if($agreement->description)
        <div class="mb-6 pt-6 border-t border-gray-200">
            <p class="text-sm font-semibold text-gray-600 mb-2">{{ __('الوصف') }}</p>
            <p class="text-gray-900 leading-relaxed">{{ $agreement->description }}</p>
        </div>
        @endif
    </div>

    <!-- شروط العقد -->
    @if($agreement->contract_terms)
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <h2 class="text-xl font-bold text-gray-900 mb-4">{{ __('شروط العقد') }}</h2>
        <div class="prose max-w-none">
            <p class="text-gray-900 whitespace-pre-wrap">{{ $agreement->contract_terms }}</p>
        </div>
    </div>
    @endif

    <!-- بنود الاتفاقية -->
    @if($agreement->agreement_terms)
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <h2 class="text-xl font-bold text-gray-900 mb-4">{{ __('بنود الاتفاقية') }}</h2>
        <div class="prose max-w-none">
            <p class="text-gray-900 whitespace-pre-wrap">{{ $agreement->agreement_terms }}</p>
        </div>
    </div>
    @endif

    <!-- الإحصائيات -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
            <p class="text-sm font-semibold text-gray-600 mb-2">{{ __('إجمالي الخصومات') }}</p>
            <p class="text-2xl font-bold text-red-600">{{ number_format($stats['total_deductions'], 2) }} {{ __('public.currency') }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
            <p class="text-sm font-semibold text-gray-600 mb-2">{{ __('إجمالي المدفوعات') }}</p>
            <p class="text-2xl font-bold text-green-600">{{ number_format($stats['total_payments'], 2) }} {{ __('public.currency') }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
            <p class="text-sm font-semibold text-gray-600 mb-2">{{ __('الدفعات المعلقة') }}</p>
            <p class="text-2xl font-bold text-yellow-600">{{ $stats['pending_payments'] }}</p>
        </div>
    </div>

    <!-- الخصومات -->
    @if($agreement->deductions->count() > 0)
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <h2 class="text-xl font-bold text-gray-900 mb-4">{{ __('الخصومات') }}</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">{{ __('رقم الخصم') }}</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">{{ __('العنوان') }}</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">{{ __('النوع') }}</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">{{ __('المبلغ') }}</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">{{ __('التاريخ') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($agreement->deductions as $deduction)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $deduction->deduction_number }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $deduction->title }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                @if($deduction->type === 'tax') {{ __('ضريبة') }}
                                @elseif($deduction->type === 'insurance') {{ __('تأمين') }}
                                @elseif($deduction->type === 'loan') {{ __('قرض') }}
                                @elseif($deduction->type === 'penalty') {{ __('غرامة') }}
                                @else {{ __('أخرى') }}
                                @endif
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-red-600">{{ number_format($deduction->amount, 2) }} {{ __('public.currency') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $deduction->deduction_date->format('Y-m-d') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- المدفوعات -->
    @if($agreement->payments->count() > 0)
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <h2 class="text-xl font-bold text-gray-900 mb-4">{{ __('سجل المدفوعات') }}</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">{{ __('رقم الدفعة') }}</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">{{ __('تاريخ الاستحقاق') }}</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">{{ __('الراتب الأساسي') }}</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">{{ __('الخصومات') }}</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">{{ __('صافي الراتب') }}</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">{{ __('الحالة') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($agreement->payments as $payment)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $payment->payment_number }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $payment->payment_date->format('Y-m-d') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($payment->base_salary, 2) }} {{ __('public.currency') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">{{ number_format($payment->total_deductions, 2) }} {{ __('public.currency') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600">{{ number_format($payment->net_salary, 2) }} {{ __('public.currency') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                @if($payment->status === 'paid') bg-green-100 text-green-800
                                @elseif($payment->status === 'pending') bg-yellow-100 text-yellow-800
                                @elseif($payment->status === 'overdue') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                @if($payment->status === 'paid') {{ __('مدفوعة') }}
                                @elseif($payment->status === 'pending') {{ __('معلقة') }}
                                @elseif($payment->status === 'overdue') {{ __('متأخرة') }}
                                @else {{ __('ملغاة') }}
                                @endif
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <div class="flex justify-end">
        <a href="{{ route('employee.agreements.index') }}" class="px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white rounded-lg font-medium transition-colors">
            <i class="fas fa-arrow-right mr-2"></i>العودة للقائمة
        </a>
    </div>
</div>
@endsection
