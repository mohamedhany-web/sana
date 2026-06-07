<?php $__env->startSection('title', 'انضمام المعلمين - ' . config('app.name', 'Sana')); ?>
<?php $__env->startSection('header', 'طلبات انضمام المعلمين'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6 sm:space-y-10">
    <?php if(session('success')): ?>
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="rounded-xl bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3"><?php echo e(session('error')); ?></div>
    <?php endif; ?>

    <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 lg:px-12 border-b border-slate-200 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-slate-900">انضمام المعلمين</h2>
                <p class="text-sm text-slate-500 mt-2">إدارة كاملة لطلبات التسجيل — مراجعة، تعديل، تفعيل/إيقاف الحساب، وحذف.</p>
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
        <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-6 gap-4 p-5 sm:p-8">
            <div class="rounded-2xl border border-slate-200 bg-white/70 p-4">
                <p class="text-xs font-semibold uppercase tracking-widest text-slate-500">الإجمالي</p>
                <p class="mt-2 text-xl font-bold text-slate-900"><?php echo e($stats['total'] ?? 0); ?></p>
            </div>
            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-widest text-amber-700">بانتظار</p>
                <p class="mt-2 text-xl font-bold text-amber-800"><?php echo e($stats['pending'] ?? 0); ?></p>
            </div>
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-widest text-emerald-700">مقبولة</p>
                <p class="mt-2 text-xl font-bold text-emerald-800"><?php echo e($stats['approved'] ?? 0); ?></p>
            </div>
            <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-widest text-rose-700">مرفوضة</p>
                <p class="mt-2 text-xl font-bold text-rose-800"><?php echo e($stats['rejected'] ?? 0); ?></p>
            </div>
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50/50 p-4">
                <p class="text-xs font-semibold uppercase tracking-widest text-emerald-700">حسابات مفعّلة</p>
                <p class="mt-2 text-xl font-bold text-emerald-800"><?php echo e($stats['active_accounts'] ?? 0); ?></p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-widest text-slate-600">حسابات موقوفة</p>
                <p class="mt-2 text-xl font-bold text-slate-800"><?php echo e($stats['inactive_accounts'] ?? 0); ?></p>
            </div>
        </div>
    </section>

    <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 lg:px-12">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-slate-500 mb-2">البحث</label>
                    <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="الاسم، البريد، الجوال..."
                           class="w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-2.5 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-2">حالة الطلب</label>
                    <select name="status" class="w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-2.5 text-sm">
                        <option value="">جميع الحالات</option>
                        <option value="<?php echo e(\App\Models\InstructorProfile::STATUS_PENDING_REVIEW); ?>" <?php if(request('status') === \App\Models\InstructorProfile::STATUS_PENDING_REVIEW): echo 'selected'; endif; ?>>بانتظار الموافقة</option>
                        <option value="<?php echo e(\App\Models\InstructorProfile::STATUS_APPROVED); ?>" <?php if(request('status') === \App\Models\InstructorProfile::STATUS_APPROVED): echo 'selected'; endif; ?>>مقبول</option>
                        <option value="<?php echo e(\App\Models\InstructorProfile::STATUS_REJECTED); ?>" <?php if(request('status') === \App\Models\InstructorProfile::STATUS_REJECTED): echo 'selected'; endif; ?>>مرفوض</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-2">حالة الحساب</label>
                    <select name="account" class="w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-2.5 text-sm">
                        <option value="">الكل</option>
                        <option value="active" <?php if(request('account') === 'active'): echo 'selected'; endif; ?>>مفعّل</option>
                        <option value="inactive" <?php if(request('account') === 'inactive'): echo 'selected'; endif; ?>>موقوف</option>
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 rounded-2xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white shadow hover:bg-sky-700">
                        <i class="fas fa-search"></i>
                        بحث
                    </button>
                    <?php if(request()->anyFilled(['search', 'status', 'account'])): ?>
                        <a href="<?php echo e(route('admin.instructor-applications.index')); ?>"
                           class="inline-flex items-center justify-center rounded-2xl border border-slate-200 px-3 py-2.5 text-slate-600 hover:bg-slate-50" title="مسح">
                            <i class="fas fa-times"></i>
                        </a>
                    <?php endif; ?>
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
                        <th class="px-6 py-4 text-right">حالة الطلب</th>
                        <th class="px-6 py-4 text-right">لوحة المعلم</th>
                        <th class="px-6 py-4 text-right">الحساب</th>
                        <th class="px-6 py-4 text-right">تاريخ التقديم</th>
                        <th class="px-6 py-4 text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    <?php $__empty_1 = true; $__currentLoopData = $applications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $app): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $isActive = (bool) ($app->user?->is_active);
                        $canManage = $app->user && !\App\Services\InstructorApplicationService::mustKeepAccountActive($app->user);
                    ?>
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-slate-900"><?php echo e($app->user?->name ?? '—'); ?></div>
                            <div class="text-xs text-slate-500 mt-0.5" dir="ltr"><?php echo e($app->user?->email); ?></div>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-700"><?php echo e(Str::limit($app->headline, 50)); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php if($app->status === \App\Models\InstructorProfile::STATUS_PENDING_REVIEW): ?>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">بانتظار</span>
                            <?php elseif($app->status === \App\Models\InstructorProfile::STATUS_APPROVED): ?>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">مقبول</span>
                            <?php elseif($app->status === \App\Models\InstructorProfile::STATUS_REJECTED): ?>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-rose-100 text-rose-800">مرفوض</span>
                            <?php else: ?>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700"><?php echo e($app->status); ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600 whitespace-nowrap">
                            <?php if($app->status === \App\Models\InstructorProfile::STATUS_APPROVED): ?>
                                <?php echo e($app->portalModeLabel()); ?>

                            <?php else: ?>
                                —
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php if($isActive): ?>
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700">
                                    <i class="fas fa-circle text-[6px]"></i> مفعّل
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600">
                                    <i class="fas fa-ban text-[10px]"></i> موقوف
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600 whitespace-nowrap">
                            <?php echo e($app->submitted_at?->timezone(config('app.timezone'))->format('Y-m-d H:i') ?? '—'); ?>

                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap items-center justify-center gap-1.5">
                                <a href="<?php echo e(route('admin.instructor-applications.show', $app)); ?>" data-turbo="false"
                                   class="inline-flex items-center gap-1 rounded-xl bg-sky-50 text-sky-700 px-2.5 py-1.5 text-xs font-semibold hover:bg-sky-100" title="عرض">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="<?php echo e(route('admin.instructor-applications.edit', $app)); ?>" data-turbo="false"
                                   class="inline-flex items-center gap-1 rounded-xl bg-violet-50 text-violet-700 px-2.5 py-1.5 text-xs font-semibold hover:bg-violet-100" title="تعديل">
                                    <i class="fas fa-pen"></i>
                                </a>
                                <?php if($canManage): ?>
                                    <form method="POST" action="<?php echo e(route('admin.instructor-applications.toggle-account', $app)); ?>" data-turbo="false" class="inline">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" title="<?php echo e($isActive ? 'إيقاف' : 'تفعيل'); ?>"
                                                class="inline-flex items-center gap-1 rounded-xl <?php echo e($isActive ? 'bg-amber-50 text-amber-700 hover:bg-amber-100' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100'); ?> px-2.5 py-1.5 text-xs font-semibold">
                                            <i class="fas <?php echo e($isActive ? 'fa-pause' : 'fa-play'); ?>"></i>
                                        </button>
                                    </form>
                                    <form method="POST" action="<?php echo e(route('admin.instructor-applications.destroy', $app)); ?>" data-turbo="false" class="inline"
                                          onsubmit="return confirm('حذف هذا الطلب؟')">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" title="حذف"
                                                class="inline-flex items-center gap-1 rounded-xl bg-rose-50 text-rose-700 px-2.5 py-1.5 text-xs font-semibold hover:bg-rose-100">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-500">لا توجد طلبات حالياً.</td>
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

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sana\resources\views\admin\instructor-applications\index.blade.php ENDPATH**/ ?>