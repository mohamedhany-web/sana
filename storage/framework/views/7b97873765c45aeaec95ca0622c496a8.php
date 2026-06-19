
<?php $__env->startSection('title', 'الفيديوهات الدعائية - الصفحة الرئيسية'); ?>
<?php $__env->startSection('header', 'الفيديوهات الدعائية (الصفحة الرئيسية)'); ?>
<?php $__env->startSection('content'); ?>
<div class="w-full space-y-6">
    <div class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 border-b border-slate-200 flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">الفيديوهات الدعائية</h1>
                <p class="text-slate-500 mt-1">أضف روابط YouTube فقط — تظهر في أسفل الصفحة الرئيسية بعرض أفقي متجاوب.</p>
            </div>
            <a href="<?php echo e(route('admin.promotional-videos.create')); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-sky-500 to-blue-600 hover:from-sky-600 hover:to-blue-700 text-white rounded-xl font-semibold shadow-lg shadow-sky-500/30 transition-all">
                <i class="fas fa-plus"></i>
                <span>فيديو جديد</span>
            </a>
        </div>
        <div class="p-5 sm:p-8">
            <?php if(session('success')): ?>
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800"><?php echo e(session('success')); ?></div>
            <?php endif; ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-right text-sm">
                    <thead class="bg-slate-50">
                        <tr class="text-xs font-semibold uppercase text-slate-500">
                            <th class="px-4 py-3">الفيديو</th>
                            <th class="px-4 py-3">العنوان</th>
                            <th class="px-4 py-3">الترتيب</th>
                            <th class="px-4 py-3">الحالة</th>
                            <th class="px-4 py-3">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        <?php $__empty_1 = true; $__currentLoopData = $videos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $video): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3">
                                <?php if($video->thumbnailUrl()): ?>
                                    <img src="<?php echo e($video->thumbnailUrl()); ?>" alt="" class="w-28 h-16 object-cover rounded-lg border border-slate-200">
                                <?php else: ?>
                                    <span class="inline-flex w-28 h-16 items-center justify-center rounded-lg bg-slate-100 text-slate-400"><i class="fab fa-youtube text-2xl"></i></span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3">
                                <p class="font-medium text-slate-800"><?php echo e($video->title); ?></p>
                                <?php if($video->description): ?>
                                    <p class="text-xs text-slate-500 mt-1 max-w-xs truncate"><?php echo e($video->description); ?></p>
                                <?php endif; ?>
                                <a href="<?php echo e($video->youtube_url); ?>" target="_blank" rel="noopener" class="text-xs text-sky-600 hover:underline mt-1 inline-block">عرض على YouTube</a>
                            </td>
                            <td class="px-4 py-3 text-slate-600"><?php echo e($video->sort_order); ?></td>
                            <td class="px-4 py-3">
                                <?php if($video->is_active): ?>
                                    <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold bg-emerald-100 text-emerald-700"><i class="fas fa-eye"></i> نشط</span>
                                <?php else: ?>
                                    <span class="rounded-full px-2.5 py-0.5 text-xs font-semibold bg-slate-100 text-slate-600">معطل</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <a href="<?php echo e(route('admin.promotional-videos.edit', $video)); ?>" class="text-sky-600 hover:text-sky-700 font-medium ml-2">تعديل</a>
                                <form action="<?php echo e(route('admin.promotional-videos.destroy', $video)); ?>" method="POST" class="inline" onsubmit="return confirm('حذف هذا الفيديو؟');">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="text-rose-600 hover:text-rose-700 font-medium">حذف</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center text-slate-500">
                                <i class="fab fa-youtube text-4xl text-slate-300 mb-3 block"></i>
                                <p>لا توجد فيديوهات. <a href="<?php echo e(route('admin.promotional-videos.create')); ?>" class="text-sky-600 hover:underline">أضف فيديواً جديداً</a></p>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="mt-4"><?php echo e($videos->links()); ?></div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sana\resources\views/admin/marketing/promotional-videos/index.blade.php ENDPATH**/ ?>