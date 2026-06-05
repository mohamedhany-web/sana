@extends('layouts.admin')

@section('title', 'العملاء المحتملون')
@section('header', 'العملاء المحتملون (Leads)')

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm text-slate-600">عرض وإضافة العملاء المحتملين. التعديل والتحويل من لوحة الموظف.</p>
        <a href="{{ route('admin.sales.leads.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold shadow-sm">
            <i class="fas fa-plus"></i>
            إضافة Lead
        </a>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4 sm:p-6">
        <form method="GET" class="flex flex-wrap items-end gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">بحث</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="اسم، بريد، هاتف، رقم…"
                       class="rounded-lg border border-slate-300 px-3 py-2 text-sm w-64 max-w-full">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">الحالة</label>
                <select name="status" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    <option value="">الكل</option>
                    @foreach(\App\Models\SalesLead::statusLabels() as $val => $label)
                        <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold">تصفية</button>
            <a href="{{ route('admin.sales.leads.index') }}" class="px-4 py-2 rounded-lg bg-slate-200 text-slate-800 text-sm font-semibold">إعادة ضبط</a>
        </form>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-600 font-semibold">
                    <tr>
                        <th class="text-right px-4 py-3">#</th>
                        <th class="text-right px-4 py-3">الاسم</th>
                        <th class="text-right px-4 py-3">تواصل</th>
                        <th class="text-right px-4 py-3">الحالة</th>
                        <th class="text-right px-4 py-3">المسؤول</th>
                        <th class="text-right px-4 py-3">التاريخ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($leads as $lead)
                    <tr class="hover:bg-slate-50/80">
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.sales.leads.show', $lead) }}" class="font-bold text-emerald-700 hover:underline">#{{ $lead->id }}</a>
                        </td>
                        <td class="px-4 py-3 font-medium text-slate-900">{{ $lead->name }}</td>
                        <td class="px-4 py-3 text-xs text-slate-600">
                            <div>{{ $lead->email ?: '—' }}</div>
                            <div>{{ $lead->phone }}</div>
                        </td>
                        <td class="px-4 py-3">{{ $lead->status_label }}</td>
                        <td class="px-4 py-3 text-slate-700">{{ $lead->assignedTo?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-xs text-slate-500">{{ $lead->created_at?->format('Y-m-d') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-4 py-12 text-center text-slate-500">لا توجد سجلات.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($leads->hasPages())
            <div class="px-4 py-3 border-t border-slate-100">{{ $leads->links() }}</div>
        @endif
    </div>
</div>
@endsection
