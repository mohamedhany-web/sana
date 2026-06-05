@extends('layouts.admin')
@section('title', 'تسجيلات Classroom')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800"><i class="fas fa-chalkboard text-indigo-500 ml-2"></i>تسجيلات Classroom</h1>
            <p class="text-sm text-slate-500 mt-1">عرض كل اجتماعات Classroom مع روابط تحميل التسجيلات من Cloudflare R2.</p>
        </div>
    </div>

    <form method="GET" class="bg-white rounded-xl p-4 border border-slate-200 flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[220px]">
            <label class="text-xs text-slate-500 mb-1 block">بحث</label>
            <input type="text" name="search" value="{{ $search }}" placeholder="عنوان، كود، اسم المعلم، أو البريد..." class="w-full rounded-lg border-slate-300 text-sm">
        </div>
        <div>
            <label class="text-xs text-slate-500 mb-1 block">حالة الاجتماع</label>
            <select name="status" class="rounded-lg border-slate-300 text-sm">
                <option value="all" {{ $status === 'all' ? 'selected' : '' }}>الكل</option>
                <option value="live" {{ $status === 'live' ? 'selected' : '' }}>مباشر</option>
                <option value="scheduled" {{ $status === 'scheduled' ? 'selected' : '' }}>مجدول</option>
                <option value="ended" {{ $status === 'ended' ? 'selected' : '' }}>منتهي</option>
            </select>
        </div>
        <div>
            <label class="text-xs text-slate-500 mb-1 block">التسجيل</label>
            <select name="has_recording" class="rounded-lg border-slate-300 text-sm">
                <option value="all" {{ $hasRecording === 'all' ? 'selected' : '' }}>الكل</option>
                <option value="yes" {{ $hasRecording === 'yes' ? 'selected' : '' }}>يوجد تسجيل</option>
                <option value="no" {{ $hasRecording === 'no' ? 'selected' : '' }}>بدون تسجيل</option>
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-slate-800 text-white rounded-lg text-sm font-medium hover:bg-slate-700 transition-colors">
            <i class="fas fa-search ml-1"></i> بحث
        </button>
    </form>

    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-3 text-right text-slate-600 font-semibold">#</th>
                    <th class="px-4 py-3 text-right text-slate-600 font-semibold">الاجتماع</th>
                    <th class="px-4 py-3 text-right text-slate-600 font-semibold">المعلم</th>
                    <th class="px-4 py-3 text-center text-slate-600 font-semibold">الحالة</th>
                    <th class="px-4 py-3 text-center text-slate-600 font-semibold">وقت الرفع</th>
                    <th class="px-4 py-3 text-center text-slate-600 font-semibold">إجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($meetings as $meeting)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-4 py-3 text-slate-500">{{ $meeting->id }}</td>
                        <td class="px-4 py-3">
                            <p class="font-medium text-slate-800">{{ $meeting->title ?: ('اجتماع #' . $meeting->id) }}</p>
                            <p class="text-xs text-slate-500 mt-1">
                                كود: <span class="font-mono">{{ $meeting->code }}</span>
                                @if($meeting->ended_at)
                                    · انتهى: {{ $meeting->ended_at->format('Y-m-d H:i') }}
                                @elseif($meeting->scheduled_for)
                                    · مجدول: {{ $meeting->scheduled_for->format('Y-m-d H:i') }}
                                @endif
                            </p>
                        </td>
                        <td class="px-4 py-3 text-slate-600">
                            <div>{{ $meeting->user->name ?? '—' }}</div>
                            <div class="text-xs text-slate-500">{{ $meeting->user->email ?? '—' }}</div>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($meeting->isLive())
                                <span class="px-2 py-0.5 rounded-full bg-rose-100 text-rose-700 text-xs">مباشر</span>
                            @elseif(!$meeting->started_at)
                                <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 text-xs">مجدول</span>
                            @else
                                <span class="px-2 py-0.5 rounded-full bg-slate-200 text-slate-700 text-xs">منتهي</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center text-slate-500">
                            {{ $meeting->recording_uploaded_at ? $meeting->recording_uploaded_at->format('Y-m-d H:i') : '—' }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="inline-flex items-center gap-2">
                                @if($meeting->recording_download_url)
                                    <a href="{{ $meeting->recording_download_url }}" target="_blank" class="px-3 py-1.5 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold">
                                        <i class="fas fa-download ml-1"></i> تحميل
                                    </a>
                                @else
                                    <span class="text-xs text-slate-400">لا يوجد تسجيل</span>
                                @endif
                                <button type="button" onclick="navigator.clipboard.writeText('{{ url('classroom/join/' . $meeting->code) }}'); this.textContent='تم النسخ'; setTimeout(()=>this.textContent='نسخ رابط الدخول', 1200)" class="px-3 py-1.5 rounded-lg bg-slate-100 text-slate-700 text-xs font-semibold">نسخ رابط الدخول</button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center text-slate-500">
                            <i class="fas fa-video-slash text-3xl text-slate-300 mb-3 block"></i>
                            لا توجد اجتماعات مطابقة للفلاتر الحالية.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if($meetings->hasPages())
            <div class="px-4 py-3 border-t border-slate-200">{{ $meetings->links() }}</div>
        @endif
    </div>
</div>
@endsection
