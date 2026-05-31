@extends('layouts.admin')

@section('title', 'تفاصيل الإحالة')
@section('header', '')

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div class="flex items-start gap-3">
            <a href="{{ route('admin.referrals.index') }}" class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-500 transition-colors"><i class="fas fa-arrow-right"></i></a>
            <div>
                <h1 class="text-2xl font-bold text-slate-800 dark:text-white font-heading">
                    <i class="fas fa-user-friends text-emerald-500 ml-2"></i>تفاصيل الإحالة
                </h1>
                <p class="text-slate-600 dark:text-slate-300 mt-1">متابعة المحيل والمحال والخصم والمكافأة</p>
                <div class="flex flex-wrap gap-2 mt-3">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        @if($referral->status == 'completed') bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400
                        @elseif($referral->status == 'pending') bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-400
                        @else bg-rose-100 dark:bg-rose-900/40 text-rose-700 dark:text-rose-400
                        @endif">
                        @if($referral->status == 'completed') مكتملة
                        @elseif($referral->status == 'pending') قيد الانتظار
                        @else ملغاة
                        @endif
                    </span>
                    @if($referral->referralProgram)
                    <a href="{{ route('admin.referral-programs.show', $referral->referralProgram) }}" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-violet-100 dark:bg-violet-900/40 text-violet-700 dark:text-violet-300 hover:underline">
                        {{ $referral->referralProgram->name }}
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="grid md:grid-cols-2 gap-5">
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6 shadow-sm">
            <h2 class="font-bold text-slate-800 dark:text-white mb-4 flex items-center gap-2"><i class="fas fa-user-check text-emerald-500"></i> المحيل</h2>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between gap-4"><dt class="text-slate-500">الاسم</dt><dd class="font-medium text-slate-800 dark:text-white">{{ $referral->referrer->name ?? 'غير معروف' }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-slate-500">الهاتف</dt><dd class="font-mono text-slate-800 dark:text-white">{{ $referral->referrer->phone ?? '—' }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-slate-500">البريد</dt><dd class="text-slate-800 dark:text-white">{{ $referral->referrer->email ?? '—' }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-slate-500">كود الإحالة</dt><dd class="font-mono font-bold text-emerald-600 dark:text-emerald-400">{{ $referral->referrer->referral_code ?? '—' }}</dd></div>
            </dl>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6 shadow-sm">
            <h2 class="font-bold text-slate-800 dark:text-white mb-4 flex items-center gap-2"><i class="fas fa-user-plus text-violet-500"></i> المحال</h2>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between gap-4"><dt class="text-slate-500">الاسم</dt><dd class="font-medium text-slate-800 dark:text-white">{{ $referral->referred->name ?? 'غير معروف' }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-slate-500">الهاتف</dt><dd class="font-mono text-slate-800 dark:text-white">{{ $referral->referred->phone ?? '—' }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-slate-500">البريد</dt><dd class="text-slate-800 dark:text-white">{{ $referral->referred->email ?? '—' }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-slate-500">تاريخ التسجيل</dt><dd>{{ $referral->referred->created_at->format('d/m/Y') }}</dd></div>
            </dl>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6 shadow-sm">
        <h2 class="font-bold text-slate-800 dark:text-white mb-4 flex items-center gap-2"><i class="fas fa-info-circle text-emerald-500"></i> تفاصيل الإحالة</h2>
        <dl class="grid md:grid-cols-2 gap-x-6 gap-y-3 text-sm">
            <div class="flex justify-between gap-4"><dt class="text-slate-500">كود الإحالة</dt><dd class="font-mono font-bold text-slate-800 dark:text-white">{{ $referral->referral_code ?? $referral->code ?? '—' }}</dd></div>
            <div class="flex justify-between gap-4"><dt class="text-slate-500">تاريخ الإحالة</dt><dd>{{ $referral->created_at->format('d/m/Y H:i') }}</dd></div>
            @if($referral->completed_at)
            <div class="flex justify-between gap-4"><dt class="text-slate-500">تاريخ الإكمال</dt><dd>{{ $referral->completed_at->format('d/m/Y H:i') }}</dd></div>
            @endif
            <div class="flex justify-between gap-4"><dt class="text-slate-500">الخصم المطبق</dt><dd class="font-bold text-violet-600 dark:text-violet-400">{{ number_format($referral->discount_amount ?? 0, 2) }} {{ __('public.currency') }}</dd></div>
            <div class="flex justify-between gap-4"><dt class="text-slate-500">استخدام الخصم</dt><dd>{{ $referral->discount_used_count ?? 0 }} / {{ $referral->referralProgram->max_discount_uses_per_referred ?? 1 }}</dd></div>
            @if($referral->discount_expires_at)
            <div class="flex justify-between gap-4"><dt class="text-slate-500">انتهاء الخصم</dt><dd class="{{ $referral->discount_expires_at < now() ? 'text-rose-600 dark:text-rose-400' : '' }}">{{ $referral->discount_expires_at->format('d/m/Y H:i') }}</dd></div>
            @endif
            <div class="flex justify-between gap-4"><dt class="text-slate-500">المكافأة</dt><dd class="font-bold text-emerald-600 dark:text-emerald-400">{{ number_format($referral->reward_amount ?? 0, 2) }} {{ __('public.currency') }}</dd></div>
            @if(($referral->reward_points ?? 0) > 0)
            <div class="flex justify-between gap-4"><dt class="text-slate-500">نقاط المكافأة</dt><dd class="font-bold text-amber-600 dark:text-amber-400">{{ number_format($referral->reward_points) }}</dd></div>
            @endif
            @if($referral->autoCoupon)
            <div class="flex justify-between gap-4"><dt class="text-slate-500">كوبون تلقائي</dt><dd class="font-mono font-bold text-slate-800 dark:text-white">{{ $referral->autoCoupon->code }}</dd></div>
            @endif
        </dl>
    </div>

    @if($referral->invoice)
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6 shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 class="font-bold text-slate-800 dark:text-white mb-1 flex items-center gap-2"><i class="fas fa-file-invoice text-emerald-500"></i> الفاتورة المرتبطة</h2>
                <p class="text-sm text-slate-500">رقم الفاتورة: <span class="font-mono font-bold text-slate-800 dark:text-white">{{ $referral->invoice->invoice_number }}</span></p>
            </div>
            <a href="{{ route('admin.invoices.show', $referral->invoice) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold transition-colors">
                <i class="fas fa-eye"></i> عرض الفاتورة
            </a>
        </div>
    </div>
    @endif
</div>
@endsection
