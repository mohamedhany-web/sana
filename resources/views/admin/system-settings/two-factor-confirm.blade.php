@extends('layouts.admin')

@section('title', 'تأكيد المصادقة الثنائية')
@section('page_title', 'تأكيد المصادقة الثنائية')

@section('content')
<div class="admin-dashboard admin-list-page admin-form-page max-w-2xl mx-auto space-y-6 pb-10">

    @include('admin.partials.alert-success')
    @include('admin.partials.alert-errors')

    <x-admin.page-hero
        title="تأكيد عبر البريد"
        subtitle="أدخل الرمز المكوّن من 6 أرقام المرسل إلى بريدك."
        icon="fas fa-envelope-open-text"
    >
        <a href="{{ route('admin.system-settings.edit') }}" class="admin-btn admin-btn--ghost">
            <i class="fas fa-arrow-right"></i>
            رجوع
        </a>
    </x-admin.page-hero>

    <div class="admin-panel">
        <div class="admin-panel__head">
            <h2><i class="fas fa-key"></i> رمز التحقق</h2>
            @if($userEmail)
                <p class="admin-panel__sub" dir="ltr">{{ $userEmail }}</p>
            @endif
        </div>
        <div class="admin-panel__body space-y-6">
            <form method="post" action="{{ route('admin.system-settings.two-factor.confirm.submit') }}" class="space-y-5">
                @csrf
                <div class="admin-field max-w-xs">
                    <label for="code">الرمز</label>
                    <input type="text" name="code" id="code" value="{{ old('code') }}" required maxlength="10"
                           autocomplete="one-time-code" inputmode="numeric"
                           class="admin-input text-center text-2xl tracking-[0.35em] font-black admin-input--mono"
                           placeholder="000000" dir="ltr">
                    <p class="admin-field-hint">صالح 15 دقيقة.</p>
                </div>
                <div class="admin-form-actions !border-0 !pt-0 !mt-0">
                    <button type="submit" class="admin-btn admin-btn--primary">
                        <i class="fas fa-check"></i>
                        تأكيد التفعيل
                    </button>
                    <a href="{{ route('admin.system-settings.edit') }}" class="admin-btn admin-btn--outline">إلغاء</a>
                </div>
            </form>
            <div class="pt-4 border-t border-slate-100">
                <p class="text-xs text-slate-500 mb-2">لم يصلك الرمز؟</p>
                <form method="post" action="{{ route('admin.system-settings.two-factor.resend') }}" class="inline">
                    @csrf
                    <button type="submit" class="admin-btn admin-btn--ghost text-sm">
                        <i class="fas fa-redo"></i>
                        إعادة الإرسال
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
