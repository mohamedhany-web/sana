@extends('layouts.admin')
@section('title', 'إنشاء ميتينج إدارة')

@section('content')
<div class="space-y-6 max-w-2xl">
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.classroom.index') }}" class="p-2 rounded-lg hover:bg-slate-100 text-slate-500"><i class="fas fa-arrow-right"></i></a>
        <div>
            <h1 class="text-2xl font-bold text-slate-800">إنشاء ميتينج إدارة</h1>
            <p class="text-sm text-slate-500 mt-1">بعد الإنشاء تحصلين على رابط يدخل منه أي شخص كضيف.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.classroom.store') }}" class="bg-white rounded-xl border border-slate-200 p-6 space-y-5">
        @csrf
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">عنوان الاجتماع <span class="text-red-500">*</span></label>
            <input type="text" name="title" value="{{ old('title', 'ميتينج إدارة — '.now()->format('Y/m/d H:i')) }}" required
                   class="w-full rounded-lg border-slate-300" placeholder="مثال: اجتماع متابعة مع ولي أمر">
            @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">الحد الأقصى للمشاركين</label>
            <input type="number" name="max_participants" min="2" max="{{ (int) $limits['classroom_max_participants'] }}"
                   value="{{ old('max_participants', 50) }}" class="w-full rounded-lg border-slate-300">
            <p class="text-xs text-slate-500 mt-1">الحد الأعلى للإدارة: {{ (int) $limits['classroom_max_participants'] }}</p>
            @error('max_participants')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">وضع البداية</label>
            <select name="start_now" class="w-full rounded-lg border-slate-300">
                <option value="1" {{ old('start_now', '1') === '1' ? 'selected' : '' }}>ابدأ الآن وادخل الغرفة</option>
                <option value="0" {{ old('start_now') === '0' ? 'selected' : '' }}>إنشاء فقط (مجدول)</option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">موعد مجدول (اختياري)</label>
            <input type="datetime-local" name="scheduled_for" value="{{ old('scheduled_for') }}" class="w-full rounded-lg border-slate-300">
            @error('scheduled_for')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">مدة الاجتماع (دقيقة)</label>
            <input type="number" name="planned_duration_minutes" min="15" max="{{ (int) $limits['classroom_max_duration_minutes'] }}"
                   value="{{ old('planned_duration_minutes', (int) $limits['classroom_default_duration_minutes']) }}"
                   class="w-full rounded-lg border-slate-300">
            <p class="text-xs text-slate-500 mt-1">الحد الأقصى: {{ (int) $limits['classroom_max_duration_minutes'] }} دقيقة</p>
            @error('planned_duration_minutes')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="rounded-xl bg-indigo-50 border border-indigo-100 p-4 text-sm text-indigo-800">
            <i class="fas fa-link ml-1"></i>
            بعد الإنشاء يظهر رابط بصيغة <code class="text-xs bg-white/70 px-1 rounded">{{ url('classroom/join/XXXX') }}</code> — أرسليه لأي شخص.
        </div>

        <div class="flex items-center gap-3 pt-2">
            <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-semibold">
                <i class="fas fa-video ml-1"></i> إنشاء الميتينج
            </button>
            <a href="{{ route('admin.classroom.index') }}" class="px-6 py-2.5 bg-slate-200 text-slate-700 rounded-xl font-medium hover:bg-slate-300">إلغاء</a>
        </div>
    </form>
</div>
@endsection
