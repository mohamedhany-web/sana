<?php $__env->startSection('title', 'برامج الإحالة'); ?>
<?php $__env->startSection('header', ''); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 font-heading">
                <i class="fas fa-gift text-emerald-500 ml-2"></i>برامج الإحالة
            </h1>
            <p class="text-sm text-slate-500 mt-1">تحديد خصم المحال ومكافأة المحيل — يُطبَّق عند التسجيل برابط الإحالة</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="<?php echo e(route('admin.referrals.index')); ?>" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 text-slate-700 text-sm font-semibold hover:bg-slate-50">
                <i class="fas fa-user-friends"></i> الإحالات
            </a>
            <a href="<?php echo e(route('admin.referral-programs.create')); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-semibold text-sm shadow-lg shadow-emerald-500/20 transition-all">
                <i class="fas fa-plus"></i> برنامج جديد
            </a>
        </div>
    </div>

    <?php echo $__env->make('admin.partials.alert-success', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('admin.partials.alert-errors', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('admin.partials.referral-program-how-it-works', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <?php $__currentLoopData = [
            ['label' => 'إجمالي البرامج', 'value' => $stats['total'], 'class' => 'text-slate-800'],
            ['label' => 'نشطة', 'value' => $stats['active'], 'class' => 'text-emerald-600'],
            ['label' => 'معطّلة', 'value' => $stats['inactive'], 'class' => 'text-rose-600'],
            ['label' => 'صالحة الآن', 'value' => $stats['valid_now'] ?? 0, 'class' => 'text-violet-600'],
        ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
            <div class="text-sm text-slate-500"><?php echo e($card['label']); ?></div>
            <div class="text-2xl font-bold mt-1 <?php echo e($card['class']); ?>"><?php echo e(number_format($card['value'])); ?></div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <?php if($programs->count() > 0): ?>
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 text-right font-semibold text-slate-600">البرنامج</th>
                        <th class="px-4 py-3 text-right font-semibold text-slate-600">افتراضي</th>
                        <th class="px-4 py-3 text-right font-semibold text-slate-600">إحالات</th>
                        <th class="px-4 py-3 text-right font-semibold text-slate-600">خصم المحال</th>
                        <th class="px-4 py-3 text-right font-semibold text-slate-600">مكافأة المحيل</th>
                        <th class="px-4 py-3 text-right font-semibold text-slate-600">الحالة</th>
                        <th class="px-4 py-3 text-right font-semibold text-slate-600">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__currentLoopData = $programs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $program): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-4 py-3">
                            <a href="<?php echo e(route('admin.referral-programs.show', $program)); ?>" class="font-semibold text-slate-900 hover:text-emerald-600"><?php echo e($program->name); ?></a>
                            <?php if($program->description): ?>
                            <p class="text-xs text-slate-500 mt-0.5"><?php echo e(\Illuminate\Support\Str::limit($program->description, 48)); ?></p>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3">
                            <?php if($program->is_default): ?>
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-violet-100 text-violet-700">افتراضي</span>
                            <?php else: ?>
                            <form action="<?php echo e(route('admin.referral-programs.set-default', $program)); ?>" method="POST" class="inline" onsubmit="return confirm('تعيين كبرنامج افتراضي؟');">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="text-xs text-violet-600 hover:underline disabled:opacity-40" <?php if(!$program->is_active || !$program->isValid()): echo 'disabled'; endif; ?>>تعيين</button>
                            </form>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 font-mono text-slate-700"><?php echo e(number_format($program->referrals_count ?? 0)); ?></td>
                        <td class="px-4 py-3 text-slate-800">
                            <?php if($program->discount_type === 'percentage'): ?>
                                <?php echo e(number_format($program->discount_value, 0)); ?>%
                            <?php else: ?>
                                <?php echo e(number_format($program->discount_value, 2)); ?> <?php echo e(__('public.currency')); ?>

                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 text-slate-800">
                            <?php if($program->referrer_reward_value): ?>
                                <?php if($program->referrer_reward_type === 'percentage'): ?> <?php echo e(number_format($program->referrer_reward_value, 0)); ?>%
                                <?php elseif($program->referrer_reward_type === 'points'): ?> <?php echo e(number_format($program->referrer_reward_value, 0)); ?> نقطة
                                <?php else: ?> <?php echo e(number_format($program->referrer_reward_value, 2)); ?> <?php echo e(__('public.currency')); ?>

                                <?php endif; ?>
                            <?php else: ?> — <?php endif; ?>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo e(($program->is_active && $program->isValid()) ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600'); ?>">
                                <?php echo e(($program->is_active && $program->isValid()) ? 'نشط' : 'غير نشط'); ?>

                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <a href="<?php echo e(route('admin.referral-programs.show', $program)); ?>" class="p-1.5 rounded-lg text-slate-500 hover:bg-slate-100" title="عرض"><i class="fas fa-eye"></i></a>
                                <a href="<?php echo e(route('admin.referral-programs.edit', $program)); ?>" class="p-1.5 rounded-lg text-amber-600 hover:bg-amber-50" title="تعديل"><i class="fas fa-edit"></i></a>
                                <form action="<?php echo e(route('admin.referral-programs.destroy', $program)); ?>" method="POST" class="inline" onsubmit="return confirm('حذف البرنامج؟');">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="p-1.5 rounded-lg text-red-600 hover:bg-red-50" title="حذف"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
        <?php if($programs->hasPages()): ?>
        <div class="px-4 py-3 border-t border-slate-100"><?php echo e($programs->links()); ?></div>
        <?php endif; ?>
    </div>
    <?php else: ?>
    <div class="bg-white rounded-xl border border-slate-200 p-12 text-center shadow-sm">
        <i class="fas fa-gift text-4xl text-slate-300 mb-4"></i>
        <p class="text-slate-600 font-medium mb-1">لا توجد برامج إحالة بعد</p>
        <p class="text-sm text-slate-500 mb-6">أنشئ برنامجاً يحدد خصم الصديق ومكافأة المحيل</p>
        <a href="<?php echo e(route('admin.referral-programs.create')); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-semibold">
            <i class="fas fa-plus"></i> إنشاء برنامج
        </a>
    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sana\resources\views/admin/referral-programs/index.blade.php ENDPATH**/ ?>