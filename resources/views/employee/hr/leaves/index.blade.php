@extends('layouts.employee')

@section('title', __('طلبات الإجازات — الموارد البشرية'))
@section('header', __('مراجعة طلبات الإجازات'))

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <a href="{{ route('employee.hr-desk.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-gray-600 hover:text-gray-900">
            <i class="fas fa-arrow-right"></i> لوحة الموارد البشرية
        </a>
    </div>

    @if(session('success'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="rounded-xl bg-rose-50 border border-rose-200 text-rose-900 px-4 py-3 text-sm">{{ session('error') }}</div>
    @endif

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs font-semibold text-gray-500">{{ __('الإجمالي') }}</p>
            <p class="text-2xl font-black text-slate-900 tabular-nums">{{ $stats['total'] }}</p>
        </div>
        <div class="rounded-2xl border border-amber-200 bg-amber-50/50 p-4 shadow-sm">
            <p class="text-xs font-semibold text-amber-800">{{ __('معلّقة') }}</p>
            <p class="text-2xl font-black text-amber-900 tabular-nums">{{ $stats['pending'] }}</p>
        </div>
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50/50 p-4 shadow-sm">
            <p class="text-xs font-semibold text-emerald-800">{{ __('موافق') }}</p>
            <p class="text-2xl font-black text-emerald-900 tabular-nums">{{ $stats['approved'] }}</p>
        </div>
        <div class="rounded-2xl border border-rose-200 bg-rose-50/50 p-4 shadow-sm">
            <p class="text-xs font-semibold text-rose-800">{{ __('مرفوض') }}</p>
            <p class="text-2xl font-black text-rose-900 tabular-nums">{{ $stats['rejected'] }}</p>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-gray-600 mb-1">{{ __('بحث') }}</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('اسم، بريد، رمز موظف…') }}"
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-rose-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">{{ __('الموظف') }}</label>
                <select name="employee_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    <option value="">{{ __('الكل') }}</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" {{ (string) request('employee_id') === (string) $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">{{ __('الحالة') }}</label>
                <select name="status" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    <option value="">{{ __('الكل') }}</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>{{ __('معلّقة') }}</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>{{ __('موافق') }}</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>{{ __('مرفوض') }}</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>{{ __('ملغاة') }}</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 px-4 py-2 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-sm font-bold">{{ __('تطبيق') }}</button>
                <a href="{{ route('employee.hr.leaves.index') }}" class="px-3 py-2 rounded-lg bg-gray-200 text-gray-800 text-sm font-semibold">{{ __('إعادة') }}</a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 font-semibold">
                    <tr>
                        <th class="text-right px-4 py-3">{{ __('الموظف') }}</th>
                        <th class="text-right px-4 py-3">{{ __('النوع') }}</th>
                        <th class="text-right px-4 py-3">{{ __('الفترة') }}</th>
                        <th class="text-right px-4 py-3">{{ __('الحالة') }}</th>
                        <th class="text-right px-4 py-3">{{ __('التقديم') }}</th>
                        <th class="text-right px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($leaveRequests as $req)
                    <tr class="hover:bg-gray-50/80">
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-900">{{ $req->employee?->name ?? '—' }}</div>
                            <div class="text-xs text-gray-500">{{ $req->employee?->employee_code ?? $req->employee?->email }}</div>
                        </td>
                        <td class="px-4 py-3">{{ $req->type_label }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-gray-700">{{ $req->start_date?->format('Y-m-d') }} → {{ $req->end_date?->format('Y-m-d') }} ({{ $req->days }} يوم)</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-bold
                                @if($req->status === 'pending') bg-amber-100 text-amber-800
                                @elseif($req->status === 'approved') bg-emerald-100 text-emerald-800
                                @elseif($req->status === 'rejected') bg-rose-100 text-rose-800
                                @else bg-gray-100 text-gray-700 @endif">{{ $req->status_label }}</span>
                        </td>
                        <td class="px-4 py-3 text-gray-500 whitespace-nowrap">{{ $req->created_at?->format('Y-m-d') }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('employee.hr.leaves.show', $req) }}" class="text-rose-700 hover:underline font-bold">{{ __('تفاصيل') }}</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-4 py-12 text-center text-gray-500">{{ __('لا توجد طلبات.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($leaveRequests->hasPages())
            <div class="px-4 py-3 border-t border-gray-100">{{ $leaveRequests->links() }}</div>
        @endif
    </div>
</div>
@endsection
