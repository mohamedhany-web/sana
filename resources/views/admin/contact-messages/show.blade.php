@extends('layouts.admin')

@section('title', 'عرض الرسالة - ' . ($platformName ?? config('brand.name', config('app.name'))))
@section('page_title', 'عرض الرسالة')

@section('content')
<div class="admin-dashboard admin-list-page space-y-7">

    <x-admin.page-hero
        :title="$contactMessage->subject"
        :subtitle="'من: ' . $contactMessage->name . ' · ' . $contactMessage->created_at->translatedFormat('d F Y — H:i')"
        icon="fas fa-envelope-open-text"
    >
        <a href="{{ route('admin.contact-messages.index') }}" class="admin-btn admin-btn--ghost">
            <i class="fas fa-arrow-right"></i>
            العودة للقائمة
        </a>
    </x-admin.page-hero>

    <div class="admin-panel max-w-4xl">
        <div class="admin-panel__head">
            <h2><i class="fas fa-user"></i> بيانات المرسل</h2>
            @if($contactMessage->read_at)
                <span class="admin-badge admin-badge--success"><i class="fas fa-check-circle"></i> مقروءة</span>
            @else
                <span class="admin-badge admin-badge--danger"><i class="fas fa-circle text-[6px]"></i> غير مقروءة</span>
            @endif
        </div>
        <div class="admin-panel__body space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="admin-detail-field">
                    <p class="admin-detail-field__label">الاسم</p>
                    <p class="admin-detail-field__value">{{ $contactMessage->name }}</p>
                </div>
                <div class="admin-detail-field">
                    <p class="admin-detail-field__label">البريد الإلكتروني</p>
                    <p class="admin-detail-field__value">{{ $contactMessage->email }}</p>
                </div>
                @if($contactMessage->phone)
                <div class="admin-detail-field">
                    <p class="admin-detail-field__label">رقم الهاتف</p>
                    <p class="admin-detail-field__value">{{ $contactMessage->phone }}</p>
                </div>
                @endif
                <div class="admin-detail-field">
                    <p class="admin-detail-field__label">تاريخ الإرسال</p>
                    <p class="admin-detail-field__value">{{ $contactMessage->created_at->format('Y-m-d H:i') }}</p>
                </div>
                @if($contactMessage->read_at)
                <div class="admin-detail-field">
                    <p class="admin-detail-field__label">تاريخ القراءة</p>
                    <p class="admin-detail-field__value">{{ $contactMessage->read_at->format('Y-m-d H:i') }}</p>
                </div>
                @endif
            </div>

            <div class="pt-2 border-t border-slate-100">
                <p class="text-xs font-bold text-slate-500 mb-2">نص الرسالة</p>
                <div class="admin-message-body">{{ $contactMessage->message }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
