@extends('layouts.app')

@section('title', 'شراء ساعات — '.$plan['label'])
@section('header', 'شراء ساعات')

@include('student.tutor-lessons.partials.dashboard-styles')

@section('content')
@php
    $currency = __('public.currency');
    $walletTypeLabels = [
        'vodafone_cash' => 'فودافون كاش',
        'instapay' => 'إنستاباي',
        'bank_transfer' => 'تحويل بنكي',
    ];
@endphp

<div class="sd-page space-y-6 pb-8 w-full">
    <div class="sd-hero">
        <div class="sd-hero-main relative z-[1]">
            <p class="text-xs font-bold sd-tag mb-2">إتمام الشراء</p>
            <h1 class="font-heading text-2xl sm:text-3xl font-black text-slate-800 leading-tight">
                {{ $plan['label'] ?? 'باقة الساعات' }}
            </h1>
            <p class="text-slate-600 text-sm mt-2 max-w-2xl">
                حوّل المبلغ ثم ارفع صورة الإيصال. بعد موافقة الأدمن تُضاف <strong>{{ $hours }}</strong> ساعة إلى رصيدك.
            </p>
            <div class="flex flex-wrap gap-2 mt-4">
                <a href="{{ route('student.tutor-lessons.hours') }}" class="sd-btn-outline">
                    <i class="fas fa-arrow-right text-xs"></i> العودة للساعات
                </a>
            </div>
        </div>
        <div class="sd-motivation">
            <p class="font-bold text-sm">المبلغ المطلوب</p>
            <p class="text-3xl font-black tabular-nums">{{ number_format((float) ($plan['price'] ?? 0), 0) }} <span class="text-base">{{ $currency }}</span></p>
            <p class="text-xs text-white/85">{{ $hours }} ساعة · {{ $billingLabel }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-1 space-y-4">
            <div class="sd-panel">
                <div class="sd-panel-head"><h2 class="font-heading font-bold text-slate-800 m-0">ملخص</h2></div>
                <div class="sd-panel-body space-y-2 text-sm">
                    <p class="m-0 flex justify-between gap-2"><span class="text-slate-500">الباقة</span><strong>{{ $plan['label'] }}</strong></p>
                    <p class="m-0 flex justify-between gap-2"><span class="text-slate-500">الساعات</span><strong>{{ $hours }}</strong></p>
                    <p class="m-0 flex justify-between gap-2"><span class="text-slate-500">السعر</span><strong>{{ number_format((float) $plan['price'], 0) }} {{ $currency }}</strong></p>
                </div>
            </div>

            @if($wallets->isNotEmpty())
                <div class="sd-panel">
                    <div class="sd-panel-head"><h2 class="font-heading font-bold text-slate-800 m-0">حسابات التحويل</h2></div>
                    <div class="sd-panel-body space-y-3">
                        @foreach($wallets as $wallet)
                            <div class="rounded-xl border border-slate-200 p-3 text-sm">
                                <p class="font-bold text-slate-800 m-0">{{ $wallet->name ?: ($walletTypeLabels[$wallet->type] ?? $wallet->type) }}</p>
                                <p class="text-xs text-slate-500 m-0 mt-1">{{ $walletTypeLabels[$wallet->type] ?? $wallet->type }}</p>
                                @if($wallet->account_number)
                                    <p class="font-mono text-sm font-bold text-slate-900 mt-2 m-0 dir-ltr text-left">{{ $wallet->account_number }}</p>
                                @endif
                                @if($wallet->instructions)
                                    <p class="text-xs text-slate-600 mt-2 m-0">{{ $wallet->instructions }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <div class="xl:col-span-2">
            <form method="post" action="{{ route('student.tutor-lessons.hours.purchase', $planKey) }}" enctype="multipart/form-data" class="sd-panel sd-form" data-turbo="false">
                <div class="sd-panel-head">
                    <h2 class="font-heading font-bold text-slate-800 m-0">رفع إيصال الدفع</h2>
                </div>
                <div class="sd-panel-body space-y-5">
                    @csrf

                    @if($errors->any())
                        <div class="sd-alert sd-alert-error space-y-1">
                            @foreach($errors->all() as $e)
                                <p class="m-0"><i class="fas fa-exclamation-circle"></i> {{ $e }}</p>
                            @endforeach
                        </div>
                    @endif

                    <div>
                        <label class="mb-2">طريقة الدفع *</label>
                        <select name="payment_method" id="payment_method" required class="w-full">
                            <option value="bank_transfer" @selected(old('payment_method') === 'bank_transfer')>تحويل بنكي</option>
                            <option value="wallet" @selected(old('payment_method', $wallets->isNotEmpty() ? 'wallet' : 'bank_transfer') === 'wallet')>محفظة / إنستاباي</option>
                        </select>
                    </div>

                    <div id="wallet_id_wrap" class="{{ old('payment_method', $wallets->isNotEmpty() ? 'wallet' : 'bank_transfer') === 'wallet' ? '' : 'hidden' }}">
                        <label class="mb-2">المحفظة التي حوّلت إليها *</label>
                        <select name="wallet_id" id="wallet_id" class="w-full">
                            <option value="">— اختر —</option>
                            @foreach($wallets as $wallet)
                                <option value="{{ $wallet->id }}" @selected((string) old('wallet_id') === (string) $wallet->id)>
                                    {{ $wallet->name ?: ($walletTypeLabels[$wallet->type] ?? $wallet->type) }}
                                    @if($wallet->account_number) — {{ $wallet->account_number }} @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="mb-2">صورة إيصال التحويل *</label>
                        <input type="file" name="payment_proof" accept="image/jpeg,image/png,image/jpg" required>
                        <p class="text-xs text-slate-500 mt-1">jpeg أو png</p>
                    </div>

                    <div>
                        <label class="mb-2">ملاحظات (اختياري)</label>
                        <textarea name="notes" rows="3" maxlength="1000" class="w-full">{{ old('notes') }}</textarea>
                    </div>

                    <button type="submit" class="sd-btn-primary w-full justify-center">
                        <i class="fas fa-paper-plane"></i>
                        إرسال الطلب للمراجعة
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function () {
    var method = document.getElementById('payment_method');
    var wrap = document.getElementById('wallet_id_wrap');
    var walletSelect = document.getElementById('wallet_id');
    function sync() {
        if (!method || !wrap) return;
        var isWallet = method.value === 'wallet';
        wrap.classList.toggle('hidden', !isWallet);
        if (walletSelect) walletSelect.required = isWallet;
    }
    if (method) {
        method.addEventListener('change', sync);
        sync();
    }
})();
</script>
@endpush
@endsection
