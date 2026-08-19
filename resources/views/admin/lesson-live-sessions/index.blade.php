@extends('layouts.admin')
@section('title', 'جلسات البث المباشر للحصص')
@section('header', 'جلسات البث المباشر للحصص')

@section('content')
<div class="space-y-6">
    @include('admin.tutor-lessons._nav')

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">
                <i class="fas fa-user-secret text-violet-500 ml-2"></i>جلسات البث المباشر للحصص
            </h1>
            <p class="text-sm text-slate-500 mt-1">حصص المعلم والطالب. الدخول للجلسة المباشرة يكون رقابة مخفية: لا تظهر في الغرفة ولا في قائمة المشاركين ولا في الفوترة.</p>
        </div>
    </div>

    @if (session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-3 text-sm font-bold">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="rounded-xl border border-rose-200 bg-rose-50 text-rose-800 px-4 py-3 text-sm font-bold">{{ session('error') }}</div>
    @endif

    <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
        <i class="fas fa-eye-slash ml-1"></i>
        الرقابة من هنا لا تبث صوتاً ولا صورة، ولا تسجّل حضورك داخل الحصة. المعلم والطالب لا يريان أنك دخلت.
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl p-4 border border-slate-200">
            <p class="text-2xl font-bold text-slate-800">{{ $stats['total'] }}</p>
            <p class="text-xs text-slate-500 mt-1">إجمالي جلسات الحصص</p>
        </div>
        <div class="bg-white rounded-xl p-4 border border-red-200">
            <p class="text-2xl font-bold text-red-600">{{ $stats['live'] }}</p>
            <p class="text-xs text-slate-500 mt-1">مباشر الآن</p>
        </div>
        <div class="bg-white rounded-xl p-4 border border-blue-200">
            <p class="text-2xl font-bold text-blue-600">{{ $stats['scheduled'] }}</p>
            <p class="text-xs text-slate-500 mt-1">لم تبدأ بعد</p>
        </div>
        <div class="bg-white rounded-xl p-4 border border-slate-200">
            <p class="text-2xl font-bold text-slate-600">{{ $stats['ended'] }}</p>
            <p class="text-xs text-slate-500 mt-1">منتهية</p>
        </div>
    </div>

    <form method="GET" class="bg-white rounded-xl p-4 border border-slate-200 flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[180px]">
            <label class="text-xs text-slate-500 mb-1 block">بحث</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="معلم، طالب، كود الحصة..." class="w-full rounded-lg border-slate-300 text-sm">
        </div>
        <div>
            <label class="text-xs text-slate-500 mb-1 block">الحالة</label>
            <select name="status" class="rounded-lg border-slate-300 text-sm">
                <option value="">الكل</option>
                <option value="live" @selected($status === 'live')>مباشر</option>
                <option value="scheduled" @selected($status === 'scheduled')>لم تبدأ</option>
                <option value="ended" @selected($status === 'ended')>منتهية</option>
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-slate-800 text-white rounded-lg text-sm font-medium hover:bg-slate-700 transition-colors">
            <i class="fas fa-search ml-1"></i> بحث
        </button>
        @if(request()->hasAny(['search', 'status']))
            <a href="{{ route('admin.lesson-live-sessions.index') }}" class="px-4 py-2 bg-slate-200 text-slate-700 rounded-lg text-sm hover:bg-slate-300 transition-colors">مسح</a>
        @endif
    </form>

    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-right text-slate-600 font-semibold">الحصة</th>
                        <th class="px-4 py-3 text-right text-slate-600 font-semibold">المعلم</th>
                        <th class="px-4 py-3 text-right text-slate-600 font-semibold">الطالب</th>
                        <th class="px-4 py-3 text-center text-slate-600 font-semibold">الحالة</th>
                        <th class="px-4 py-3 text-center text-slate-600 font-semibold">المتواجدون</th>
                        <th class="px-4 py-3 text-right text-slate-600 font-semibold">البدء</th>
                        <th class="px-4 py-3 text-center text-slate-600 font-semibold">إجراء</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($meetings as $meeting)
                        @php
                            $bookings = $meeting->lesson_bookings_display ?? collect();
                            $instructorName = $bookings->first()?->instructor?->name ?? $meeting->user?->name;
                            $studentNames = $bookings->pluck('student.name')->filter()->unique()->values();
                            $subjectName = $bookings->first()?->subject?->name;
                            $isLive = $meeting->isLive() && ! data_get($meeting->settings, 'host_ended');
                            $booking = $bookings->first();
                        @endphp
                        <tr class="hover:bg-slate-50 transition-colors {{ $isLive ? 'bg-red-50/40' : '' }}">
                            <td class="px-4 py-3">
                                <p class="font-semibold text-slate-800">{{ $meeting->title ?: 'حصة مباشرة' }}</p>
                                <p class="text-[11px] text-slate-400 mt-0.5 font-mono">{{ $meeting->code }}</p>
                                @if($subjectName)
                                    <p class="text-[11px] text-violet-600 mt-0.5">{{ $subjectName }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-3 font-medium text-slate-800">{{ $instructorName ?: '—' }}</td>
                            <td class="px-4 py-3 text-slate-700">
                                {{ $studentNames->isNotEmpty() ? $studentNames->implode('، ') : '—' }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($isLive)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-red-100 text-red-600 text-xs font-bold">
                                        <span class="w-1.5 h-1.5 bg-red-500 rounded-full animate-pulse"></span> مباشر
                                    </span>
                                @elseif($meeting->ended_at)
                                    <span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-600 text-xs font-medium">منتهية</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full bg-blue-100 text-blue-600 text-xs font-medium">لم تبدأ</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center text-slate-600">
                                <i class="fas fa-users text-xs text-slate-400 ml-1"></i>{{ (int) ($meeting->online_participants_count ?? 0) }}
                            </td>
                            <td class="px-4 py-3 text-slate-500 text-xs">{{ $meeting->started_at?->format('Y/m/d H:i') ?? '—' }}</td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    @if($isLive)
                                        <a href="{{ route('admin.lesson-live-sessions.observe', $meeting) }}"
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-violet-600 hover:bg-violet-700 text-white text-xs font-bold">
                                            <i class="fas fa-eye-slash"></i> دخول رقابة
                                        </a>
                                    @endif
                                    @if($booking && Route::has('admin.tutor-lessons.bookings.show'))
                                        <a href="{{ route('admin.tutor-lessons.bookings.show', $booking) }}"
                                           class="text-slate-500 hover:text-violet-600 text-xs font-semibold">الحجز</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center text-slate-500">
                                لا توجد جلسات حصص بعد.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($meetings->hasPages())
            <div class="px-4 py-3 border-t border-slate-200">
                {{ $meetings->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
