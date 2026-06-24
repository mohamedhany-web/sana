<?php $__env->startSection('title', 'الإحالات'); ?>
<?php $__env->startSection('header', ''); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 font-heading">
                <i class="fas fa-user-friends text-emerald-500 ml-2"></i>الإحالات
            </h1>
            <p class="text-sm text-slate-500 mt-1">متابعة المحيل والمحال والمكافآت والخصومات</p>
        </div>
        <a href="<?php echo e(route('admin.referral-programs.index')); ?>" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 text-slate-700 text-sm font-semibold hover:bg-slate-50">
            <i class="fas fa-gift"></i> برامج الإحالة
        </a>
    </div>

    <?php echo $__env->make('admin.partials.alert-success', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
        <?php $__currentLoopData = [
            ['label' => 'إجمالي', 'value' => $stats['total'], 'color' => 'text-slate-800'],
            ['label' => 'مكتملة', 'value' => $stats['completed'], 'color' => 'text-emerald-600'],
            ['label' => 'قيد الانتظار', 'value' => $stats['pending'], 'color' => 'text-amber-600'],
            ['label' => 'مكافآت', 'value' => number_format($stats['total_rewards'], 2).' '.(__('public.currency')), 'color' => 'text-violet-600', 'raw' => true],
            ['label' => 'خصومات', 'value' => number_format($stats['total_discounts'], 2).' '.(__('public.currency')), 'color' => 'text-rose-600', 'raw' => true],
        ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
            <div class="text-xs text-slate-500"><?php echo e($s['label']); ?></div>
            <div class="text-lg font-bold mt-1 <?php echo e($s['color']); ?>"><?php echo e(($s['raw'] ?? false) ? $s['value'] : number_format($s['value'])); ?></div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <form method="GET" action="<?php echo e(route('admin.referrals.index')); ?>" class="bg-white rounded-xl border border-slate-200 p-4 flex flex-wrap items-end gap-3">
        <div class="flex-1 min-w-[180px]">
            <label class="block text-xs font-medium text-slate-600 mb-1">بحث</label>
            <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="اسم، هاتف، كود..." class="w-full rounded-lg border-slate-300 text-sm">
        </div>
        <div class="w-full sm:w-36">
            <label class="block text-xs font-medium text-slate-600 mb-1">الحالة</label>
            <select name="status" class="w-full rounded-lg border-slate-300 text-sm">
                <option value="">الكل</option>
                <option value="pending" <?php if(request('status') === 'pending'): echo 'selected'; endif; ?>>قيد الانتظار</option>
                <option value="completed" <?php if(request('status') === 'completed'): echo 'selected'; endif; ?>>مكتملة</option>
                <option value="cancelled" <?php if(request('status') === 'cancelled'): echo 'selected'; endif; ?>>ملغاة</option>
            </select>
        </div>
        <div class="w-full sm:w-44">
            <label class="block text-xs font-medium text-slate-600 mb-1">البرنامج</label>
            <select name="program_id" class="w-full rounded-lg border-slate-300 text-sm">
                <option value="">جميع البرامج</option>
                <?php $__currentLoopData = $programs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $program): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($program->id); ?>" <?php if(request('program_id') == $program->id): echo 'selected'; endif; ?>><?php echo e($program->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="w-full sm:w-36">
            <label class="block text-xs font-medium text-slate-600 mb-1">من</label>
            <input type="date" name="date_from" value="<?php echo e(request('date_from')); ?>" class="w-full rounded-lg border-slate-300 text-sm">
        </div>
        <div class="w-full sm:w-36">
            <label class="block text-xs font-medium text-slate-600 mb-1">إلى</label>
            <input type="date" name="date_to" value="<?php echo e(request('date_to')); ?>" class="w-full rounded-lg border-slate-300 text-sm">
        </div>
        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-white text-sm font-medium"><i class="fas fa-filter"></i> تطبيق</button>
        <?php if(request()->hasAny(['search','status','program_id','date_from','date_to'])): ?>
        <a href="<?php echo e(route('admin.referrals.index')); ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-slate-300 text-slate-600 text-sm">إعادة</a>
        <?php endif; ?>
    </form>

    <?php if($referrals->count() > 0): ?>
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 text-right font-semibold text-slate-600">المحيل</th>
                        <th class="px-4 py-3 text-right font-semibold text-slate-600">المحال</th>
                        <th class="px-4 py-3 text-right font-semibold text-slate-600">البرنامج</th>
                        <th class="px-4 py-3 text-right font-semibold text-slate-600">الكود</th>
                        <th class="px-4 py-3 text-right font-semibold text-slate-600">الحالة</th>
                        <th class="px-4 py-3 text-right font-semibold text-slate-600">خصم</th>
                        <th class="px-4 py-3 text-right font-semibold text-slate-600">مكافأة</th>
                        <th class="px-4 py-3 text-right font-semibold text-slate-600">التاريخ</th>
                        <th class="px-4 py-3 text-right font-semibold text-slate-600"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__currentLoopData = $referrals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $referral): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3">
                            <div class="font-medium text-slate-900"><?php echo e($referral->referrer->name ?? '—'); ?></div>
                            <div class="text-xs text-slate-500"><?php echo e($referral->referrer->phone ?? ''); ?></div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-medium text-slate-900"><?php echo e($referral->referred->name ?? '—'); ?></div>
                            <div class="text-xs text-slate-500"><?php echo e($referral->referred->phone ?? ''); ?></div>
                        </td>
                        <td class="px-4 py-3 text-slate-600"><?php echo e($referral->referralProgram->name ?? '—'); ?></td>
                        <td class="px-4 py-3 font-mono text-xs"><?php echo e($referral->referral_code ?? $referral->code ?? '—'); ?></td>
                        <td class="px-4 py-3">
                            <?php $st = $referral->status; ?>
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium
                                <?php if($st === 'completed'): ?> bg-emerald-100 text-emerald-700
                                <?php elseif($st === 'pending'): ?> bg-amber-100 text-amber-700
                                <?php else: ?> bg-red-100 text-red-700 <?php endif; ?>">
                                <?php if($st === 'completed'): ?> مكتملة <?php elseif($st === 'pending'): ?> انتظار <?php else: ?> ملغاة <?php endif; ?>
                            </span>
                        </td>
                        <td class="px-4 py-3"><?php echo e(number_format($referral->discount_amount ?? 0, 2)); ?></td>
                        <td class="px-4 py-3 font-semibold text-emerald-600"><?php echo e(number_format($referral->reward_amount ?? 0, 2)); ?></td>
                        <td class="px-4 py-3 text-slate-500 text-xs"><?php echo e($referral->created_at->format('Y-m-d')); ?></td>
                        <td class="px-4 py-3"><a href="<?php echo e(route('admin.referrals.show', $referral)); ?>" class="text-emerald-600 hover:underline text-xs">تفاصيل</a></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
        <?php if($referrals->hasPages()): ?><div class="px-4 py-3 border-t border-slate-100"><?php echo e($referrals->links()); ?></div><?php endif; ?>
    </div>
    <?php else: ?>
    <div class="bg-white rounded-xl border border-slate-200 p-12 text-center">
        <p class="text-slate-600 mb-4">لا توجد إحالات مطابقة</p>
        <a href="<?php echo e(route('admin.referral-programs.create')); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 text-white rounded-xl text-sm font-semibold"><i class="fas fa-gift"></i> إنشاء برنامج</a>
    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sana\resources\views\admin\referrals\index.blade.php ENDPATH**/ ?>