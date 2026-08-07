@extends('layouts.admin')
@section('title', 'ميتينج الإدارة')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">
                <i class="fas fa-video text-indigo-500 ml-2"></i>ميتينج الإدارة
            </h1>
            <p class="text-sm text-slate-500 mt-1">أنشئي اجتماع LiveKit وأرسلي الرابط لأي شخص — بدون تسجيل في المنصة.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <form method="POST" action="{{ route('admin.classroom.start') }}">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-semibold shadow-lg shadow-emerald-600/20 transition-all">
                    <i class="fas fa-bolt"></i> ابدأ الآن
                </button>
            </form>
            <a href="{{ route('admin.classroom.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-semibold shadow-lg shadow-indigo-600/25 transition-all">
                <i class="fas fa-plus"></i> إنشاء ميتينج
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="rounded-xl bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 text-sm">{{ session('error') }}</div>
    @endif

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl p-4 border border-slate-200">
            <p class="text-2xl font-bold text-slate-800">{{ number_format($stats['total']) }}</p>
            <p class="text-xs text-slate-500">إجمالي</p>
        </div>
        <div class="bg-white rounded-xl p-4 border border-red-200">
            <p class="text-2xl font-bold text-red-600">{{ number_format($stats['live']) }}</p>
            <p class="text-xs text-slate-500">مباشر الآن</p>
        </div>
        <div class="bg-white rounded-xl p-4 border border-blue-200">
            <p class="text-2xl font-bold text-blue-600">{{ number_format($stats['scheduled']) }}</p>
            <p class="text-xs text-slate-500">مجدول</p>
        </div>
        <div class="bg-white rounded-xl p-4 border border-emerald-200">
            <p class="text-2xl font-bold text-emerald-600">{{ number_format($stats['ended']) }}</p>
            <p class="text-xs text-slate-500">منتهي</p>
        </div>
    </div>

    <form method="GET" class="bg-white rounded-xl p-4 border border-slate-200 flex flex-wrap gap-2 items-center">
        <span class="text-xs text-slate-500 ml-1">الحالة:</span>
        @foreach(['all' => 'الكل', 'live' => 'مباشر', 'scheduled' => 'مجدول', 'ended' => 'منتهي'] as $k => $label)
            <button type="submit" name="status" value="{{ $k }}"
                    class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ $status === $k ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-700' }}">
                {{ $label }}
            </button>
        @endforeach
    </form>

    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="px-4 py-3 text-right">الاجتماع</th>
                        <th class="px-4 py-3 text-right">الكود</th>
                        <th class="px-4 py-3 text-right">المضيف</th>
                        <th class="px-4 py-3 text-right">الحالة</th>
                        <th class="px-4 py-3 text-right">المشاركون</th>
                        <th class="px-4 py-3 text-right">رابط الضيف</th>
                        <th class="px-4 py-3 text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($meetings as $m)
                        @php $joinUrl = $joinBaseUrl.'/'.$m->code; @endphp
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.classroom.show', $m) }}" class="font-semibold text-slate-800 hover:text-indigo-600">{{ $m->title ?: 'ميتينج بدون عنوان' }}</a>
                                <p class="text-xs text-slate-400 mt-0.5">{{ $m->created_at?->format('Y/m/d H:i') }}</p>
                            </td>
                            <td class="px-4 py-3 font-mono text-slate-700">{{ $m->code }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $m->user?->name ?? '—' }}</td>
                            <td class="px-4 py-3">
                                @if($m->isLive())
                                    <span class="px-2 py-0.5 rounded-full bg-red-100 text-red-700 text-xs font-semibold">مباشر</span>
                                @elseif(!$m->started_at)
                                    <span class="px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 text-xs font-semibold">مجدول</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 text-xs font-semibold">منتهي</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-600">{{ (int) $m->participants_count }} / {{ (int) $m->max_participants }}</td>
                            <td class="px-4 py-3">
                                <button type="button"
                                        onclick="navigator.clipboard.writeText('{{ $joinUrl }}'); this.textContent='تم النسخ'; setTimeout(()=>this.textContent='نسخ الرابط', 1200)"
                                        class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-xs font-semibold text-slate-700">
                                    نسخ الرابط
                                </button>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center gap-1">
                                    <a href="{{ route('admin.classroom.show', $m) }}" class="p-1.5 rounded-lg hover:bg-slate-100 text-slate-500" title="عرض"><i class="fas fa-eye"></i></a>
                                    @if($m->isLive())
                                        <a href="{{ route('admin.classroom.room', $m) }}" class="p-1.5 rounded-lg hover:bg-red-50 text-red-500" title="دخول الغرفة"><i class="fas fa-broadcast-tower"></i></a>
                                    @elseif(!$m->started_at && !$m->ended_at)
                                        <form method="POST" action="{{ route('admin.classroom.start-meeting', $m) }}">@csrf
                                            <button class="p-1.5 rounded-lg hover:bg-emerald-50 text-emerald-600" title="بدء"><i class="fas fa-play"></i></button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center text-slate-500">
                                <i class="fas fa-video-slash text-3xl text-slate-300 mb-3 block"></i>
                                لا توجد ميتينجات إدارة بعد — ابدئي واحدًا الآن.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($meetings->hasPages())
            <div class="px-4 py-3 border-t border-slate-100">{{ $meetings->links() }}</div>
        @endif
    </div>
</div>
@endsection
