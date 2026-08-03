@extends('layouts.admin')

@section('title', 'رقابة المعلمين')
@section('header', 'رقابة المعلمين')

@section('content')
@php
    $statusLabels = [
        \App\Models\InstructorProfile::STATUS_DRAFT => 'مسودة',
        \App\Models\InstructorProfile::STATUS_PENDING_REVIEW => 'بانتظار',
        \App\Models\InstructorProfile::STATUS_APPROVED => 'مقبول',
        \App\Models\InstructorProfile::STATUS_REJECTED => 'مرفوض',
    ];
@endphp

<div class="space-y-6">
    <div class="bg-white rounded-3xl shadow-lg p-6 border border-slate-200 overflow-hidden">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">رقابة المعلمين — شاملة</h1>
                <p class="text-slate-500 text-sm mt-1">بيانات الحساب الحالية، ملف الانضمام، الحصص، الكورسات، وكل ما تم عمله.</p>
            </div>
            <a href="{{ route('admin.quality-control.index') }}" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                لوحة الرقابة
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
            <div class="rounded-2xl border border-sky-200 bg-sky-50/50 p-4">
                <p class="text-xs font-semibold text-sky-700">الإجمالي</p>
                <p class="text-2xl font-black text-slate-900">{{ $stats['total'] }}</p>
            </div>
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50/50 p-4">
                <p class="text-xs font-semibold text-emerald-700">مفعّل</p>
                <p class="text-2xl font-black text-slate-900">{{ $stats['active'] }}</p>
            </div>
            <div class="rounded-2xl border border-violet-200 bg-violet-50/50 p-4">
                <p class="text-xs font-semibold text-violet-700">حجز حصص مفعّل</p>
                <p class="text-2xl font-black text-slate-900">{{ $stats['tutor_booking'] }}</p>
            </div>
            <div class="rounded-2xl border border-amber-200 bg-amber-50/50 p-4">
                <p class="text-xs font-semibold text-amber-800">جدد هذا الشهر</p>
                <p class="text-2xl font-black text-slate-900">{{ $stats['new_month'] }}</p>
            </div>
        </div>

        <form method="GET" class="mb-6 flex flex-wrap items-center gap-3">
            <input type="text" name="search" value="{{ $search ?? request('search') }}" placeholder="بحث بالاسم، البريد، الهاتف..." class="rounded-2xl border border-slate-200 px-4 py-2.5 text-sm w-64">
            <select name="status" class="rounded-2xl border border-slate-200 px-4 py-2.5 text-sm">
                <option value="">كل الحالات</option>
                <option value="active" @selected(request('status') === 'active')>مفعّل</option>
                <option value="inactive" @selected(request('status') === 'inactive')>غير مفعّل</option>
            </select>
            <button type="submit" class="rounded-2xl bg-sky-600 hover:bg-sky-700 text-white px-4 py-2.5 text-sm font-semibold">بحث</button>
            <a href="{{ route('admin.quality-control.instructors') }}" class="px-4 py-2.5 text-sm font-semibold text-slate-600">مسح</a>
        </form>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-right">
                <thead class="bg-slate-50">
                    <tr class="text-xs font-semibold uppercase text-slate-500">
                        <th class="px-6 py-3">المعلم</th>
                        <th class="px-6 py-3">الحساب / الطلب</th>
                        <th class="px-6 py-3">الكورسات</th>
                        <th class="px-6 py-3">الحصص</th>
                        <th class="px-6 py-3">آخر نشاط</th>
                        <th class="px-6 py-3">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 bg-white">
                    @forelse($instructors as $instructor)
                    @php $profile = $instructor->instructorProfile; @endphp
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.quality-control.instructors.show', $instructor) }}" class="font-semibold text-sky-600 hover:text-sky-700">{{ $instructor->name }}</a>
                            <div class="text-xs text-slate-500 mt-0.5" dir="ltr">{{ $instructor->email ?? '—' }}</div>
                            <div class="text-xs text-slate-400" dir="ltr">{{ $instructor->phone ?? '' }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-bold {{ $instructor->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                {{ $instructor->is_active ? 'مفعّل' : 'غير مفعّل' }}
                            </span>
                            @if($profile)
                                <div class="mt-1 text-xs text-slate-600">{{ $statusLabels[$profile->status] ?? $profile->status }}</div>
                                @if($profile->isTutorActivated())
                                    <div class="mt-1 text-[10px] font-bold text-violet-700">حجز حصص مفعّل</div>
                                @endif
                            @endif
                        </td>
                        <td class="px-6 py-4">{{ $instructor->courses_count }}</td>
                        <td class="px-6 py-4">{{ $instructor->bookings_count }}</td>
                        <td class="px-6 py-4 text-sm">{{ $instructor->last_activity ? $instructor->last_activity->diffForHumans() : '—' }}</td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap items-center gap-2">
                                <a href="{{ route('admin.quality-control.instructors.show', $instructor) }}" class="inline-flex items-center gap-1 rounded-xl bg-sky-100 text-sky-700 px-3 py-1.5 text-xs font-semibold hover:bg-sky-200">رقابة شاملة</a>
                                <a href="{{ route('admin.quality-control.instructors.export', $instructor) }}" class="inline-flex items-center gap-1 rounded-xl bg-emerald-100 text-emerald-700 px-3 py-1.5 text-xs font-semibold hover:bg-emerald-200"><i class="fas fa-file-excel"></i> Excel</a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-500">لا يوجد معلمون مطابقون للبحث.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4 px-2">{{ $instructors->links() }}</div>
    </div>
</div>
@endsection
