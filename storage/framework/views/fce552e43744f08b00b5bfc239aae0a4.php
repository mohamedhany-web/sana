<?php $__env->startSection('title', 'تقرير #' . $reportView->id); ?>

<?php $__env->startSection('content'); ?>
    <div class="space-y-6 max-w-4xl">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div class="flex items-start gap-3">
                <a href="<?php echo e(route('admin.n8n.live-session-reports.index')); ?>"
                   class="p-2 rounded-lg hover:bg-slate-100 text-slate-500 transition-colors">
                    <i class="fas fa-arrow-right"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-slate-800 font-heading">
                        <?php echo e($reportView->title ?? 'تقرير #' . $reportView->id); ?>

                    </h1>
                    <div class="flex flex-wrap items-center gap-2 mt-2">
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs font-medium
                            <?php echo e($reportView->source === 'live_session'
                                ? 'bg-indigo-100 text-indigo-700'
                                : 'bg-sky-100 text-sky-700'); ?>">
                            <?php echo e($reportView->source_label); ?>

                        </span>
                        <?php
                            $statusLabels = [
                                'pending' => 'قيد الانتظار',
                                'processing' => 'قيد المعالجة',
                                'completed' => 'مكتمل',
                                'failed' => 'فشل',
                            ];
                            $badgeClasses = match($reportView->status) {
                                'pending' => 'bg-slate-100 text-slate-600',
                                'processing' => 'bg-amber-100 text-amber-700',
                                'completed' => 'bg-emerald-100 text-emerald-700',
                                'failed' => 'bg-rose-100 text-rose-700',
                                default => 'bg-slate-100 text-slate-600',
                            };
                        ?>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold <?php echo e($badgeClasses); ?>">
                            <?php echo e($statusLabels[$reportView->status] ?? $reportView->status); ?>

                        </span>
                    </div>
                </div>
            </div>
            <?php if($reportView->media_url): ?>
                <a href="<?php echo e($reportView->media_url); ?>"
                   target="_blank"
                   rel="noopener"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-emerald-500/60 text-emerald-600 text-sm font-medium hover:bg-emerald-50">
                    <i class="fas fa-play"></i>
                    تشغيل التسجيل
                </a>
            <?php endif; ?>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
            <h2 class="font-bold text-slate-800 mb-4 text-sm">بيانات التقرير</h2>
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                <div>
                    <dt class="text-slate-500">المستخدم</dt>
                    <dd class="font-medium text-slate-800"><?php echo e($reportView->user?->name ?? '—'); ?>

                        <?php if($reportView->user_id): ?><span class="text-slate-400 text-xs">(#<?php echo e($reportView->user_id); ?>)</span><?php endif; ?>
                    </dd>
                </div>
                <div>
                    <dt class="text-slate-500"><?php echo e($reportView->source === 'live_session' ? 'الجلسة' : 'الاجتماع'); ?></dt>
                    <dd class="font-medium text-slate-800"><?php echo e($reportView->context_title ?? '—'); ?>

                        <?php if($reportView->context_id): ?><span class="text-slate-400 text-xs">(#<?php echo e($reportView->context_id); ?>)</span><?php endif; ?>
                    </dd>
                </div>
                <div>
                    <dt class="text-slate-500">تاريخ الإنشاء</dt>
                    <dd><?php echo e($reportView->created_at?->format('Y-m-d H:i') ?? '—'); ?></dd>
                </div>
                <div>
                    <dt class="text-slate-500">آخر تحديث</dt>
                    <dd><?php echo e($reportView->updated_at?->format('Y-m-d H:i') ?? '—'); ?></dd>
                </div>
                <?php if($reportView->n8n_execution_id): ?>
                <div class="sm:col-span-2">
                    <dt class="text-slate-500">معرّف تنفيذ n8n</dt>
                    <dd class="font-mono text-xs break-all"><?php echo e($reportView->n8n_execution_id); ?></dd>
                </div>
                <?php endif; ?>
            </dl>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
            <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between gap-3">
                <h2 class="font-bold text-slate-800 flex items-center gap-2">
                    <i class="fas fa-file-alt text-emerald-500"></i>
                    نص التقرير
                </h2>
                <?php if($reportView->summary): ?>
                    <button type="button"
                            onclick="navigator.clipboard.writeText(document.getElementById('report-summary-body').innerText).then(() => { this.querySelector('span').textContent = 'تم النسخ'; setTimeout(() => this.querySelector('span').textContent = 'نسخ النص', 2000); })"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium border border-slate-300 text-slate-600 hover:bg-slate-50">
                        <i class="fas fa-copy"></i>
                        <span>نسخ النص</span>
                    </button>
                <?php endif; ?>
            </div>
            <div class="p-6">
                <?php if($reportView->summary): ?>
                    <div id="report-summary-body"
                         class="text-sm text-slate-800 whitespace-pre-wrap leading-relaxed rounded-lg border border-slate-100 bg-slate-50/80 px-4 py-4 max-h-[70vh] overflow-y-auto"><?php echo e($reportView->summary); ?></div>
                <?php elseif($reportView->status === 'processing' || $reportView->status === 'pending'): ?>
                    <p class="text-sm text-slate-500 text-center py-8">
                        <i class="fas fa-spinner fa-spin text-amber-500 ml-2"></i>
                        التقرير قيد المعالجة — سيظهر النص هنا عند اكتمال n8n.
                    </p>
                <?php elseif($reportView->status === 'failed'): ?>
                    <p class="text-sm text-rose-600 text-center py-8">
                        <i class="fas fa-exclamation-triangle ml-2"></i>
                        فشل إنشاء التقرير ولا يوجد نص متاح.
                    </p>
                <?php else: ?>
                    <p class="text-sm text-slate-500 text-center py-8">لا يوجد نص تقرير محفوظ لهذا الطلب.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sana\resources\views\admin\n8n\live-session-reports\show.blade.php ENDPATH**/ ?>