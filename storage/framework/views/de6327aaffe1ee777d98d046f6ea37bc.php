<?php if(Route::has('admin.tutor-lessons.index')): ?>
<nav class="flex flex-wrap gap-2 mb-6 p-4 rounded-2xl bg-slate-50 border border-slate-200">
    <span class="text-xs font-bold text-slate-600 w-full mb-1">مرتبط بنظام المنصة الحالي</span>
    <a href="<?php echo e(route('admin.tutor-lessons.index')); ?>" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl text-sm font-bold border border-violet-200 bg-white text-violet-800 hover:bg-violet-50">
        <i class="fas fa-user-clock"></i> رقابة حصص المعلمين
    </a>
    <a href="<?php echo e(route('admin.tutor-lessons.settings')); ?>" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl text-sm font-bold border border-slate-200 bg-white text-slate-700 hover:border-violet-300">
        <i class="fas fa-cog"></i> إعدادات ساعات الطلاب
    </a>
</nav>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\sana\resources\views/admin/subscriptions/_subscriptions-admin-nav.blade.php ENDPATH**/ ?>