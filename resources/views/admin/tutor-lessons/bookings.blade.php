@extends('layouts.admin')
@section('title', 'حجوزات الحصص')
@section('header', 'حجوزات الحصص')
@section('content')
<div class="space-y-6">
    @include('admin.tutor-lessons._nav')

    @if (session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-3 text-sm font-bold">{{ session('success') }}</div>
    @endif

    <div class="flex flex-wrap items-center justify-between gap-3">
        <form method="get" class="flex flex-wrap gap-2 items-end">
            <div>
                <label class="text-xs font-bold text-slate-600">الحالة</label>
                <select name="status" class="border rounded-lg px-3 py-2 text-sm">
                    <option value="">الكل</option>
                    @foreach(['pending','confirmed','in_progress','completed','cancelled'] as $s)
                        <option value="{{ $s }}" @selected(request('status') === $s)>{{ $s }}</option>
                    @endforeach
                </select>
            </div>
            <label class="inline-flex items-center gap-2 text-sm pb-2">
                <input type="checkbox" name="admin_only" value="1" @checked(request()->boolean('admin_only')) class="rounded text-violet-600">
                حجوزات الإدارة فقط
            </label>
            @if(request('group'))
                <input type="hidden" name="group" value="{{ request('group') }}">
            @endif
            <button class="px-4 py-2 bg-slate-800 text-white rounded-lg text-sm font-bold">تصفية</button>
        </form>
        <a href="{{ route('admin.tutor-lessons.book.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-violet-600 text-white text-sm font-bold hover:bg-violet-700">
            <i class="fas fa-calendar-plus"></i> حجز جديد
        </a>
    </div>

    @if(request('group'))
        <div class="rounded-xl border border-violet-200 bg-violet-50 px-4 py-3 text-sm text-violet-900">
            عرض مجموعة مرتبطة:
            <code class="font-mono text-xs">{{ request('group') }}</code>
            <a href="{{ route('admin.tutor-lessons.bookings') }}" class="underline mr-2">إلغاء التصفية</a>
        </div>
    @endif

    <div class="bg-white rounded-2xl border overflow-hidden shadow-sm">
        <table class="w-full text-sm">
            <thead class="bg-slate-50"><tr>
                <th class="p-3 text-right">الرمز</th>
                <th class="p-3 text-right">طالب</th>
                <th class="p-3 text-right">معلم</th>
                <th class="p-3">موعد</th>
                <th class="p-3">النوع</th>
                <th class="p-3">حالة</th>
            </tr></thead>
            <tbody>
            @forelse($bookings as $b)
                <tr class="border-t">
                    <td class="p-3">
                        <a href="{{ route('admin.tutor-lessons.bookings.show', $b) }}" class="text-violet-600 font-mono">{{ $b->code }}</a>
                        @if($b->group_session_key)
                            <a href="{{ route('admin.tutor-lessons.bookings', ['group' => $b->group_session_key]) }}"
                               class="block text-[10px] text-slate-400 mt-0.5 hover:text-violet-600">مجموعة</a>
                        @endif
                    </td>
                    <td class="p-3">{{ $b->student?->name }}</td>
                    <td class="p-3">{{ $b->instructor?->name }}</td>
                    <td class="p-3">{{ $b->scheduled_at?->format('Y-m-d H:i') }}</td>
                    <td class="p-3">{{ $b->session_type === 'small_group' ? 'مجموعة' : 'فردي' }}</td>
                    <td class="p-3">{{ $b->statusLabel() }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="p-8 text-center text-slate-400">لا توجد حجوزات.</td></tr>
            @endforelse
            </tbody>
        </table>
        <div class="p-4">{{ $bookings->links() }}</div>
    </div>
</div>
@endsection
