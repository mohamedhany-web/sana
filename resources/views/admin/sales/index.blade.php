@extends('layouts.admin')

@section('title', 'تحليلات المبيعات')
@section('header', 'تحليلات المبيعات')

@section('content')
<div class="space-y-6">
    <div class="rounded-2xl bg-white border border-emerald-200 shadow-lg overflow-hidden">
        <div class="px-6 py-5 bg-gradient-to-l from-emerald-50 to-white border-b border-emerald-100 flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-900 flex items-center gap-3">
                    <span class="w-12 h-12 rounded-xl bg-emerald-600 text-white flex items-center justify-center shadow-md"><i class="fas fa-chart-pie"></i></span>
                    لوحة المبيعات
                </h1>
                <p class="text-sm text-slate-600 mt-2">إيرادات معتمدة، أداء المناديب، وأكثر الكورسات طلباً خلال الفترة المختارة.</p>
            </div>
            <form method="GET" class="flex flex-wrap items-end gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">من</label>
                    <input type="date" name="from" value="{{ $from->format('Y-m-d') }}" class="rounded-xl border border-slate-300 bg-white text-slate-900 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">إلى</label>
                    <input type="date" name="to" value="{{ $to->format('Y-m-d') }}" class="rounded-xl border border-slate-300 bg-white text-slate-900 px-3 py-2 text-sm">
                </div>
                <button type="submit" class="rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 text-sm font-bold">تحديث</button>
            </form>
        </div>
        <div class="p-6 grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="rounded-xl border border-slate-200 p-4 bg-slate-50/80">
                <p class="text-xs font-semibold text-slate-600">طلبات (خلال الفترة)</p>
                <p class="text-2xl font-black text-slate-900 tabular-nums mt-1">{{ number_format($stats['orders_total']) }}</p>
            </div>
            <div class="rounded-xl border border-amber-200 p-4 bg-amber-50/50">
                <p class="text-xs font-semibold text-amber-800">معلّقة الآن</p>
                <p class="text-2xl font-black text-amber-900 tabular-nums mt-1">{{ number_format($stats['pending']) }}</p>
            </div>
            <div class="rounded-xl border border-emerald-200 p-4 bg-emerald-50/50">
                <p class="text-xs font-semibold text-emerald-800">إيراد معتمد (الفترة)</p>
                <p class="text-xl font-black text-emerald-900 tabular-nums mt-1">{{ number_format($stats['revenue_period'], 2) }}</p>
            </div>
            <div class="rounded-xl border border-blue-200 p-4 bg-blue-50/50">
                <p class="text-xs font-semibold text-blue-800">إيراد معتمد (الشهر)</p>
                <p class="text-xl font-black text-blue-900 tabular-nums mt-1">{{ number_format($stats['revenue_month'], 2) }}</p>
            </div>
        </div>
        @if($stats['conversion'] !== null)
            <div class="px-6 pb-6">
                <p class="text-sm text-slate-700">نسبة التحويل التقريبية (معتمد ÷ طلبات جديدة في الفترة): <strong class="text-emerald-700">{{ $stats['conversion'] }}%</strong></p>
            </div>
        @endif
    </div>

    @if($unassignedPending > 0)
        <div class="rounded-xl border border-amber-300 bg-amber-50 px-4 py-3 text-sm text-amber-900">
            <i class="fas fa-exclamation-triangle ml-2"></i>
            يوجد <strong>{{ $unassignedPending }}</strong> طلب معلّق بدون مندوب مبيعات.
            <a href="{{ route('admin.orders.index', ['status' => 'pending', 'sales_owner_id' => 'unassigned']) }}" class="font-bold underline mr-2 text-amber-950 hover:text-amber-700">عرضها</a>
        </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <div class="rounded-2xl bg-white border border-slate-200 shadow-lg p-6">
            <h2 class="text-lg font-black text-slate-900 mb-4">طلبات جديدة يومياً</h2>
            <div class="flex items-end gap-1 h-40">
                @php
                    $maxD = max($daily->max() ?: 0, 1);
                @endphp
                @foreach($daily as $day => $count)
                    <div class="flex-1 min-w-0 flex flex-col items-center justify-end group">
                        <span class="text-[10px] text-slate-500 mb-1 tabular-nums">{{ $count }}</span>
                        <div class="w-full max-w-[24px] mx-auto rounded-t bg-emerald-500/80 group-hover:bg-emerald-600 transition-colors" style="height: {{ max(4, ($count / $maxD) * 100) }}%"></div>
                        <span class="text-[9px] text-slate-400 mt-1 truncate w-full text-center" title="{{ $day }}">{{ \Illuminate\Support\Str::afterLast($day, '-') }}</span>
                    </div>
                @endforeach
            </div>
            @if($daily->isEmpty())
                <p class="text-sm text-slate-500 text-center py-8">لا بيانات في هذه الفترة.</p>
            @endif
        </div>

        <div class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
                <h2 class="text-lg font-black text-slate-900">أكثر الكورسات إيراداً (معتمد)</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-slate-600 font-semibold">
                        <tr>
                            <th class="text-right px-4 py-3">الكورس</th>
                            <th class="text-right px-4 py-3">طلبات</th>
                            <th class="text-right px-4 py-3">إيراد معتمد</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($topCourses as $row)
                            <tr class=>
                                <td class="px-4 py-3 font-medium text-slate-900">{{ $row->course?->title ?? 'كورس #'.$row->advanced_course_id }}</td>
                                <td class="px-4 py-3 tabular-nums text-slate-700">{{ number_format($row->order_count) }}</td>
                                <td class="px-4 py-3 font-bold tabular-nums text-emerald-800">{{ number_format((float) $row->revenue, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-4 py-8 text-center text-slate-500">لا بيانات.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50 flex flex-wrap justify-between gap-2">
            <h2 class="text-lg font-black text-slate-900">أداء مناديب المبيعات</h2>
            <span class="text-xs text-slate-500">وظيفة «سيلز» فقط</span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-600 font-semibold">
                    <tr>
                        <th class="text-right px-4 py-3">المندوب</th>
                        <th class="text-right px-4 py-3">طلبات معلّقة مسندة</th>
                        <th class="text-right px-4 py-3">صفقات معتمدة (الفترة)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($repStats as $rep)
                        <tr class=>
                            <td class="px-4 py-3 font-medium text-slate-900">{{ $rep->name }}</td>
                            <td class="px-4 py-3 tabular-nums text-slate-700">{{ number_format($rep->owned_pending) }}</td>
                            <td class="px-4 py-3 font-bold tabular-nums text-emerald-800">{{ number_format($rep->owned_won_period) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="px-4 py-8 text-center text-slate-500">لا يوجد موظفون بوظيفة مبيعات أو لا بيانات.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="flex flex-wrap gap-3">
        <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center gap-2 rounded-xl bg-slate-800 hover:bg-slate-900 text-white px-5 py-2.5 text-sm font-bold">
            <i class="fas fa-shopping-bag"></i> إدارة الطلبات
        </a>
        <a href="{{ route('admin.coupons.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
            <i class="fas fa-ticket-alt"></i> الكوبونات
        </a>
    </div>
</div>
@endsection
