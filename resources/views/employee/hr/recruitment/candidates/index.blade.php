@extends('layouts.employee')

@section('title', __('المرشحون'))
@section('header', __('مرشحو التوظيف'))

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap justify-between gap-3">
        <a href="{{ route('employee.hr.recruitment.index') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900"><i class="fas fa-arrow-right ml-1"></i> التوظيف</a>
        <a href="{{ route('employee.hr.recruitment.candidates.create') }}" class="px-4 py-2 rounded-lg bg-slate-800 text-white text-sm font-bold">مرشح جديد</a>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <form method="GET" class="flex flex-wrap gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="بحث بالاسم أو البريد…" class="rounded-lg border border-gray-300 px-3 py-2 text-sm flex-1 min-w-[200px]">
            <button type="submit" class="px-4 py-2 rounded-lg bg-slate-700 text-white text-sm font-bold">بحث</button>
        </form>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 font-semibold text-gray-600">
                <tr>
                    <th class="text-right px-4 py-3">الاسم</th>
                    <th class="text-right px-4 py-3">البريد</th>
                    <th class="text-right px-4 py-3">المصدر</th>
                    <th class="text-right px-4 py-3">طلبات</th>
                    <th class="text-right px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($candidates as $c)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium">{{ $c->full_name }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $c->email }}</td>
                    <td class="px-4 py-3">{{ $c->source_label }}</td>
                    <td class="px-4 py-3 tabular-nums">{{ $c->applications_count }}</td>
                    <td class="px-4 py-3"><a href="{{ route('employee.hr.recruitment.candidates.show', $c) }}" class="text-violet-700 font-bold">عرض</a></td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-4 py-12 text-center text-gray-500">لا مرشحين.</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($candidates->hasPages())<div class="px-4 py-3 border-t">{{ $candidates->links() }}</div>@endif
    </div>
</div>
@endsection
