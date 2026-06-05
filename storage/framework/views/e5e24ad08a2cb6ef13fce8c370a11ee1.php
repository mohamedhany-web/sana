<?php
    $links = [
        ['route' => 'admin.tutor-lessons.index', 'label' => 'لوحة الرقابة', 'icon' => 'fa-chart-line'],
        ['route' => 'admin.tutor-lessons.bookings', 'label' => 'كل الحجوزات', 'icon' => 'fa-calendar-check'],
        ['route' => 'admin.tutor-lessons.instructors', 'label' => 'المعلمون', 'icon' => 'fa-chalkboard-teacher'],
        ['route' => 'admin.tutor-lessons.assisted.index', 'label' => 'طلبات المساعدة', 'icon' => 'fa-hands-helping'],
        ['route' => 'admin.tutor-lessons.settings', 'label' => 'إعدادات حصص الطلاب', 'icon' => 'fa-cog'],
    ];
?>
<nav class="flex flex-wrap gap-2 mb-6">
    <?php $__currentLoopData = $links; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e(route($link['route'])); ?>"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-bold border transition-colors <?php echo e(request()->routeIs($link['route']) || request()->routeIs($link['route'].'.*') ? 'bg-violet-600 text-white border-violet-600' : 'bg-white text-slate-700 border-slate-200 hover:border-violet-300'); ?>">
            <i class="fas <?php echo e($link['icon']); ?>"></i>
            <?php echo e($link['label']); ?>

        </a>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</nav>
<?php /**PATH C:\xampp\htdocs\sana\resources\views/admin/tutor-lessons/_nav.blade.php ENDPATH**/ ?>