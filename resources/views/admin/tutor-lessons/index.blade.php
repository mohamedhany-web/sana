@extends('layouts.admin')
@section('title', __('tutor.admin_hub_title'))
@section('header', __('tutor.admin_hub_title'))
@section('content')
<div class="space-y-6">
    @include('admin.tutor-lessons._nav')
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl">{{ session('success') }}</div>
    @endif
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border p-5 shadow-sm">
            <p class="text-3xl font-black text-violet-600">{{ $stats['active_tutors'] }}</p>
            <p class="text-sm text-slate-600 mt-1">معلمون مفعّلون للحجز</p>
        </div>
        <div class="bg-white rounded-2xl border p-5 shadow-sm">
            <p class="text-3xl font-black text-amber-600">{{ $stats['pending_bookings'] }}</p>
            <p class="text-sm text-slate-600 mt-1">حجوزات بانتظار التأكيد</p>
        </div>
        <div class="bg-white rounded-2xl border p-5 shadow-sm">
            <p class="text-3xl font-black text-sky-600">{{ $stats['upcoming'] }}</p>
            <p class="text-sm text-slate-600 mt-1">حصص قادمة</p>
        </div>
        <div class="bg-white rounded-2xl border p-5 shadow-sm">
            <p class="text-3xl font-black text-rose-600">{{ $stats['open_assisted'] }}</p>
            <p class="text-sm text-slate-600 mt-1">طلبات مساعدة مفتوحة</p>
        </div>
    </div>
    <div class="bg-white rounded-2xl border shadow-sm overflow-hidden">
        <div class="p-4 border-b flex justify-between items-center">
            <h2 class="font-bold text-slate-900">آخر الحجوزات</h2>
            <a href="{{ route('admin.tutor-lessons.bookings') }}" class="text-sm text-violet-600 font-bold">عرض الكل</a>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-slate-50"><tr>
                <th class="p-3 text-right">الطالب</th>
                <th class="p-3 text-right">المعلم</th>
                <th class="p-3">الموعد</th>
                <th class="p-3">الحالة</th>
            </tr></thead>
            <tbody>
            @forelse($recentBookings as $b)
                <tr class="border-t hover:bg-slate-50">
                    <td class="p-3">{{ $b->student?->name }}</td>
                    <td class="p-3">{{ $b->instructor?->name }}</td>
                    <td class="p-3">{{ $b->scheduled_at?->format('Y-m-d H:i') }}</td>
                    <td class="p-3"><a href="{{ route('admin.tutor-lessons.bookings.show', $b) }}" class="text-violet-600 font-bold">{{ $b->status }}</a></td>
                </tr>
            @empty
                <tr><td colspan="4" class="p-6 text-center text-slate-500">لا توجد حجوزات بعد</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
