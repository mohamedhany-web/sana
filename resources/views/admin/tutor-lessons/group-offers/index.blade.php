@extends('layouts.admin')

@section('title', 'عروض حجز المجموعات')

@section('content')
<div class="max-w-6xl mx-auto">
    @include('admin.tutor-lessons._nav')

    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-black text-slate-900">عروض حجز المجموعات</h1>
            <p class="text-sm text-slate-600 mt-1">اربط كل عرض بمعلّم، حجم المجموعة، باقة دورات، واشتراكات الطلاب المسموح لها.</p>
        </div>
        <a href="{{ route('admin.tutor-lessons.group-offers.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-violet-600 text-white text-sm font-bold hover:bg-violet-700">
            <i class="fas fa-plus"></i> عرض جديد
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 rounded-xl bg-emerald-50 text-emerald-800 text-sm font-semibold">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="text-right px-4 py-3 font-bold">العرض</th>
                        <th class="text-right px-4 py-3 font-bold">المعلّم</th>
                        <th class="text-right px-4 py-3 font-bold">الحجم</th>
                        <th class="text-right px-4 py-3 font-bold">الباقات</th>
                        <th class="text-right px-4 py-3 font-bold">الحالة</th>
                        <th class="text-right px-4 py-3 font-bold"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($offers as $offer)
                    <tr>
                        <td class="px-4 py-3">
                            <strong class="text-slate-900">{{ $offer->title }}</strong>
                            @if($offer->package)
                                <p class="text-xs text-slate-500 mt-0.5">باقة: {{ $offer->package->name }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-3">{{ $offer->instructor?->name ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $offer->min_group_size }}–{{ $offer->max_group_size }} طلاب</td>
                        <td class="px-4 py-3 text-xs text-slate-600">{{ $offer->planKeysLabel() }}</td>
                        <td class="px-4 py-3">
                            @if($offer->is_active)
                                <span class="px-2 py-1 rounded-full bg-emerald-100 text-emerald-700 text-xs font-bold">نشط</span>
                            @else
                                <span class="px-2 py-1 rounded-full bg-slate-100 text-slate-600 text-xs font-bold">معطّل</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-left whitespace-nowrap">
                            <a href="{{ route('admin.tutor-lessons.group-offers.edit', $offer) }}" class="text-violet-600 font-bold text-xs ml-3">تعديل</a>
                            <form method="post" action="{{ route('admin.tutor-lessons.group-offers.destroy', $offer) }}" class="inline" onsubmit="return confirm('حذف هذا العرض؟')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-rose-600 font-bold text-xs">حذف</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center text-slate-500">لا توجد عروض مجموعات بعد.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($offers->hasPages())
            <div class="px-4 py-3 border-t border-slate-100">{{ $offers->links() }}</div>
        @endif
    </div>
</div>
@endsection
