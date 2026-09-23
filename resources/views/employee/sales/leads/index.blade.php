@extends('layouts.employee')

@section('title', __('العملاء المحتملون'))
@section('header', __('العملاء المحتملون (Leads)'))

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <a href="{{ route('employee.sales.desk') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-gray-600 hover:text-gray-900">
            <i class="fas fa-arrow-right"></i> لوحة المبيعات
        </a>
        <a href="{{ route('employee.sales.leads.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-sm font-bold shadow-sm">
            <i class="fas fa-plus"></i> إضافة Lead
        </a>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-gray-600 mb-1">بحث</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="اسم، بريد، هاتف، شركة، رقم…"
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">الحالة</label>
                <select name="status" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500">
                    <option value="">الكل</option>
                    @foreach(\App\Models\SalesLead::statusLabels() as $val => $label)
                        <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">المصدر</label>
                <select name="source" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500">
                    <option value="">الكل</option>
                    @foreach(\App\Models\SalesLead::sourceLabels() as $val => $label)
                        <option value="{{ $val }}" {{ request('source') === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="lg:col-span-4 flex flex-wrap items-center gap-4">
                <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" name="mine" value="1" {{ request('mine') ? 'checked' : '' }} class="rounded border-gray-300 text-teal-600 focus:ring-teal-500">
                    المعيّنة لي فقط
                </label>
                <button type="submit" class="px-4 py-2 rounded-lg bg-teal-600 hover:bg-teal-700 text-white text-sm font-bold">تطبيق</button>
                <a href="{{ route('employee.sales.leads.index') }}" class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm font-semibold">إعادة ضبط</a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 font-semibold">
                    <tr>
                        <th class="text-right px-4 py-3">#</th>
                        <th class="text-right px-4 py-3">الاسم</th>
                        <th class="text-right px-4 py-3">تواصل</th>
                        <th class="text-right px-4 py-3">المصدر</th>
                        <th class="text-right px-4 py-3">الحالة</th>
                        <th class="text-right px-4 py-3">المسؤول</th>
                        <th class="text-right px-4 py-3">التاريخ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($leads as $lead)
                    <tr class="hover:bg-gray-50/80">
                        <td class="px-4 py-3">
                            <a href="{{ route('employee.sales.leads.show', $lead) }}" class="font-bold text-teal-700 hover:underline">#{{ $lead->id }}</a>
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-900">{{ $lead->name }}</div>
                            @if($lead->company)<div class="text-xs text-gray-500">{{ $lead->company }}</div>@endif
                        </td>
                        <td class="px-4 py-3 text-gray-700 text-xs">
                            <div>{{ $lead->email ?: '—' }}</div>
                            <div>{{ $lead->phone ?: '' }}</div>
                        </td>
                        <td class="px-4 py-3 text-gray-700">{{ $lead->source_label }}</td>
                        <td class="px-4 py-3">
                            @if($lead->status === \App\Models\SalesLead::STATUS_CONVERTED)
                                <span class="rounded-full bg-emerald-100 text-emerald-800 px-2 py-0.5 text-xs font-bold">{{ $lead->status_label }}</span>
                            @elseif($lead->status === \App\Models\SalesLead::STATUS_LOST)
                                <span class="rounded-full bg-rose-100 text-rose-800 px-2 py-0.5 text-xs font-bold">{{ $lead->status_label }}</span>
                            @elseif($lead->status === \App\Models\SalesLead::STATUS_QUALIFIED)
                                <span class="rounded-full bg-indigo-100 text-indigo-800 px-2 py-0.5 text-xs font-bold">{{ $lead->status_label }}</span>
                            @else
                                <span class="rounded-full bg-amber-100 text-amber-800 px-2 py-0.5 text-xs font-bold">{{ $lead->status_label }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-700">{{ $lead->assignedTo?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-500 text-xs whitespace-nowrap">{{ $lead->created_at?->format('Y-m-d') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center text-gray-500">لا توجد نتائج. أضف عميلاً محتملاً من الزر أعلاه.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($leads->hasPages())
            <div class="px-4 py-3 border-t border-gray-100">{{ $leads->links() }}</div>
        @endif
    </div>
</div>
@endsection
