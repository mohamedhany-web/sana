<?php $__env->startSection('title', __('parent.children')); ?>

<?php $__env->startSection('content'); ?>
<?php
    $totalCourses = 0;
    $progressSum = 0;
    $progressCount = 0;
    $childCards = $children->map(function ($child) use (&$totalCourses, &$progressSum, &$progressCount) {
        $enrollments = $child->courseEnrollments()->whereIn('status', ['active', 'completed'])->get();
        $prog = $enrollments->isEmpty() ? 0 : (int) round($enrollments->avg('progress') ?? 0);
        $totalCourses += $enrollments->count();
        if ($enrollments->isNotEmpty()) {
            $progressSum += $prog;
            $progressCount++;
        }
        $rel = $child->pivot->relation ?? 'guardian';

        return compact('child', 'enrollments', 'prog', 'rel');
    });
    $avgProgress = $progressCount > 0 ? (int) round($progressSum / $progressCount) : 0;
?>

<div class="par-page space-y-5 sm:space-y-6">
    <div class="par-hero par-card p-4 sm:p-6 lg:p-8">
        <h1 class="par-page-title font-heading">
            <i class="fas fa-users text-teal-600 ml-2"></i><?php echo e(__('parent.children_monitoring')); ?>

        </h1>
        <p class="par-page-lead"><?php echo e(__('parent.dashboard_subtitle')); ?></p>
    </div>

    <?php if($children->count() > 0): ?>
    <div class="par-kpi-grid par-kpi-grid--3">
        <div class="par-card p-4 sm:p-5">
            <div class="flex items-center gap-2 mb-2">
                <span class="w-9 h-9 rounded-lg bg-teal-50 text-teal-600 flex items-center justify-center"><i class="fas fa-child text-sm"></i></span>
                <span class="text-xs font-bold uppercase text-slate-600"><?php echo e(__('parent.children')); ?></span>
            </div>
            <div class="text-2xl font-black text-slate-900 tabular-nums"><?php echo e($children->count()); ?></div>
        </div>
        <div class="par-card p-4 sm:p-5">
            <div class="flex items-center gap-2 mb-2">
                <span class="w-9 h-9 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center"><i class="fas fa-chart-line text-sm"></i></span>
                <span class="text-xs font-bold uppercase text-slate-600"><?php echo e(__('parent.progress')); ?></span>
            </div>
            <div class="text-2xl font-black text-slate-900 tabular-nums"><?php echo e($avgProgress); ?>%</div>
        </div>
        <div class="par-card p-4 sm:p-5 par-kpi-span-2-sm">
            <div class="flex items-center gap-2 mb-2">
                <span class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center"><i class="fas fa-book-open text-sm"></i></span>
                <span class="text-xs font-bold uppercase text-slate-600"><?php echo e(__('parent.active_courses')); ?></span>
            </div>
            <div class="text-2xl font-black text-slate-900 tabular-nums"><?php echo e($totalCourses); ?></div>
        </div>
    </div>

    <div class="grid gap-3 sm:gap-4 md:grid-cols-2 xl:grid-cols-3">
        <?php $__currentLoopData = $childCards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
            $child = $item['child'];
            $prog = $item['prog'];
            $relLabel = __('parent.relation_'.$item['rel']);
        ?>
        <a href="<?php echo e(route('parent.children.show', $child)); ?>" class="par-card par-child-card">
            <div class="flex items-start gap-3">
                <span class="par-avatar w-12 h-12 text-lg rounded-xl"><?php echo e(mb_substr($child->name, 0, 1)); ?></span>
                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2 mb-1">
                        <p class="font-bold text-lg text-slate-900 truncate"><?php echo e($child->name); ?></p>
                        <span class="par-badge par-badge--teal"><?php echo e($relLabel); ?></span>
                    </div>
                    <p class="text-xs text-slate-500 truncate" dir="ltr"><?php echo e($child->email); ?></p>
                </div>
                <div class="text-end shrink-0">
                    <p class="text-xl font-black text-teal-600 tabular-nums"><?php echo e($prog); ?>%</p>
                    <p class="text-[10px] font-bold uppercase text-slate-400"><?php echo e(__('parent.progress')); ?></p>
                </div>
            </div>
            <div class="mt-4 flex items-center justify-between gap-3 text-xs text-slate-500">
                <span><i class="fas fa-book-open text-teal-500 ml-1"></i> <?php echo e($item['enrollments']->count()); ?> <?php echo e(__('parent.active_courses')); ?></span>
                <span class="font-semibold text-teal-700">عرض التفاصيل <i class="fas fa-arrow-left text-[10px] mr-1"></i></span>
            </div>
            <div class="par-progress mt-3"><span style="width: <?php echo e($prog); ?>%"></span></div>
        </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <?php else: ?>
    <div class="par-card par-empty">
        <div class="par-empty-icon"><i class="fas fa-child"></i></div>
        <p class="font-bold text-slate-800 text-base"><?php echo e(__('parent.no_children')); ?></p>
        <p class="text-sm mt-2 max-w-md mx-auto leading-relaxed"><?php echo e(__('parent.no_children_hint')); ?></p>
    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sana\resources\views\parent\children\index.blade.php ENDPATH**/ ?>