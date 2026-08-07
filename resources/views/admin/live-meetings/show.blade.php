@extends('layouts.admin')
@section('title', $meeting->title)

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.classroom.index') }}" class="p-2 rounded-lg hover:bg-slate-100 text-slate-500"><i class="fas fa-arrow-right"></i></a>
            <div>
                <h1 class="text-2xl font-bold text-slate-800">{{ $meeting->title }}</h1>
                <p class="text-sm text-slate-400 font-mono mt-0.5">{{ $meeting->code }} · {{ $meeting->room_name }}</p>
            </div>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            @if(!$meeting->started_at && !$meeting->ended_at)
                <a href="{{ route('admin.classroom.edit', $meeting) }}" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-sm font-medium">تعديل</a>
                <form method="POST" action="{{ route('admin.classroom.start-meeting', $meeting) }}">@csrf
                    <button class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-medium">بدء الآن</button>
                </form>
            @elseif($meeting->isLive())
                <a href="{{ route('admin.classroom.room', $meeting) }}" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg text-sm font-medium">
                    <i class="fas fa-broadcast-tower ml-1"></i> دخول الغرفة
                </a>
                <form method="POST" action="{{ route('admin.classroom.end', $meeting) }}" onsubmit="return confirm('إنهاء الاجتماع للجميع؟')">@csrf
                    <button class="px-4 py-2 bg-slate-700 hover:bg-slate-800 text-white rounded-lg text-sm font-medium">إنهاء</button>
                </form>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="rounded-xl bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 text-sm">{{ session('error') }}</div>
    @endif

    @if($meeting->isLive())
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 flex items-center gap-3">
            <span class="w-3 h-3 bg-red-500 rounded-full animate-pulse"></span>
            <span class="font-semibold text-red-700">الميتينج مباشر الآن</span>
            <span class="text-sm text-red-600">— بدأ {{ $meeting->started_at?->diffForHumans() }}</span>
        </div>
    @endif

    <div class="grid lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl border border-slate-200 p-6">
                <h2 class="font-bold text-slate-800 mb-4"><i class="fas fa-info-circle text-indigo-500 ml-2"></i>تفاصيل</h2>
                <div class="grid sm:grid-cols-2 gap-4 text-sm">
                    <div><span class="text-slate-500">المضيف:</span> <span class="font-semibold text-slate-800 mr-2">{{ $meeting->user?->name ?? '—' }}</span></div>
                    <div><span class="text-slate-500">الحالة:</span>
                        <span class="font-semibold mr-2">
                            {{ $meeting->isLive() ? 'مباشر' : (!$meeting->started_at ? 'مجدول' : 'منتهي') }}
                        </span>
                    </div>
                    <div><span class="text-slate-500">الموعد:</span> <span class="font-semibold text-slate-800 mr-2">{{ $meeting->scheduled_for?->format('Y/m/d H:i') ?? '—' }}</span></div>
                    <div><span class="text-slate-500">المدة المخططة:</span> <span class="font-semibold text-slate-800 mr-2">{{ (int) ($meeting->planned_duration_minutes ?? $limits['classroom_default_duration_minutes']) }} دقيقة</span></div>
                    <div><span class="text-slate-500">الحد الأقصى:</span> <span class="font-semibold text-slate-800 mr-2">{{ (int) $meeting->max_participants }}</span></div>
                    <div><span class="text-slate-500">المشاركون المسجّلون:</span> <span class="font-semibold text-slate-800 mr-2">{{ (int) ($meeting->participants_count ?? 0) }}</span></div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-dashed border-indigo-200 p-6">
                <h2 class="font-bold text-slate-800 mb-2"><i class="fas fa-user-plus text-indigo-500 ml-2"></i>رابط دخول أي شخص</h2>
                <p class="text-sm text-slate-500 mb-3">أرسل هذا الرابط لأي ضيف — يدخل باسمه بدون حساب.</p>
                <div class="flex flex-wrap items-center gap-2">
                    <input type="text" readonly value="{{ $joinUrl }}" class="flex-1 min-w-[220px] px-3 py-2 rounded-lg border border-slate-200 bg-slate-50 text-sm font-mono">
                    <button type="button" onclick="navigator.clipboard.writeText('{{ $joinUrl }}')" class="px-4 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold">نسخ</button>
                    <a href="{{ $joinUrl }}" target="_blank" class="px-4 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold">فتح</a>
                </div>
            </div>
        </div>

        <div class="space-y-4">
            <div class="bg-white rounded-xl border border-slate-200 p-5 space-y-3">
                <h3 class="font-bold text-slate-800 text-sm">اختصارات</h3>
                <a href="{{ route('admin.classroom.whiteboard') }}" target="_blank" class="block w-full text-center px-4 py-2 rounded-lg bg-amber-50 text-amber-800 border border-amber-200 text-sm font-semibold">لوحة بيضاء منفصلة</a>
                <a href="{{ route('admin.live-sessions.index') }}" class="block w-full text-center px-4 py-2 rounded-lg bg-slate-50 text-slate-700 border border-slate-200 text-sm font-semibold">جلسات بث المعلمين</a>
            </div>
            @if(!$meeting->ended_at)
            <form method="POST" action="{{ route('admin.classroom.destroy', $meeting) }}" onsubmit="return confirm('حذف هذا الميتينج؟');">
                @csrf
                @method('DELETE')
                <button class="w-full px-4 py-2 rounded-lg bg-rose-50 text-rose-700 border border-rose-200 text-sm font-semibold hover:bg-rose-100">حذف الميتينج</button>
            </form>
            @endif
        </div>
    </div>
</div>
@endsection
