@extends('layouts.admin')
@section('title', 'جلسات البث المباشر')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">
                <i class="fas fa-broadcast-tower text-red-500 ml-2"></i>جلسات البث المباشر
            </h1>
            <p class="text-sm text-slate-500 mt-1">إدارة جلسات البث والتحكم في المعلمين (المعلم = المشترك عندنا — طالب يشترون منا الخدمة)</p>
        </div>
        <a href="{{ route('admin.live-sessions.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-red-500 hover:bg-red-600 text-white rounded-xl font-semibold shadow-lg shadow-red-500/25 transition-all">
            <i class="fas fa-plus"></i> إنشاء جلسة جديدة
        </a>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl p-4 border border-slate-200">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center">
                    <i class="fas fa-list text-slate-500"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-slate-800">{{ $stats['total'] }}</p>
                    <p class="text-xs text-slate-500">إجمالي الجلسات</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-4 border border-red-200">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-red-50 flex items-center justify-center">
                    <i class="fas fa-circle text-red-500 animate-pulse text-xs"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-red-600">{{ $stats['live'] }}</p>
                    <p class="text-xs text-slate-500">مباشر الآن</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-4 border border-blue-200">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center">
                    <i class="fas fa-clock text-blue-500"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-blue-600">{{ $stats['scheduled'] }}</p>
                    <p class="text-xs text-slate-500">مجدولة</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-4 border border-emerald-200">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center">
                    <i class="fas fa-check-circle text-emerald-500"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-emerald-600">{{ $stats['ended'] }}</p>
                    <p class="text-xs text-slate-500">منتهية</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" class="bg-white rounded-xl p-4 border border-slate-200 flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[180px]">
            <label class="text-xs text-slate-500 mb-1 block">بحث</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="اسم الجلسة أو الغرفة..." class="w-full rounded-lg border-slate-300 text-sm">
        </div>
        <div>
            <label class="text-xs text-slate-500 mb-1 block">الحالة</label>
            <select name="status" class="rounded-lg border-slate-300 text-sm">
                <option value="">الكل</option>
                <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>مجدولة</option>
                <option value="live" {{ request('status') == 'live' ? 'selected' : '' }}>مباشر</option>
                <option value="ended" {{ request('status') == 'ended' ? 'selected' : '' }}>منتهية</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغاة</option>
            </select>
        </div>
        <div>
            <label class="text-xs text-slate-500 mb-1 block">الكورس</label>
            <select name="course_id" class="rounded-lg border-slate-300 text-sm">
                <option value="">الكل</option>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>{{ Str::limit($course->title, 30) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-xs text-slate-500 mb-1 block">المعلم (المشترك)</label>
            <select name="instructor_id" class="rounded-lg border-slate-300 text-sm">
                <option value="">الكل</option>
                @foreach($instructors as $inst)
                    <option value="{{ $inst->id }}" {{ request('instructor_id') == $inst->id ? 'selected' : '' }}>{{ $inst->name }}{{ $inst->role === 'student' ? ' (مشترك)' : '' }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-slate-800 text-white rounded-lg text-sm font-medium hover:bg-slate-700 transition-colors">
            <i class="fas fa-search ml-1"></i> بحث
        </button>
        @if(request()->hasAny(['search', 'status', 'course_id', 'instructor_id']))
            <a href="{{ route('admin.live-sessions.index') }}" class="px-4 py-2 bg-slate-200 text-slate-700 rounded-lg text-sm hover:bg-slate-300 transition-colors">مسح</a>
        @endif
    </form>

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-right text-slate-600 font-semibold">#</th>
                        <th class="px-4 py-3 text-right text-slate-600 font-semibold">الجلسة</th>
                        <th class="px-4 py-3 text-right text-slate-600 font-semibold">المعلم (المشترك)</th>
                        <th class="px-4 py-3 text-right text-slate-600 font-semibold">الكورس</th>
                        <th class="px-4 py-3 text-center text-slate-600 font-semibold">الحالة</th>
                        <th class="px-4 py-3 text-center text-slate-600 font-semibold">الحضور</th>
                        <th class="px-4 py-3 text-right text-slate-600 font-semibold">الموعد</th>
                        <th class="px-4 py-3 text-center text-slate-600 font-semibold">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($sessions as $session)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-4 py-3 text-slate-500">{{ $session->id }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.live-sessions.show', $session) }}" class="font-semibold text-slate-800 hover:text-blue-600 transition-colors">{{ Str::limit($session->title, 40) }}</a>
                            <p class="text-[11px] text-slate-400 mt-0.5 font-mono">{{ $session->room_name }}</p>
                        </td>
                        <td class="px-4 py-3">
                            @if($session->instructor)
                                <a href="{{ route('admin.users.show', $session->instructor->id) }}" class="font-medium text-slate-800 hover:text-blue-600 hover:underline">{{ $session->instructor->name }}</a>
                                @if($session->instructor->role === 'student')
                                    <span class="text-[10px] text-emerald-600 font-medium">(مشترك)</span>
                                @endif
                            @else
                                <span class="text-slate-500">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-slate-600">{{ Str::limit($session->course?->title ?? 'عامة', 25) }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($session->status === 'live')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-red-100 text-red-600 text-xs font-bold">
                                    <span class="w-1.5 h-1.5 bg-red-500 rounded-full animate-pulse"></span> مباشر
                                </span>
                            @elseif($session->status === 'scheduled')
                                <span class="px-2.5 py-1 rounded-full bg-blue-100 text-blue-600 text-xs font-medium">مجدولة</span>
                            @elseif($session->status === 'ended')
                                <span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-600 text-xs font-medium">منتهية</span>
                            @else
                                <span class="px-2.5 py-1 rounded-full bg-amber-100 text-amber-600 text-xs font-medium">ملغاة</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center gap-1 text-slate-600">
                                <i class="fas fa-users text-xs text-slate-400"></i> {{ $session->attendance_count }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-slate-500 text-xs">{{ $session->scheduled_at?->format('Y/m/d H:i') ?? '—' }}</td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-1">
                                <a href="{{ route('admin.live-sessions.show', $session) }}" class="p-1.5 rounded-lg hover:bg-slate-100 text-slate-500 hover:text-blue-600 transition-colors" title="عرض">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                                @if($session->status === 'scheduled')
                                    <a href="{{ route('admin.live-sessions.edit', $session) }}" class="p-1.5 rounded-lg hover:bg-slate-100 text-slate-500 hover:text-amber-600 transition-colors" title="تعديل">
                                        <i class="fas fa-edit text-xs"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.live-sessions.cancel', $session) }}" class="inline" onsubmit="return confirm('إلغاء هذه الجلسة؟')">
                                        @csrf
                                        <button type="submit" class="p-1.5 rounded-lg hover:bg-amber-100 text-slate-500 hover:text-amber-600 transition-colors" title="إلغاء الجلسة">
                                            <i class="fas fa-ban text-xs"></i>
                                        </button>
                                    </form>
                                @endif
                                @if($session->status === 'live')
                                    <form method="POST" action="{{ route('admin.live-sessions.force-end', $session) }}" class="inline" onsubmit="return confirm('هل تريد إنهاء البث؟')">
                                        @csrf
                                        <button class="p-1.5 rounded-lg hover:bg-red-100 text-slate-500 hover:text-red-600 transition-colors" title="إنهاء البث">
                                            <i class="fas fa-stop text-xs"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-12 text-center">
                            <i class="fas fa-broadcast-tower text-4xl text-slate-300 mb-3"></i>
                            <p class="text-slate-500">لا توجد جلسات بث بعد</p>
                            <a href="{{ route('admin.live-sessions.create') }}" class="inline-flex items-center gap-2 mt-3 px-4 py-2 bg-red-500 text-white rounded-lg text-sm font-medium hover:bg-red-600 transition-colors">
                                <i class="fas fa-plus"></i> إنشاء أول جلسة
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($sessions->hasPages())
        <div class="px-4 py-3 border-t border-slate-200">
            {{ $sessions->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
