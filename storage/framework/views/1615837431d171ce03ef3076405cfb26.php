<?php $__env->startSection('title', 'دعم الطلاب'); ?>
<?php $__env->startSection('header', 'دعم الطلاب'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $view = $view ?? 'needs_reply';
    $statCards = [
        [
            'label' => 'يحتاج ردّكم',
            'value' => number_format($stats['needs_reply'] ?? 0),
            'icon' => 'fa-reply',
            'bg' => 'bg-rose-100',
            'text' => 'text-rose-600',
            'desc' => 'تذكرة بانتظار فريق الدعم',
            'filter' => 'needs_reply',
        ],
        [
            'label' => 'مفتوحة',
            'value' => number_format($stats['open'] ?? 0),
            'icon' => 'fa-inbox',
            'bg' => 'bg-sky-100',
            'text' => 'text-sky-600',
            'desc' => 'لم تُعالج بعد',
            'filter' => 'active',
        ],
        [
            'label' => 'عاجلة نشطة',
            'value' => number_format($stats['urgent_active'] ?? 0),
            'icon' => 'fa-bolt',
            'bg' => 'bg-amber-100',
            'text' => 'text-amber-700',
            'desc' => 'أولوية عاجلة',
            'filter' => 'active',
            'extra' => ['priority' => 'urgent'],
        ],
        [
            'label' => 'جديدة اليوم',
            'value' => number_format($stats['today'] ?? 0),
            'icon' => 'fa-calendar-day',
            'bg' => 'bg-violet-100',
            'text' => 'text-violet-600',
            'desc' => 'وُردت خلال 24 ساعة',
            'filter' => 'all',
        ],
    ];
    $quickViews = [
        'needs_reply' => ['label' => 'بانتظار الرد', 'icon' => 'fa-hourglass-half'],
        'active' => ['label' => 'نشطة', 'icon' => 'fa-spinner'],
        'all' => ['label' => 'الكل', 'icon' => 'fa-list'],
        'closed' => ['label' => 'مغلقة / محلولة', 'icon' => 'fa-check-double'],
    ];
?>

<div class="space-y-6">
    <?php if(session('success')): ?>
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm font-medium flex items-center gap-2">
            <i class="fas fa-check-circle"></i>
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-5 bg-gradient-to-l from-sky-50 to-white border-b border-slate-200 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-sky-600 to-indigo-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-headset text-lg"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-black text-slate-900">دعم الطلاب</h2>
                    <p class="text-sm text-slate-600 mt-0.5">متابعة تذاكر الطلاب، الرد السريع، وإغلاق المشكلات — <?php echo e(number_format($stats['total'] ?? 0)); ?> تذكرة إجمالاً</p>
                </div>
            </div>
            <div class="flex flex-wrap gap-2">
                <?php if(Route::has('admin.support-inquiry-categories.index')): ?>
                    <a href="<?php echo e(route('admin.support-inquiry-categories.index')); ?>"
                       class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-indigo-700 bg-indigo-50 border border-indigo-200 rounded-xl hover:bg-indigo-100 transition-colors">
                        <i class="fas fa-tags"></i>
                        تصنيفات الاستفسار
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 p-6">
            <?php $__currentLoopData = $statCards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $params = array_filter([
                        'view' => $card['filter'],
                        'priority' => $card['extra']['priority'] ?? null,
                        'status' => request('status'),
                        'category_id' => request('category_id'),
                        'search' => request('search'),
                    ]);
                ?>
                <a href="<?php echo e(route('admin.support-tickets.index', $params)); ?>"
                   class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm hover:shadow-md hover:border-sky-300 transition-all block">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-xs font-semibold text-slate-600"><?php echo e($card['label']); ?></p>
                            <p class="text-2xl font-black text-slate-900 mt-1"><?php echo e($card['value']); ?></p>
                            <p class="text-[11px] text-slate-500 mt-1"><?php echo e($card['desc']); ?></p>
                        </div>
                        <div class="w-10 h-10 rounded-lg <?php echo e($card['bg']); ?> <?php echo e($card['text']); ?> flex items-center justify-center shrink-0">
                            <i class="fas <?php echo e($card['icon']); ?>"></i>
                        </div>
                    </div>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </section>

    <?php if($inquiryCategories->isNotEmpty()): ?>
        <section class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5">
            <p class="text-xs font-bold text-slate-500 mb-3">تذاكر نشطة حسب التصنيف</p>
            <div class="flex flex-wrap gap-2">
                <?php $__currentLoopData = $inquiryCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ic): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $cnt = (int) ($categoryCounts[$ic->id] ?? 0); ?>
                    <a href="<?php echo e(route('admin.support-tickets.index', array_merge(request()->only(['view', 'status', 'priority', 'search']), ['category_id' => $ic->id, 'view' => 'active']))); ?>"
                       class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold border transition-colors
                       <?php echo e((string) ($categoryId ?? '') === (string) $ic->id ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-slate-50 text-slate-700 border-slate-200 hover:border-indigo-300'); ?>">
                        <?php echo e($ic->name); ?>

                        <?php if($cnt > 0): ?>
                            <span class="min-w-[1.25rem] text-center px-1 rounded-full <?php echo e((string) ($categoryId ?? '') === (string) $ic->id ? 'bg-white/20' : 'bg-indigo-100 text-indigo-800'); ?>"><?php echo e($cnt); ?></span>
                        <?php endif; ?>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </section>
    <?php endif; ?>

    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200 bg-slate-50 space-y-4">
            <div class="flex flex-wrap gap-2">
                <?php $__currentLoopData = $quickViews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $qv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(route('admin.support-tickets.index', array_merge(request()->except('page'), ['view' => $key]))); ?>"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-bold transition-colors
                       <?php echo e($view === $key ? 'bg-sky-600 text-white shadow-sm' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-100'); ?>">
                        <i class="fas <?php echo e($qv['icon']); ?> text-xs"></i>
                        <?php echo e($qv['label']); ?>

                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <form method="GET" class="flex flex-col lg:flex-row flex-wrap items-stretch lg:items-center gap-2">
                <input type="hidden" name="view" value="<?php echo e($view); ?>">
                <div class="flex-1 min-w-[200px]">
                    <input type="search" name="search" value="<?php echo e($search ?? ''); ?>" placeholder="بحث: اسم الطالب، الجوال، أو عنوان التذكرة…"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-sky-500">
                </div>
                <select name="category_id" class="px-3 py-2.5 rounded-xl border border-slate-200 text-sm bg-white">
                    <option value="all">كل التصنيفات</option>
                    <?php $__currentLoopData = $inquiryCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ic): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($ic->id); ?>" <?php if((string) ($categoryId ?? '') === (string) $ic->id): echo 'selected'; endif; ?>><?php echo e($ic->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <select name="status" class="px-3 py-2.5 rounded-xl border border-slate-200 text-sm bg-white">
                    <option value="all" <?php if($status === 'all'): echo 'selected'; endif; ?>>كل الحالات</option>
                    <?php $__currentLoopData = \App\Models\SupportTicket::statusLabels(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $lbl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($val); ?>" <?php if($status === $val): echo 'selected'; endif; ?>><?php echo e($lbl); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <select name="priority" class="px-3 py-2.5 rounded-xl border border-slate-200 text-sm bg-white">
                    <option value="all" <?php if($priority === 'all'): echo 'selected'; endif; ?>>كل الأولويات</option>
                    <?php $__currentLoopData = \App\Models\SupportTicket::priorityLabels(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $lbl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($val); ?>" <?php if($priority === $val): echo 'selected'; endif; ?>><?php echo e($lbl); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-sky-600 text-white text-sm font-bold hover:bg-sky-700">تصفية</button>
                <?php if($search || $status !== 'all' || $priority !== 'all' || ($categoryId ?? 'all') !== 'all'): ?>
                    <a href="<?php echo e(route('admin.support-tickets.index', ['view' => $view])); ?>" class="px-4 py-2.5 text-sm font-semibold text-slate-600 hover:text-slate-900">مسح</a>
                <?php endif; ?>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr class="text-xs font-bold text-slate-600 uppercase tracking-wide">
                        <th class="px-4 py-3 text-right">الطالب</th>
                        <th class="px-4 py-3 text-right">التصنيف / الموضوع</th>
                        <th class="px-4 py-3 text-right">الحالة</th>
                        <th class="px-4 py-3 text-right">آخر نشاط</th>
                        <th class="px-4 py-3 text-right">إجراء</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__empty_1 = true; $__currentLoopData = $tickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticket): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php $awaiting = $ticket->isAwaitingAdminResponse(); ?>
                        <tr class="hover:bg-slate-50/80 <?php echo e($awaiting ? 'bg-rose-50/40' : ''); ?>">
                            <td class="px-4 py-3">
                                <p class="text-sm font-bold text-slate-900"><?php echo e($ticket->user->name ?? '—'); ?></p>
                                <p class="text-xs text-slate-500 mt-0.5" dir="ltr"><?php echo e($ticket->user->phone ?? ''); ?></p>
                                <?php if($awaiting): ?>
                                    <span class="inline-flex mt-1 items-center gap-1 text-[10px] font-bold text-rose-700 bg-rose-100 px-2 py-0.5 rounded-full">
                                        <i class="fas fa-circle text-[6px]"></i> بانتظار ردّكم
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3">
                                <p class="text-xs text-indigo-700 font-semibold"><?php echo e($ticket->inquiryCategory->name ?? 'عام'); ?></p>
                                <p class="text-sm font-semibold text-slate-900 mt-0.5 line-clamp-2"><?php echo e($ticket->subject); ?></p>
                                <div class="flex flex-wrap gap-1 mt-1">
                                    <?php echo $__env->make('admin.support-tickets._badges', ['priority' => $ticket->priority], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <?php echo $__env->make('admin.support-tickets._badges', ['status' => $ticket->status], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                <?php if($ticket->assignedAdmin): ?>
                                    <p class="text-[10px] text-slate-500 mt-1">مُعيَّنة: <?php echo e($ticket->assignedAdmin->name); ?></p>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-xs text-slate-500">
                                <?php echo e(optional($ticket->last_reply_at ?? $ticket->created_at)->format('Y-m-d')); ?>

                                <span class="text-slate-400"><?php echo e(optional($ticket->last_reply_at ?? $ticket->created_at)->format('H:i')); ?></span>
                                <p class="text-[10px] mt-0.5 text-slate-400">#<?php echo e($ticket->id); ?></p>
                            </td>
                            <td class="px-4 py-3">
                                <a href="<?php echo e(route('admin.support-tickets.show', $ticket)); ?>"
                                   class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-sm font-bold <?php echo e($awaiting ? 'bg-rose-600 text-white hover:bg-rose-700' : 'bg-sky-600 text-white hover:bg-sky-700'); ?>">
                                    <?php echo e($awaiting ? 'رد الآن' : 'فتح'); ?>

                                    <i class="fas fa-arrow-left text-xs"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="px-4 py-16 text-center">
                                <div class="max-w-sm mx-auto">
                                    <i class="fas fa-inbox text-4xl text-slate-300 mb-3"></i>
                                    <p class="text-sm font-semibold text-slate-700">لا توجد تذاكر في هذا العرض</p>
                                    <p class="text-xs text-slate-500 mt-1">عندما يفتح الطالب تذكرة من لوحته ستظهر هنا فوراً.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($tickets->hasPages()): ?>
            <div class="px-5 py-3 border-t border-slate-200"><?php echo e($tickets->links()); ?></div>
        <?php endif; ?>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sana\resources\views\admin\support-tickets\index.blade.php ENDPATH**/ ?>