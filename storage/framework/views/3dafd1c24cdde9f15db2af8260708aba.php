<?php $__env->startSection('title', $label . ' - ميزة من باقتك'); ?>
<?php $__env->startSection('header', $label); ?>

<?php $__env->startSection('content'); ?>
<?php
    $cfg = $featureConfig ?? [];
    $icon = $cfg['icon'] ?? 'fa-star';
    $iconBg = $cfg['icon_bg'] ?? 'bg-sky-100';
    $iconText = $cfg['icon_text'] ?? 'text-sky-600';
?>
<div class="w-full px-4 sm:px-6 lg:px-8 py-6 space-y-6">
    <div class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="bg-gradient-to-l from-slate-50 via-white to-white p-6 border-b border-slate-100">
            <div class="flex flex-wrap items-center gap-3 mb-2">
                <span class="w-12 h-12 rounded-xl <?php echo e($iconBg); ?> <?php echo e($iconText); ?> flex items-center justify-center">
                    <i class="fas <?php echo e($icon); ?> text-lg"></i>
                </span>
                <div class="flex-1 min-w-0">
                    <h1 class="text-xl sm:text-2xl font-black text-slate-800"><?php echo e($label); ?></h1>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg bg-amber-100 text-amber-800 text-xs font-semibold border border-amber-200">
                        ميزة من باقتك
                    </span>
                </div>
            </div>
            <p class="text-sm text-slate-600 mt-2"><?php echo e($description); ?></p>
        </div>
        <div class="p-6">
            <div class="rounded-xl border border-dashed border-slate-200 bg-slate-50/50 p-8 text-center">
                <p class="text-slate-600 mb-4">هذه الصفحة مخصصة لهذه الميزة. سيتم إثراؤها بمحتوى تفاعلي لاحقاً حسب نوع الخدمة.</p>
                <a href="<?php echo e(route('student.my-subscription')); ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-sky-600 text-white text-sm font-semibold hover:bg-sky-700 transition-colors">
                    <i class="fas fa-layer-group"></i>
                    عرض تفاصيل اشتراكي
                </a>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sana\resources\views\student\features\show.blade.php ENDPATH**/ ?>