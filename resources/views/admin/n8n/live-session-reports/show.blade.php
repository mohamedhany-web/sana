@extends('layouts.admin')

@section('title', 'تقرير #' . $reportView->id)

@section('content')
    <div class="space-y-6 max-w-4xl">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div class="flex items-start gap-3">
                <a href="{{ route('admin.n8n.live-session-reports.index') }}"
                   class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-500 transition-colors">
                    <i class="fas fa-arrow-right"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-slate-800 dark:text-white font-heading">
                        {{ $reportView->title ?? 'تقرير #' . $reportView->id }}
                    </h1>
                    <div class="flex flex-wrap items-center gap-2 mt-2">
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs font-medium
                            {{ $reportView->source === 'live_session'
                                ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300'
                                : 'bg-sky-100 text-sky-700 dark:bg-sky-900/40 dark:text-sky-300' }}">
                            {{ $reportView->source_label }}
                        </span>
                        @php
                            $statusLabels = [
                                'pending' => 'قيد الانتظار',
                                'processing' => 'قيد المعالجة',
                                'completed' => 'مكتمل',
                                'failed' => 'فشل',
                            ];
                            $badgeClasses = match($reportView->status) {
                                'pending' => 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-200',
                                'processing' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300',
                                'completed' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300',
                                'failed' => 'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300',
                                default => 'bg-slate-100 text-slate-600',
                            };
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $badgeClasses }}">
                            {{ $statusLabels[$reportView->status] ?? $reportView->status }}
                        </span>
                    </div>
                </div>
            </div>
            @if($reportView->media_url)
                <a href="{{ $reportView->media_url }}"
                   target="_blank"
                   rel="noopener"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-emerald-500/60 text-emerald-600 dark:text-emerald-300 text-sm font-medium hover:bg-emerald-50 dark:hover:bg-emerald-900/30">
                    <i class="fas fa-play"></i>
                    تشغيل التسجيل
                </a>
            @endif
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6 shadow-sm">
            <h2 class="font-bold text-slate-800 dark:text-white mb-4 text-sm">بيانات التقرير</h2>
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                <div>
                    <dt class="text-slate-500 dark:text-slate-400">المستخدم</dt>
                    <dd class="font-medium text-slate-800 dark:text-white">{{ $reportView->user?->name ?? '—' }}
                        @if($reportView->user_id)<span class="text-slate-400 text-xs">(#{{ $reportView->user_id }})</span>@endif
                    </dd>
                </div>
                <div>
                    <dt class="text-slate-500 dark:text-slate-400">{{ $reportView->source === 'live_session' ? 'الجلسة' : 'الاجتماع' }}</dt>
                    <dd class="font-medium text-slate-800 dark:text-white">{{ $reportView->context_title ?? '—' }}
                        @if($reportView->context_id)<span class="text-slate-400 text-xs">(#{{ $reportView->context_id }})</span>@endif
                    </dd>
                </div>
                <div>
                    <dt class="text-slate-500 dark:text-slate-400">تاريخ الإنشاء</dt>
                    <dd>{{ $reportView->created_at?->format('Y-m-d H:i') ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500 dark:text-slate-400">آخر تحديث</dt>
                    <dd>{{ $reportView->updated_at?->format('Y-m-d H:i') ?? '—' }}</dd>
                </div>
                @if($reportView->n8n_execution_id)
                <div class="sm:col-span-2">
                    <dt class="text-slate-500 dark:text-slate-400">معرّف تنفيذ n8n</dt>
                    <dd class="font-mono text-xs break-all">{{ $reportView->n8n_execution_id }}</dd>
                </div>
                @endif
            </dl>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden shadow-sm">
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between gap-3">
                <h2 class="font-bold text-slate-800 dark:text-white flex items-center gap-2">
                    <i class="fas fa-file-alt text-emerald-500"></i>
                    نص التقرير
                </h2>
                @if($reportView->summary)
                    <button type="button"
                            onclick="navigator.clipboard.writeText(document.getElementById('report-summary-body').innerText).then(() => { this.querySelector('span').textContent = 'تم النسخ'; setTimeout(() => this.querySelector('span').textContent = 'نسخ النص', 2000); })"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium border border-slate-300 dark:border-slate-600 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50">
                        <i class="fas fa-copy"></i>
                        <span>نسخ النص</span>
                    </button>
                @endif
            </div>
            <div class="p-6">
                @if($reportView->summary)
                    <div id="report-summary-body"
                         class="text-sm text-slate-800 dark:text-slate-100 whitespace-pre-wrap leading-relaxed rounded-lg border border-slate-100 dark:border-slate-600 bg-slate-50/80 dark:bg-slate-900/50 px-4 py-4 max-h-[70vh] overflow-y-auto">{{ $reportView->summary }}</div>
                @elseif($reportView->status === 'processing' || $reportView->status === 'pending')
                    <p class="text-sm text-slate-500 dark:text-slate-400 text-center py-8">
                        <i class="fas fa-spinner fa-spin text-amber-500 ml-2"></i>
                        التقرير قيد المعالجة — سيظهر النص هنا عند اكتمال n8n.
                    </p>
                @elseif($reportView->status === 'failed')
                    <p class="text-sm text-rose-600 dark:text-rose-400 text-center py-8">
                        <i class="fas fa-exclamation-triangle ml-2"></i>
                        فشل إنشاء التقرير ولا يوجد نص متاح.
                    </p>
                @else
                    <p class="text-sm text-slate-500 dark:text-slate-400 text-center py-8">لا يوجد نص تقرير محفوظ لهذا الطلب.</p>
                @endif
            </div>
        </div>
    </div>
@endsection
