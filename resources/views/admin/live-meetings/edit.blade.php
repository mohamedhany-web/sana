@extends('layouts.admin')
@section('title', 'تعديل ميتينج')

@section('content')
<div class="space-y-6 max-w-2xl">
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.classroom.show', $meeting) }}" class="p-2 rounded-lg hover:bg-slate-100 text-slate-500"><i class="fas fa-arrow-right"></i></a>
        <h1 class="text-2xl font-bold text-slate-800">تعديل الميتينج</h1>
    </div>

    <form method="POST" action="{{ route('admin.classroom.update', $meeting) }}" class="bg-white rounded-xl border border-slate-200 p-6 space-y-5">
        @csrf
        @method('PUT')
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">عنوان الاجتماع</label>
            <input type="text" name="title" value="{{ old('title', $meeting->title) }}" required class="w-full rounded-lg border-slate-300">
            @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">الحد الأقصى للمشاركين</label>
            <input type="number" name="max_participants" min="2" max="{{ (int) $limits['classroom_max_participants'] }}"
                   value="{{ old('max_participants', $meeting->max_participants) }}" class="w-full rounded-lg border-slate-300">
            @error('max_participants')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">موعد مجدول</label>
            <input type="datetime-local" name="scheduled_for"
                   value="{{ old('scheduled_for', optional($meeting->scheduled_for)->format('Y-m-d\TH:i')) }}"
                   class="w-full rounded-lg border-slate-300">
            @error('scheduled_for')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">مدة الاجتماع (دقيقة)</label>
            <input type="number" name="planned_duration_minutes" min="15" max="{{ (int) $limits['classroom_max_duration_minutes'] }}"
                   value="{{ old('planned_duration_minutes', $meeting->planned_duration_minutes ?? $limits['classroom_default_duration_minutes']) }}"
                   class="w-full rounded-lg border-slate-300">
            @error('planned_duration_minutes')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="flex items-center gap-3">
            <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-semibold">حفظ</button>
            <a href="{{ route('admin.classroom.show', $meeting) }}" class="px-6 py-2.5 bg-slate-200 text-slate-700 rounded-xl">إلغاء</a>
        </div>
    </form>
</div>
@endsection
