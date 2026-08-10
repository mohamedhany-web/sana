@extends('layouts.admin')
@section('title', 'طلب شراء ساعات #'.$purchase->id)
@section('header', 'طلب شراء ساعات')
@section('content')
@php
    $walletTypeLabels = [
        'vodafone_cash' => 'فودافون كاش',
        'instapay' => 'إنستاباي',
        'bank_transfer' => 'تحويل بنكي',
    ];
@endphp
<div class="space-y-6">
    @include('admin.tutor-lessons._nav')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-4">
            <div class="bg-white border border-slate-200 rounded-2xl p-6 space-y-3">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <h3 class="font-black text-slate-900 text-lg m-0">{{ $purchase->plan_name }}</h3>
                    <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold
                        {{ $purchase->status === 'approved' ? 'bg-emerald-100 text-emerald-800' : ($purchase->status === 'rejected' ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800') }}">
                        {{ $purchase->statusLabel() }}
                    </span>
                </div>
                <dl class="grid grid-cols-2 gap-3 text-sm">
                    <div><dt class="text-slate-500 text-xs">الطالب</dt><dd class="font-bold m-0">{{ $purchase->user?->name }}</dd></div>
                    <div><dt class="text-slate-500 text-xs">البريد</dt><dd class="font-bold m-0 break-all">{{ $purchase->user?->email }}</dd></div>
                    <div><dt class="text-slate-500 text-xs">الساعات</dt><dd class="font-bold m-0">{{ $purchase->hours }}</dd></div>
                    <div><dt class="text-slate-500 text-xs">المبلغ</dt><dd class="font-bold m-0">{{ number_format((float) $purchase->price, 0) }} {{ __('public.currency') }}</dd></div>
                    <div><dt class="text-slate-500 text-xs">طريقة الدفع</dt><dd class="font-bold m-0">{{ $purchase->payment_method === 'wallet' ? 'محفظة' : 'تحويل بنكي' }}</dd></div>
                    <div><dt class="text-slate-500 text-xs">التاريخ</dt><dd class="font-bold m-0">{{ $purchase->created_at?->format('Y-m-d H:i') }}</dd></div>
                    @if($purchase->wallet)
                        <div class="col-span-2">
                            <dt class="text-slate-500 text-xs">المحفظة</dt>
                            <dd class="font-bold m-0">
                                {{ $purchase->wallet->name ?: ($walletTypeLabels[$purchase->wallet->type] ?? $purchase->wallet->type) }}
                                @if($purchase->wallet->account_number) — {{ $purchase->wallet->account_number }} @endif
                            </dd>
                        </div>
                    @endif
                    @if($purchase->notes)
                        <div class="col-span-2"><dt class="text-slate-500 text-xs">ملاحظات الطالب</dt><dd class="m-0">{{ $purchase->notes }}</dd></div>
                    @endif
                    @if($purchase->admin_notes)
                        <div class="col-span-2"><dt class="text-slate-500 text-xs">ملاحظات الإدارة</dt><dd class="m-0">{{ $purchase->admin_notes }}</dd></div>
                    @endif
                </dl>
            </div>

            @if($purchase->payment_proof)
                <div class="bg-white border border-slate-200 rounded-2xl p-6">
                    <h4 class="font-bold text-slate-800 mb-3">إيصال الدفع</h4>
                    @php
                        $imageUrl = public_storage_url($purchase->payment_proof);
                    @endphp
                    <a href="{{ $imageUrl }}" target="_blank" rel="noopener" class="block">
                        <img src="{{ $imageUrl }}" alt="إيصال" class="max-h-96 rounded-xl border border-slate-200 object-contain bg-slate-50">
                    </a>
                </div>
            @endif
        </div>

        <div class="space-y-4">
            @if($purchase->isPending())
                <form method="post" action="{{ route('admin.tutor-lessons.hour-purchases.approve', $purchase) }}" class="bg-white border border-emerald-200 rounded-2xl p-5 space-y-3" data-turbo="false">
                    @csrf
                    <h4 class="font-black text-emerald-800 m-0">قبول وإضافة الساعات</h4>
                    <p class="text-xs text-slate-600 m-0">ستُضاف <strong>{{ $purchase->hours }}</strong> ساعة إلى رصيد الطالب فوراً.</p>
                    <textarea name="admin_notes" rows="2" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" placeholder="ملاحظات (اختياري)">{{ old('admin_notes') }}</textarea>
                    <button type="submit" class="w-full px-4 py-2.5 rounded-xl bg-emerald-600 text-white text-sm font-bold hover:bg-emerald-700">قبول الطلب</button>
                </form>
                <form method="post" action="{{ route('admin.tutor-lessons.hour-purchases.reject', $purchase) }}" class="bg-white border border-rose-200 rounded-2xl p-5 space-y-3" data-turbo="false">
                    @csrf
                    <h4 class="font-black text-rose-800 m-0">رفض الطلب</h4>
                    <textarea name="admin_notes" rows="2" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" placeholder="سبب الرفض (اختياري)">{{ old('admin_notes') }}</textarea>
                    <button type="submit" class="w-full px-4 py-2.5 rounded-xl bg-rose-600 text-white text-sm font-bold hover:bg-rose-700">رفض</button>
                </form>
            @else
                <div class="bg-slate-50 border border-slate-200 rounded-2xl p-5 text-sm text-slate-600">
                    تمت معالجة هذا الطلب
                    @if($purchase->approver)
                        بواسطة {{ $purchase->approver->name }}
                    @endif
                    @if($purchase->approved_at)
                        في {{ $purchase->approved_at->format('Y-m-d H:i') }}
                    @endif
                </div>
            @endif
            <a href="{{ route('admin.tutor-lessons.hour-purchases.index') }}" class="inline-flex text-sm font-bold text-violet-600 hover:underline">← العودة للقائمة</a>
        </div>
    </div>
</div>
@endsection
