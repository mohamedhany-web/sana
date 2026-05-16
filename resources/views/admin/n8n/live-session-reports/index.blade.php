@extends('layouts.admin')

@section('title', 'تقارير الذكاء الاصطناعي (n8n)')

@section('content')
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-robot text-lg"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-slate-800 dark:text-white">
                        تقارير الذكاء الاصطناعي (n8n)
                    </h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                        جميع طلبات التقارير من المعلمين والطلاب: جلسات البث المباشر واجتماعات Classroom.
                    </p>
                </div>
            </div>
        </div>

        {{-- Filters --}}
        <form method="GET" class="bg-white dark:bg-slate-800 rounded-xl p-4 border border-slate-200 dark:border-slate-700 flex flex-wrap gap-3 items-end">
            <div class="w-full sm:w-44">
                <label class="text-xs text-slate-500 mb-1 block">المصدر</label>
                <select name="source" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                    <option value="">كل المصادر</option>
                    <option value="live_session" {{ ($source ?? '') === 'live_session' ? 'selected' : '' }}>بث مباشر</option>
                    <option value="classroom_meeting" {{ ($source ?? '') === 'classroom_meeting' ? 'selected' : '' }}>اجتماع Classroom</option>
                </select>
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="text-xs text-slate-500 mb-1 block">الحالة</label>
                <select name="status" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                    <option value="">كل الحالات</option>
                    <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                    <option value="processing" {{ $status === 'processing' ? 'selected' : '' }}>قيد المعالجة</option>
                    <option value="completed" {{ $status === 'completed' ? 'selected' : '' }}>مكتمل</option>
                    <option value="failed" {{ $status === 'failed' ? 'selected' : '' }}>فشل</option>
                </select>
            </div>
            <div class="w-full sm:w-48">
                <label class="text-xs text-slate-500 mb-1 block">معرّف المستخدم (معلم/طالب)</label>
                <input type="number"
                       name="instructor_id"
                       value="{{ $instructorId }}"
                       class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm"
                       placeholder="ID المستخدم">
            </div>
            <div class="flex gap-2">
                <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-medium shadow-sm">
                    <i class="fas fa-filter ml-1"></i>
                    <span>تطبيق الفلاتر</span>
                </button>
                @if(request()->hasAny(['status', 'instructor_id', 'source']))
                    <a href="{{ route('admin.n8n.live-session-reports.index') }}"
                       class="inline-flex items-center gap-2 px-4 py-2 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-lg text-sm hover:bg-slate-300 dark:hover:bg-slate-600">
                        مسح
                    </a>
                @endif
            </div>
        </form>

        {{-- Table --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 dark:bg-slate-700/50">
                        <tr>
                            <th class="px-4 py-3 text-right text-slate-600 dark:text-slate-300 font-semibold">#</th>
                            <th class="px-4 py-3 text-right text-slate-600 dark:text-slate-300 font-semibold">المصدر</th>
                            <th class="px-4 py-3 text-right text-slate-600 dark:text-slate-300 font-semibold">الجلسة / الاجتماع</th>
                            <th class="px-4 py-3 text-right text-slate-600 dark:text-slate-300 font-semibold">المستخدم</th>
                            <th class="px-4 py-3 text-center text-slate-600 dark:text-slate-300 font-semibold">الحالة</th>
                            <th class="px-4 py-3 text-right text-slate-600 dark:text-slate-300 font-semibold">العنوان / الملخص</th>
                            <th class="px-4 py-3 text-center text-slate-600 dark:text-slate-300 font-semibold">التسجيل</th>
                            <th class="px-4 py-3 text-right text-slate-600 dark:text-slate-300 font-semibold">آخر تحديث</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse($reports as $report)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                            <td class="px-4 py-3 text-slate-500">
                                {{ $report->id }}
                                <div class="text-[10px] text-slate-400 font-mono">{{ $report->source }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs font-medium
                                    {{ $report->source === 'live_session'
                                        ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300'
                                        : 'bg-sky-100 text-sky-700 dark:bg-sky-900/40 dark:text-sky-300' }}">
                                    {{ $report->source_label }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @if($report->context_title)
                                    <div>
                                        <div class="font-semibold text-slate-800 dark:text-white">
                                            {{ \Illuminate\Support\Str::limit($report->context_title, 40) }}
                                        </div>
                                        <div class="text-[11px] text-slate-400 mt-0.5">
                                            ID: {{ $report->context_id }}
                                        </div>
                                    </div>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($report->user)
                                    <div>
                                        <div class="font-medium text-slate-800 dark:text-white">
                                            {{ $report->user->name }}
                                        </div>
                                        <div class="text-[11px] text-slate-400 mt-0.5">
                                            ID: {{ $report->user_id }}
                                        </div>
                                    </div>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @php
                                    $badgeClasses = match($report->status) {
                                        'pending' => 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-200',
                                        'processing' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300',
                                        'completed' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300',
                                        'failed' => 'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300',
                                        default => 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-200',
                                    };
                                    $statusLabels = [
                                        'pending' => 'قيد الانتظار',
                                        'processing' => 'قيد المعالجة',
                                        'completed' => 'مكتمل',
                                        'failed' => 'فشل',
                                    ];
                                @endphp
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold {{ $badgeClasses }}">
                                    @if($report->status === 'processing')
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                    @elseif($report->status === 'completed')
                                        <i class="fas fa-check-circle text-[11px]"></i>
                                    @elseif($report->status === 'failed')
                                        <i class="fas fa-times-circle text-[11px]"></i>
                                    @endif
                                    <span>{{ $statusLabels[$report->status] ?? $report->status }}</span>
                                </span>
                            </td>
                            <td class="px-4 py-3 max-w-xs">
                                <div class="space-y-1">
                                    <div class="font-semibold text-slate-800 dark:text-slate-100 truncate">
                                        {{ $report->title ?? '—' }}
                                    </div>
                                    @if($report->summary)
                                        <div class="text-xs text-slate-500 dark:text-slate-300 line-clamp-2" title="{{ $report->summary }}">
                                            {{ \Illuminate\Support\Str::limit($report->summary, 120) }}
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($report->media_url)
                                    <a href="{{ $report->media_url }}"
                                       target="_blank"
                                       rel="noopener"
                                       class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-emerald-500/60 text-emerald-600 dark:text-emerald-300 text-xs font-medium hover:bg-emerald-50 dark:hover:bg-emerald-900/30">
                                        <i class="fas fa-play text-[11px]"></i>
                                        تشغيل
                                    </a>
                                @elseif($report->audio_path)
                                    <span class="text-[11px] font-mono text-slate-500 break-all">
                                        {{ $report->audio_path }}
                                    </span>
                                @else
                                    <span class="text-slate-400 text-xs">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-500 text-xs">
                                {{ $report->updated_at?->format('Y-m-d H:i') ?? '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-12 text-center">
                                <i class="fas fa-robot text-4xl text-slate-300 dark:text-slate-600 mb-3"></i>
                                <p class="text-slate-500 dark:text-slate-300">لا توجد تقارير حالياً.</p>
                                <p class="text-xs text-slate-400 mt-2">تظهر هنا التقارير فور طلبها من غرفة البث أو صفحة اجتماع Classroom.</p>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            @if($reports->hasPages())
                <div class="px-4 py-3 border-t border-slate-200 dark:border-slate-700">
                    {{ $reports->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
