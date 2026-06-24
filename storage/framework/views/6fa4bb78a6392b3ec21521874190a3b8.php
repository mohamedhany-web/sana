<?php $__env->startSection('title', 'برنامج إحالة: ' . $referralProgram->name); ?>
<?php $__env->startSection('header', ''); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div class="flex items-start gap-3">
            <a href="<?php echo e(route('admin.referral-programs.index')); ?>" class="p-2 rounded-lg hover:bg-slate-100 text-slate-500 transition-colors"><i class="fas fa-arrow-right"></i></a>
            <div>
                <h1 class="text-2xl font-bold text-slate-800 font-heading">
                    <i class="fas fa-gift text-emerald-500 ml-2"></i><?php echo e($referralProgram->name); ?>

                </h1>
                <?php if($referralProgram->description): ?>
                <p class="text-slate-600 mt-1"><?php echo e($referralProgram->description); ?></p>
                <?php endif; ?>
                <div class="flex flex-wrap gap-2 mt-3">
                    <?php if($referralProgram->is_default): ?>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-violet-100 text-violet-700">البرنامج الافتراضي</span>
                    <?php endif; ?>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo e($referralProgram->is_active && $referralProgram->isValid() ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600'); ?>">
                        <?php echo e($referralProgram->is_active && $referralProgram->isValid() ? 'نشط' : 'معطّل أو منتهي'); ?>

                    </span>
                </div>
            </div>
        </div>
        <div class="flex flex-wrap gap-2">
            <?php if(!$referralProgram->is_default && $referralProgram->is_active && $referralProgram->isValid()): ?>
            <form action="<?php echo e(route('admin.referral-programs.set-default', $referralProgram)); ?>" method="POST" onsubmit="return confirm('تعيين هذا البرنامج كافتراضي؟');">
                <?php echo csrf_field(); ?>
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-violet-200 text-violet-700 text-sm font-semibold hover:bg-violet-50">
                    <i class="fas fa-star"></i> تعيين كافتراضي
                </button>
            </form>
            <?php endif; ?>
            <a href="<?php echo e(route('admin.referral-programs.edit', $referralProgram)); ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold transition-colors">
                <i class="fas fa-edit"></i> تعديل
            </a>
        </div>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
        <?php $__currentLoopData = [
            ['label' => 'إجمالي الإحالات', 'value' => $stats['total_referrals'], 'color' => 'text-slate-800'],
            ['label' => 'مكتملة', 'value' => $stats['completed_referrals'], 'color' => 'text-emerald-600'],
            ['label' => 'قيد الانتظار', 'value' => $stats['pending_referrals'], 'color' => 'text-amber-600'],
            ['label' => 'إجمالي الخصومات', 'value' => number_format($stats['total_discount_given'], 2).' '.(__('public.currency')), 'color' => 'text-violet-600', 'raw' => true],
            ['label' => 'إجمالي المكافآت', 'value' => number_format($stats['total_rewards_given'], 2).' '.(__('public.currency')), 'color' => 'text-rose-600', 'raw' => true],
        ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
            <div class="text-xs text-slate-500"><?php echo e($s['label']); ?></div>
            <div class="text-lg font-bold mt-1 <?php echo e($s['color']); ?>"><?php echo e(($s['raw'] ?? false) ? $s['value'] : number_format($s['value'])); ?></div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <div class="grid md:grid-cols-2 gap-5">
        <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
            <h2 class="font-bold text-slate-800 mb-4 flex items-center gap-2"><i class="fas fa-info-circle text-emerald-500"></i> تفاصيل البرنامج</h2>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between gap-4"><dt class="text-slate-500">افتراضي للتسجيل</dt><dd class="font-medium <?php echo e($referralProgram->is_default ? 'text-violet-600' : 'text-slate-600'); ?>"><?php echo e($referralProgram->is_default ? 'نعم' : 'لا'); ?></dd></div>
                <div class="flex justify-between gap-4"><dt class="text-slate-500">نوع الخصم للمحال</dt><dd class="font-medium text-slate-800"><?php echo e($referralProgram->discount_type == 'percentage' ? 'نسبة مئوية' : 'مبلغ ثابت'); ?></dd></div>
                <div class="flex justify-between gap-4"><dt class="text-slate-500">قيمة الخصم</dt><dd class="font-bold text-slate-800">
                    <?php if($referralProgram->discount_type == 'percentage'): ?>
                        <?php echo e(number_format($referralProgram->discount_value, 0)); ?>%
                    <?php else: ?>
                        <?php echo e(number_format($referralProgram->discount_value, 2)); ?> <?php echo e(__('public.currency')); ?>

                    <?php endif; ?>
                </dd></div>
                <?php if($referralProgram->maximum_discount): ?>
                <div class="flex justify-between gap-4"><dt class="text-slate-500">الحد الأقصى للخصم</dt><dd class="font-mono"><?php echo e(number_format($referralProgram->maximum_discount, 2)); ?> <?php echo e(__('public.currency')); ?></dd></div>
                <?php endif; ?>
                <?php if($referralProgram->minimum_order_amount): ?>
                <div class="flex justify-between gap-4"><dt class="text-slate-500">الحد الأدنى للطلب</dt><dd class="font-mono"><?php echo e(number_format($referralProgram->minimum_order_amount, 2)); ?> <?php echo e(__('public.currency')); ?></dd></div>
                <?php endif; ?>
                <div class="flex justify-between gap-4"><dt class="text-slate-500">مدة صلاحية الخصم</dt><dd><?php echo e($referralProgram->discount_valid_days); ?> يوم</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-slate-500">الحد الأقصى لاستخدام الخصم</dt><dd><?php echo e($referralProgram->max_discount_uses_per_referred); ?> مرة</dd></div>
                <?php if($referralProgram->max_referrals_per_user): ?>
                <div class="flex justify-between gap-4"><dt class="text-slate-500">حد الإحالات لكل مستخدم</dt><dd><?php echo e($referralProgram->max_referrals_per_user); ?></dd></div>
                <?php endif; ?>
                <?php if($referralProgram->referrer_reward_value): ?>
                <div class="flex justify-between gap-4"><dt class="text-slate-500">مكافأة المحيل</dt><dd class="font-bold text-emerald-600">
                    <?php if($referralProgram->referrer_reward_type == 'percentage'): ?>
                        <?php echo e(number_format($referralProgram->referrer_reward_value, 0)); ?>%
                    <?php elseif($referralProgram->referrer_reward_type == 'points'): ?>
                        <?php echo e(number_format($referralProgram->referrer_reward_value, 0)); ?> نقطة
                    <?php else: ?>
                        <?php echo e(number_format($referralProgram->referrer_reward_value, 2)); ?> <?php echo e(__('public.currency')); ?>

                    <?php endif; ?>
                </dd></div>
                <?php endif; ?>
                <?php if($referralProgram->starts_at || $referralProgram->expires_at): ?>
                <div class="flex justify-between gap-4"><dt class="text-slate-500">الفترة</dt><dd class="text-xs">
                    <?php if($referralProgram->starts_at): ?> من <?php echo e($referralProgram->starts_at->format('Y-m-d')); ?> <?php endif; ?>
                    <?php if($referralProgram->expires_at): ?> إلى <?php echo e($referralProgram->expires_at->format('Y-m-d')); ?> <?php endif; ?>
                </dd></div>
                <?php endif; ?>
            </dl>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-bold text-slate-800 flex items-center gap-2"><i class="fas fa-list text-emerald-500"></i> آخر الإحالات</h2>
                <a href="<?php echo e(route('admin.referrals.index', ['program_id' => $referralProgram->id])); ?>" class="text-xs font-semibold text-emerald-600 hover:underline">عرض الكل</a>
            </div>
            <?php $recentReferrals = $referralProgram->referrals()->latest()->take(10)->get(); ?>
            <?php if($recentReferrals->count() > 0): ?>
            <div class="space-y-3 max-h-96 overflow-y-auto">
                <?php $__currentLoopData = $recentReferrals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $referral): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route('admin.referrals.show', $referral)); ?>" class="block p-4 rounded-xl border border-slate-200 bg-slate-50/50 hover:border-emerald-300 transition-colors">
                    <div class="flex justify-between items-start gap-2 mb-2">
                        <div>
                            <p class="font-medium text-slate-800"><?php echo e($referral->referred->name ?? 'غير معروف'); ?></p>
                            <p class="text-xs text-slate-500">محال من: <?php echo e($referral->referrer->name ?? 'غير معروف'); ?></p>
                        </div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium <?php echo e($referral->status == 'completed' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700'); ?>">
                            <?php echo e($referral->status == 'completed' ? 'مكتملة' : 'قيد الانتظار'); ?>

                        </span>
                    </div>
                    <div class="flex justify-between text-xs text-slate-500">
                        <span>خصم: <?php echo e(number_format($referral->discount_amount ?? 0, 2)); ?> <?php echo e(__('public.currency')); ?></span>
                        <span><?php echo e($referral->created_at->format('d/m/Y')); ?></span>
                    </div>
                </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php else: ?>
            <div class="text-center py-10 text-slate-500">
                <i class="fas fa-user-friends text-3xl mb-2 opacity-40"></i>
                <p class="text-sm">لا توجد إحالات لهذا البرنامج بعد</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sana\resources\views\admin\referral-programs\show.blade.php ENDPATH**/ ?>