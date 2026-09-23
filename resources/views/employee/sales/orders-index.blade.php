@extends('layouts.employee')

@section('title', __('طلبات المبيعات'))
@section('header', __('طلبات المبيعات'))

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap gap-3">
        <a href="{{ route('employee.sales.desk') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-gray-600 hover:text-gray-900">
            <i class="fas fa-arrow-right"></i> لوحة المبيعات
        </a>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-gray-600 mb-1">{{ __('بحث') }}</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('اسم، بريد، هاتف، رقم الطلب، كورس…') }}"
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">{{ __('الحالة') }}</label>
                <select name="status" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500">
                    <option value="">{{ __('الكل') }}</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>{{ __('معلّق') }}</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>{{ __('معتمد') }}</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>{{ __('مرفوض') }}</option>
                </select>
            </div>
            <div class="flex flex-wrap items-end gap-3">
                <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" name="mine" value="1" {{ request('mine') ? 'checked' : '' }} class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                    طلباتي فقط
                </label>
                <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" name="unassigned" value="1" {{ request('unassigned') ? 'checked' : '' }} class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                    بلا مندوب
                </label>
            </div>
            <div class="lg:col-span-4 flex flex-wrap gap-2">
                <button type="submit" class="px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold">{{ __('تطبيق') }}</button>
                <a href="{{ route('employee.sales.orders.index') }}" class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm font-semibold">{{ __('إعادة ضبط') }}</a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 font-semibold">
                    <tr>
                        <th class="text-right px-4 py-3">#</th>
                        <th class="text-right px-4 py-3">{{ __('العميل') }}</th>
                        <th class="text-right px-4 py-3">{{ __('الكورس') }}</th>
                        <th class="text-right px-4 py-3">{{ __('المبلغ') }}</th>
                        <th class="text-right px-4 py-3">{{ __('المندوب') }}</th>
                        <th class="text-right px-4 py-3">{{ __('الحالة') }}</th>
                        <th class="text-right px-4 py-3">{{ __('التاريخ') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($orders as $order)
                    <tr class="hover:bg-gray-50/80">
                        <td class="px-4 py-3">
                            <a href="{{ route('employee.sales.orders.show', $order) }}" class="font-bold text-emerald-700 hover:underline">#{{ $order->id }}</a>
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-900">{{ $order->user?->name ?? '—' }}</div>
                            <div class="text-xs text-gray-500">{{ $order->user?->email }}</div>
                        </td>
                        <td class="px-4 py-3 text-gray-800">{{ $order->course?->title ?? '—' }}</td>
                        <td class="px-4 py-3 font-semibold tabular-nums">{{ number_format((float) $order->amount, 2) }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $order->salesOwner?->name ?? '—' }}</td>
                        <td class="px-4 py-3">
                            @if($order->status === \App\Models\Order::STATUS_PENDING)
                                <span class="rounded-full bg-amber-100 text-amber-800 px-2 py-0.5 text-xs font-bold">{{ __('معلّق') }}</span>
                            @elseif($order->status === \App\Models\Order::STATUS_APPROVED)
                                <span class="rounded-full bg-emerald-100 text-emerald-800 px-2 py-0.5 text-xs font-bold">{{ __('معتمد') }}</span>
                            @else
                                <span class="rounded-full bg-rose-100 text-rose-800 px-2 py-0.5 text-xs font-bold">{{ __('مرفوض') }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-600 whitespace-nowrap">{{ $order->created_at?->format('Y-m-d H:i') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center text-gray-500">{{ __('لا توجد طلبات مطابقة.') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($orders->hasPages())
            <div class="px-4 py-3 border-t border-gray-100">{{ $orders->links() }}</div>
        @endif
    </div>
</div>
@endsection
