@extends('layouts.admin')

@section('title', 'إدارة المزايا المدفوعة')
@section('header', 'إدارة المزايا المدفوعة')

@section('content')
<div class="space-y-6">
    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">كل المزايا المدفوعة</h1>
                <p class="text-sm text-slate-600 mt-1">ادخل على أي ميزة لمراقبة المستخدمين والتحكم التفصيلي.</p>
            </div>
            <div class="flex gap-3 text-sm">
                <span class="px-3 py-1.5 rounded-lg bg-slate-100 text-slate-700">اشتراكات نشطة: <strong>{{ number_format($stats['active_subscriptions']) }}</strong></span>
                <span class="px-3 py-1.5 rounded-lg bg-emerald-100 text-emerald-700">طلاب نشطون: <strong>{{ number_format($stats['active_students_with_subscription']) }}</strong></span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @forelse($features as $feature)
            <a href="{{ route('admin.students-control.paid-features.show', $feature['key']) }}"
               class="rounded-2xl bg-white border border-slate-200 hover:border-sky-300 shadow-sm hover:shadow-md p-5 transition-all">
                <div class="flex items-center justify-between gap-3 mb-4">
                    <span class="w-12 h-12 rounded-xl {{ $feature['icon_bg'] }} {{ $feature['icon_text'] }} flex items-center justify-center">
                        <i class="fas {{ $feature['icon'] }}"></i>
                    </span>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-sky-100 text-sky-700">
                        {{ number_format($feature['users_count']) }} مستخدم
                    </span>
                </div>
                <h3 class="text-base font-bold text-slate-900">{{ $feature['label'] }}</h3>
                <p class="text-xs text-slate-500 mt-1">{{ $feature['key'] }}</p>
            </a>
        @empty
            <div class="col-span-full rounded-2xl bg-white border border-slate-200 p-10 text-center text-slate-500">
                لا توجد مزايا معرفة حالياً.
            </div>
        @endforelse
    </div>
</div>
@endsection

