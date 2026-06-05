@extends('layouts.admin')

@section('title', 'الكوبونات والخصومات')
@section('header', '')

@section('content')
<div class="space-y-6">
    {{-- رأس الصفحة --}}
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 font-heading">
                <i class="fas fa-ticket-alt text-violet-500 ml-2"></i>الكوبونات والخصومات
            </h1>
            <p class="text-sm text-slate-500 mt-1">إدارة أكواد الخصم والاستخدامات</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.marketing.student-wallet-credit.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-sky-600 hover:bg-sky-700 text-white rounded-xl font-semibold text-sm transition-all">
                <i class="fas fa-wallet"></i> رصيد محفظة طالب
            </a>
            <a href="{{ route('admin.coupons.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-violet-600 hover:bg-violet-700 text-white rounded-xl font-semibold shadow-lg shadow-violet-500/25 transition-all">
                <i class="fas fa-plus"></i> إضافة كوبون جديد
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 text-emerald-700 text-sm">
        <i class="fas fa-check-circle ml-1"></i> {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-red-700 text-sm">
        <i class="fas fa-exclamation-circle ml-1"></i> {{ session('error') }}
    </div>
    @endif

    {{-- إحصائيات --}}
    @if(isset($stats))
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
            <div class="text-sm text-slate-500">إجمالي الكوبونات</div>
            <div class="text-2xl font-bold text-slate-800 mt-1">{{ $stats['total'] ?? 0 }}</div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
            <div class="text-sm text-slate-500">نشطة</div>
            <div class="text-2xl font-bold text-emerald-600 mt-1">{{ $stats['active'] ?? 0 }}</div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
            <div class="text-sm text-slate-500">منتهية/غير نشطة</div>
            <div class="text-2xl font-bold text-rose-600 mt-1">{{ $stats['expired'] ?? 0 }}</div>
        </div>
    </div>
    @endif

    {{-- تصفية وبحث --}}
    <form method="GET" action="{{ route('admin.coupons.index') }}" class="bg-white rounded-xl border border-slate-200 p-4 flex flex-wrap items-end gap-3">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs font-medium text-slate-600 mb-1">بحث</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="كود أو عنوان..." class="w-full rounded-lg border-slate-300 text-sm">
        </div>
        <div class="w-full sm:w-40">
            <label class="block text-xs font-medium text-slate-600 mb-1">الحالة</label>
            <select name="status" class="w-full rounded-lg border-slate-300 text-sm">
                <option value="">الكل</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>نشط</option>
                <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>منتهي</option>
            </select>
        </div>
        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-white text-sm font-medium">
            <i class="fas fa-filter"></i> تطبيق
        </button>
        @if(request()->hasAny(['search', 'status']))
        <a href="{{ route('admin.coupons.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-slate-300 text-slate-600 text-sm hover:bg-slate-50">إعادة تعيين</a>
        @endif
    </form>

    {{-- الجدول --}}
    @if(isset($coupons) && $coupons->count() > 0)
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 text-right font-semibold text-slate-600">الكود</th>
                        <th class="px-4 py-3 text-right font-semibold text-slate-600">العنوان</th>
                        <th class="px-4 py-3 text-right font-semibold text-slate-600">نوع الخصم</th>
                        <th class="px-4 py-3 text-right font-semibold text-slate-600">القيمة</th>
                        <th class="px-4 py-3 text-right font-semibold text-slate-600">الاستخدامات</th>
                        <th class="px-4 py-3 text-right font-semibold text-slate-600">الحالة</th>
                        <th class="px-4 py-3 text-right font-semibold text-slate-600">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($coupons as $coupon)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-4 py-3 text-slate-900 font-mono font-medium">{{ $coupon->code }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $coupon->title ?? $coupon->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-slate-600">
                            {{ $coupon->discount_type == 'percentage' ? 'نسبة مئوية' : 'مبلغ ثابت' }}
                        </td>
                        <td class="px-4 py-3 text-slate-900 font-medium">
                            {{ $coupon->discount_type == 'percentage' ? $coupon->discount_value . '%' : number_format($coupon->discount_value, 2) . currency_suffix() }}
                        </td>
                        <td class="px-4 py-3 text-slate-600">
                            <span title="استخدامات فعلية">{{ $coupon->totalUsageCount() }}</span>
                            @if($coupon->usage_limit)
                                / {{ $coupon->usage_limit }}
                            @else
                                <span class="text-slate-400">/ ∞</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @php
                                $isActive = $coupon->is_active && (!$coupon->expires_at || $coupon->expires_at >= now());
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $isActive ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                {{ $isActive ? 'نشط' : 'منتهي' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex flex-wrap items-center gap-2">
                                <a href="{{ route('admin.coupons.show', $coupon) }}" class="text-violet-600 hover:underline text-xs font-medium">عرض</a>
                                <a href="{{ route('admin.coupons.edit', $coupon) }}" class="text-slate-600 hover:underline text-xs font-medium">تعديل</a>
                                <form method="POST" action="{{ route('admin.coupons.destroy', $coupon) }}" class="inline" onsubmit="return confirm('حذف هذا الكوبون؟');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-rose-600 hover:underline text-xs font-medium">حذف</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-slate-200 bg-slate-50/50">
            {{ $coupons->links() }}
        </div>
    </div>
    @else
    <div class="bg-white rounded-xl border border-slate-200 p-12 text-center">
        <i class="fas fa-ticket-alt text-slate-300 text-5xl mb-4"></i>
        <p class="text-slate-600 font-medium">لا توجد كوبونات</p>
        <p class="text-sm text-slate-500 mt-1">أضف أول كوبون أو غيّر معايير البحث</p>
        <a href="{{ route('admin.coupons.create') }}" class="inline-flex items-center gap-2 mt-4 px-5 py-2.5 bg-violet-600 hover:bg-violet-700 text-white rounded-xl font-semibold text-sm">
            <i class="fas fa-plus"></i> إضافة كوبون
        </a>
    </div>
    @endif
</div>
@endsection
