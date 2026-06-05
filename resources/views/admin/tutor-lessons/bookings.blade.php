@extends('layouts.admin')
@section('title', 'حجوزات الحصص')
@section('header', 'حجوزات الحصص')
@section('content')
<div class="space-y-6">
    @include('admin.tutor-lessons._nav')
    <form method="get" class="flex gap-2 items-end">
        <div>
            <label class="text-xs font-bold text-slate-600">الحالة</label>
            <select name="status" class="border rounded-lg px-3 py-2 text-sm">
                <option value="">الكل</option>
                @foreach(['pending','confirmed','in_progress','completed','cancelled'] as $s)
                    <option value="{{ $s }}" @selected(request('status') === $s)>{{ $s }}</option>
                @endforeach
            </select>
        </div>
        <button class="px-4 py-2 bg-slate-800 text-white rounded-lg text-sm font-bold">تصفية</button>
    </form>
    <div class="bg-white rounded-2xl border overflow-hidden shadow-sm">
        <table class="w-full text-sm">
            <thead class="bg-slate-50"><tr>
                <th class="p-3 text-right">الرمز</th>
                <th class="p-3 text-right">طالب</th>
                <th class="p-3 text-right">معلم</th>
                <th class="p-3">موعد</th>
                <th class="p-3">نمط</th>
                <th class="p-3">حالة</th>
            </tr></thead>
            <tbody>
            @foreach($bookings as $b)
                <tr class="border-t">
                    <td class="p-3"><a href="{{ route('admin.tutor-lessons.bookings.show', $b) }}" class="text-violet-600 font-mono">{{ $b->code }}</a></td>
                    <td class="p-3">{{ $b->student?->name }}</td>
                    <td class="p-3">{{ $b->instructor?->name }}</td>
                    <td class="p-3">{{ $b->scheduled_at?->format('Y-m-d H:i') }}</td>
                    <td class="p-3">{{ $b->matching_mode }}</td>
                    <td class="p-3">{{ $b->status }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="p-4">{{ $bookings->links() }}</div>
    </div>
</div>
@endsection
