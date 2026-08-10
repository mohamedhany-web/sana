@extends('layouts.admin')

@section('title', 'تنفيذ الدفع للمدرب')
@section('header', 'تنفيذ الدفع للمدرب')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">تنفيذ الدفع وتحويل المبلغ</h1>
                <p class="text-gray-600 mt-1">رفع إيصال التحويل وتسجيل الدفع للمدرب</p>
            </div>
            <a href="{{ route('admin.salaries.instructor', $payment->instructor) }}" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-right mr-2"></i>العودة لماليات المدرب
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
            <h2 class="text-lg font-bold text-gray-900 mb-4">تفاصيل المدفوعة</h2>
            <dl class="space-y-3">
                <div><dt class="text-sm text-gray-500">رقم المدفوعة</dt><dd class="font-mono font-semibold">{{ $payment->payment_number }}</dd></div>
                <div><dt class="text-sm text-gray-500">المدرب</dt><dd class="font-semibold">{{ $payment->instructor->name ?? '—' }}</dd></div>
                <div><dt class="text-sm text-gray-500">الاتفاقية</dt><dd>{{ $payment->agreement->title ?? '—' }}</dd></div>
                <div><dt class="text-sm text-gray-500">المبلغ</dt><dd class="text-xl font-bold text-green-700">{{ number_format($payment->amount, 2) }} {{ __('public.currency') }}</dd></div>
            </dl>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
            <h2 class="text-lg font-bold text-gray-900 mb-4">حساب التحويل (بيانات المدرب)</h2>
            @include('admin.partials.instructor-payout-details', [
                'payoutDetail' => $payment->instructor?->payoutDetail,
            ])
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <h2 class="text-lg font-bold text-gray-900 mb-4">تسجيل الدفع ورفع إيصال التحويل</h2>
        <form action="{{ route('admin.salaries.mark-paid', $payment) }}" method="POST" enctype="multipart/form-data" class="space-y-4 max-w-xl">
            @csrf
            <div>
                <label for="transfer_receipt" class="block text-sm font-medium text-gray-700 mb-1">إيصال التحويل (مطلوب) *</label>
                <input type="file" name="transfer_receipt" id="transfer_receipt" accept=".pdf,.jpg,.jpeg,.png" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <p class="text-xs text-gray-500 mt-1">PDF أو صورة، حجم أقصى 40 ميجابايت</p>
                @error('transfer_receipt')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">ملاحظات (اختياري)</label>
                <textarea name="notes" id="notes" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('notes') }}</textarea>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium">
                    <i class="fas fa-check mr-2"></i>تسجيل الدفع ورفع الإيصال
                </button>
                <a href="{{ route('admin.salaries.instructor', $payment->instructor) }}" class="px-6 py-2.5 bg-gray-500 hover:bg-gray-600 text-white rounded-lg font-medium">إلغاء</a>
            </div>
        </form>
    </div>
</div>
@endsection
