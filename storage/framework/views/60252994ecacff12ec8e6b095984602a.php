<?php $__env->startSection('title', 'انضمام المعلمين - ' . config('app.name', 'Sana')); ?>
<?php $__env->startSection('header', 'طلبات انضمام المعلمين'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6 sm:space-y-10">
    <?php if(session('success')): ?>
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 lg:px-12 border-b border-slate-200 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-slate-900">انضمام المعلمين</h2>
                <p class="text-sm text-slate-500 mt-2">مراجعة طلبات التسجيل من الرابط العام — بعد القبول يستطيع المعلم تسجيل الدخول من بوابة المدربين.</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-2 sm:items-center">
                <input type="text" readonly value="<?php echo e($publicApplyUrl); ?>" id="publicApplyUrl"
                       class="flex-1 min-w-0 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm font-mono" dir="ltr">
                <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('publicApplyUrl').value); this.textContent='تم النسخ!'; setTimeout(() => this.textContent='نسخ الرابط', 2000)"
                        class="inline-flex items-center justify-center gap-2 rounded-2xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white shadow hover:bg-sky-700 whitespace-nowrap">
                    <i class="fas fa-copy"></i>
                    نسخ الرابط
                </button>
                <a href="<?php echo e($publicApplyUrl); ?>" target="_blank" rel="noopener"
                   class="inline-flex items-center justify-center gap-2 rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 whitespace-nowrap">
                    <i class="fas fa-external-link-alt"></i>
                    فتح النموذج
                </a>
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 p-5 sm:p-8">
            <div class="rounded-2xl border border-slate-200 bg-white/70 p-5">
                <p class="text-xs font-semibold uppercase tracking-widest text-slate-500">الإجمالي</p>
                <p class="mt-3 text-2xl font-bold text-slate-900"><?php echo e($stats['total'] ?? 0); ?></p>
            </div>
            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5">
                <p class="text-xs font-semibold uppercase tracking-widest text-amber-700">بانتظار الموافقة</p>
                <p class="mt-3 text-2xl font-bold text-amber-800"><?php echo e($stats['pending'] ?? 0); ?></p>
            </div>
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5">
                <p class="text-xs font-semibold uppercase tracking-widest text-emerald-700">مقبولة</p>
                <p class="mt-3 text-2xl font-bold text-emerald-800"><?php echo e($stats['approved'] ?? 0); ?></p>
            </div>
            <div class="rounded-2xl border border-rose-200 bg-rose-50 p-5">
                <p class="text-xs font-semibold uppercase tracking-widest text-rose-700">مرفوضة</p>
                <p class="mt-3 text-2xl font-bold text-rose-800"><?php echo e($stats['rejected'] ?? 0); ?></p>
            </div>
        </div>
    </section>

    <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 lg:px-12">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-2">البحث</label>
                    <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="الاسم، البريد، الجوال..."
                           class="w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-2.5 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-2">الحالة</label>
                    <select name="status" class="w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-2.5 text-sm">
                        <option value="">جميع الحالات</option>
                        <option value="<?php echo e(\App\Models\InstructorProfile::STATUS_PENDING_REVIEW); ?>" <?php if(request('status') === \App\Models\InstructorProfile::STATUS_PENDING_REVIEW): echo 'selected'; endif; ?>>بانتظار الموافقة</option>
                        <option value="<?php echo e(\App\Models\InstructorProfile::STATUS_APPROVED); ?>" <?php if(request('status') === \App\Models\InstructorProfile::STATUS_APPROVED): echo 'selected'; endif; ?>>مقبول</option>
                        <option value="<?php echo e(\App\Models\InstructorProfile::STATUS_REJECTED); ?>" <?php if(request('status') === \App\Models\InstructorProfile::STATUS_REJECTED): echo 'selected'; endif; ?>>مرفوض</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-2xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white shadow hover:bg-sky-700">
                        <i class="fas fa-search"></i>
                        بحث
                    </button>
                </div>
            </form>
        </div>
    </section>

    <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 lg:px-12 border-b border-slate-200">
            <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                <i class="fas fa-inbox text-sky-600"></i>
                قائمة الطلبات
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr class="text-xs font-semibold uppercase tracking-widest text-slate-500">
                        <th class="px-6 py-4 text-right">المعلم</th>
                        <th class="px-6 py-4 text-right">العنوان</th>
                        <th class="px-6 py-4 text-right">الحالة</th>
                        <th class="px-6 py-4 text-right">تاريخ التقديم</th>
                        <th class="px-6 py-4 text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    <?php $__empty_1 = true; $__currentLoopData = $applications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $app): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-slate-900"><?php echo e($app->user?->name ?? '—'); ?></div>
                            <div class="text-xs text-slate-500 mt-0.5" dir="ltr"><?php echo e($app->user?->email); ?></div>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-700"><?php echo e(Str::limit($app->headline, 60)); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php if($app->status === \App\Models\InstructorProfile::STATUS_PENDING_REVIEW): ?>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">بانتظار الموافقة</span>
                            <?php elseif($app->status === \App\Models\InstructorProfile::STATUS_APPROVED): ?>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">مقبول</span>
                            <?php elseif($app->status === \App\Models\InstructorProfile::STATUS_REJECTED): ?>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-rose-100 text-rose-800">مرفوض</span>
                            <?php else: ?>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700"><?php echo e($app->status); ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600 whitespace-nowrap">
                            <?php echo e($app->submitted_at?->timezone(config('app.timezone'))->format('Y-m-d H:i') ?? '—'); ?>

                        </td>
                        <td class="px-6 py-4 text-center">
                            <a href="<?php echo e(route('admin.instructor-applications.show', $app)); ?>"
                               class="inline-flex items-center gap-1 rounded-xl bg-sky-50 text-sky-700 px-3 py-1.5 text-xs font-semibold hover:bg-sky-100">
                                <i class="fas fa-eye"></i>
                                مراجعة
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-slate-500">لا توجد طلبات حالياً.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($applications->hasPages()): ?>
        <div class="px-6 py-4 border-t border-slate-200"><?php echo e($applications->links()); ?></div>
        <?php endif; ?>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sana\resources\views/admin/instructor-applications/index.blade.php ENDPATH**/ ?>