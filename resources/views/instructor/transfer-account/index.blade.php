@extends('layouts.app')

@section('title', __('instructor.transfer_account') . ' - ' . config('app.name', 'Sana'))
@section('header', __('instructor.transfer_account'))

@section('content')
@php
    $method = old('payout_method', $detail->payout_method ?: \App\Models\InstructorPayoutDetail::METHOD_INSTAPAY);
@endphp
<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;"
     x-data="{ method: @js($method) }">
    <section class="rounded-2xl bg-white backdrop-blur border-2 border-slate-200 shadow-xl overflow-hidden">
        <div class="px-5 py-6 sm:px-8 border-b border-slate-200 bg-gradient-to-r from-indigo-50 to-white">
            <h2 class="text-xl sm:text-2xl font-black text-slate-900 flex items-center gap-2">
                <i class="fas fa-money-bill-transfer text-indigo-600"></i>
                {{ __('instructor.transfer_account') }}
            </h2>
            <p class="text-sm text-slate-600 mt-2">اختر طريقة واحدة لاستلام مستحقاتك: <strong>InstaPay</strong> أو <strong>IBAN</strong> أو <strong>STC Pay</strong>. تظهر هذه البيانات للإدارة عند التحويل.</p>
        </div>

        @if(session('success'))
        <div class="mx-5 mt-5 p-4 bg-green-50 border border-green-200 rounded-xl text-green-800 text-sm">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        </div>
        @endif

        <form action="{{ route('instructor.transfer-account.store') }}" method="POST" class="p-5 sm:p-8 space-y-6" data-turbo="false">
            @csrf

            <div>
                <p class="block text-sm font-bold text-slate-800 mb-3">طريقة التحويل *</p>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    @foreach($methods as $value => $label)
                        <label class="relative cursor-pointer">
                            <input type="radio" name="payout_method" value="{{ $value }}" class="peer sr-only"
                                   x-model="method" @checked($method === $value)>
                            <span class="flex flex-col items-center gap-2 rounded-2xl border-2 border-slate-200 bg-white px-4 py-4 text-center peer-checked:border-indigo-600 peer-checked:bg-indigo-50 peer-checked:shadow-md transition">
                                <i class="fas {{ $value === 'iban' ? 'fa-building-columns' : ($value === 'stc_pay' ? 'fa-mobile-screen' : 'fa-bolt') }} text-xl text-indigo-600"></i>
                                <span class="font-black text-slate-900">{{ $label }}</span>
                            </span>
                        </label>
                    @endforeach
                </div>
                @error('payout_method')<p class="text-red-500 text-xs mt-2">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="account_holder_name" class="block text-sm font-semibold text-slate-700 mb-1">اسم صاحب الحساب *</label>
                <input type="text" name="account_holder_name" id="account_holder_name"
                       value="{{ old('account_holder_name', $detail->account_holder_name) }}" required
                       class="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                       placeholder="الاسم كما يظهر على الحساب">
                @error('account_holder_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div x-show="method === 'instapay' || method === 'stc_pay'" x-cloak class="space-y-3 rounded-2xl border border-indigo-100 bg-indigo-50/40 p-4">
                <label for="account_number" class="block text-sm font-semibold text-slate-700 mb-1">
                    <span x-show="method === 'instapay'">رقم / معرّف InstaPay *</span>
                    <span x-show="method === 'stc_pay'" x-cloak>رقم STC Pay *</span>
                </label>
                <input type="text" name="account_number" id="account_number" dir="ltr"
                       value="{{ old('account_number', in_array($detail->payout_method, ['instapay', 'stc_pay'], true) ? $detail->account_number : '') }}"
                       class="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500"
                       :placeholder="method === 'stc_pay' ? '05xxxxxxxx' : 'رقم الموبايل أو IPA'">
                <p class="text-xs text-slate-500 m-0" x-show="method === 'instapay'">المعرّف الذي تستقبل عليه التحويلات عبر InstaPay.</p>
                <p class="text-xs text-slate-500 m-0" x-show="method === 'stc_pay'" x-cloak>رقم الجوال المسجّل في STC Pay.</p>
                @error('account_number')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div x-show="method === 'iban'" x-cloak class="space-y-4 rounded-2xl border border-sky-100 bg-sky-50/40 p-4">
                <div>
                    <label for="iban" class="block text-sm font-semibold text-slate-700 mb-1">IBAN *</label>
                    <input type="text" name="iban" id="iban" dir="ltr"
                           value="{{ old('iban', $detail->iban) }}"
                           class="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500 font-mono"
                           placeholder="SAxxxxxxxxxxxxxxxxxxxxxxxx">
                    @error('iban')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="bank_name" class="block text-sm font-semibold text-slate-700 mb-1">اسم البنك (اختياري)</label>
                    <input type="text" name="bank_name" id="bank_name"
                           value="{{ old('bank_name', $detail->bank_name) }}"
                           class="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500"
                           placeholder="مثال: الراجحي / الأهلي">
                </div>
            </div>

            <div>
                <label for="notes" class="block text-sm font-semibold text-slate-700 mb-1">ملاحظات للإدارة (اختياري)</label>
                <textarea name="notes" id="notes" rows="2" class="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500"
                          placeholder="أي توضيح إضافي للتحويل">{{ old('notes', $detail->notes) }}</textarea>
            </div>

            <div class="pt-2">
                <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold transition-colors">
                    <i class="fas fa-save mr-2"></i>حفظ بيانات التحويل
                </button>
            </div>
        </form>
    </section>
</div>
@endsection
