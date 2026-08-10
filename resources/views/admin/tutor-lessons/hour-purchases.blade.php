@extends('layouts.admin')
@section('title', 'طلبات شراء الساعات')
@section('header', 'طلبات شراء ساعات الحصص')
@section('content')
<div class="space-y-6">
    @include('admin.tutor-lessons._nav')

    <div class="flex flex-wrap gap-2">
        @foreach(['' => 'الكل', 'pending' => 'قيد المراجعة', 'approved' => 'مقبول', 'rejected' => 'مرفوض'] as $val => $lbl)
            <a href="{{ route('admin.tutor-lessons.hour-purchases.index', $val !== '' ? ['status' => $val] : []) }}"
               class="px-3 py-1.5 rounded-lg text-xs font-bold border {{ ($status ?? '') === $val ? 'bg-violet-600 text-white border-violet-600' : 'bg-white text-slate-700 border-slate-200' }}">
                {{ $lbl }}
            </a>
        @endforeach
    </div>

    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-600 text-xs">
                <tr>
                    <th class="text-right p-3 font-bold">الطالب</th>
                    <th class="text-right p-3 font-bold">الباقة</th>
                    <th class="text-right p-3 font-bold">ساعات</th>
                    <th class="text-right p-3 font-bold">المبلغ</th>
                    <th class="text-right p-3 font-bold">الحالة</th>
                    <th class="text-right p-3 font-bold">التاريخ</th>
                    <th class="p-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($purchases as $p)
                    <tr class="border-t border-slate-100 hover:bg-slate-50/80">
                        <td class="p-3 font-semibold text-slate-800">{{ $p->user?->name }}</td>
                        <td class="p-3">{{ $p->plan_name }}</td>
                        <td class="p-3 tabular-nums">{{ $p->hours }}</td>
                        <td class="p-3 tabular-nums">{{ number_format((float) $p->price, 0) }} {{ __('public.currency') }}</td>
                        <td class="p-3">
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-bold
                                {{ $p->status === 'approved' ? 'bg-emerald-100 text-emerald-800' : ($p->status === 'rejected' ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800') }}">
                                {{ $p->statusLabel() }}
                            </span>
                        </td>
                        <td class="p-3 text-slate-500 text-xs">{{ $p->created_at?->format('Y-m-d H:i') }}</td>
                        <td class="p-3 text-left">
                            <a href="{{ route('admin.tutor-lessons.hour-purchases.show', $p) }}" class="text-violet-600 font-bold text-xs hover:underline">تفاصيل</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-8 text-center text-slate-500">لا توجد طلبات.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $purchases->withQueryString()->links() }}
</div>
@endsection
