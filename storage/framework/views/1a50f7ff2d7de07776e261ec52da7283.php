<?php $__env->startSection('title', __('admin.courses_management')); ?>
<?php $__env->startSection('header', __('admin.courses_management')); ?>

<?php $__env->startSection('content'); ?>
<?php
    $stats = $stats ?? [];
    $statCards = [
        ['label' => 'إجمالي الكورسات', 'value' => $stats['total'] ?? 0, 'icon' => 'fa-book', 'bg' => 'bg-indigo-100', 'text' => 'text-indigo-600', 'desc' => 'كل الكورسات في المنصة'],
        ['label' => 'نشطة', 'value' => $stats['active'] ?? 0, 'icon' => 'fa-check-circle', 'bg' => 'bg-emerald-100', 'text' => 'text-emerald-600', 'desc' => 'متاحة للطلاب'],
        ['label' => 'معطّلة', 'value' => $stats['inactive'] ?? 0, 'icon' => 'fa-pause-circle', 'bg' => 'bg-slate-100', 'text' => 'text-slate-600', 'desc' => 'غير منشورة'],
        ['label' => 'مجانية', 'value' => $stats['free'] ?? 0, 'icon' => 'fa-gift', 'bg' => 'bg-teal-100', 'text' => 'text-teal-600', 'desc' => 'بدون سعر شراء'],
    ];
?>

<div class="space-y-6">
    <?php if(session('success')): ?>
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm font-medium flex items-center gap-2">
            <i class="fas fa-check-circle"></i><?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="rounded-xl bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 text-sm font-medium flex items-center gap-2">
            <i class="fas fa-times-circle"></i><?php echo e(session('error')); ?>

        </div>
    <?php endif; ?>

    
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-5 bg-gradient-to-l from-indigo-50 to-white border-b border-slate-200 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-600 to-violet-600 flex items-center justify-center text-white shadow-md shadow-indigo-500/25">
                    <i class="fas fa-graduation-cap text-lg"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-black text-slate-900"><?php echo e(__('admin.courses_management')); ?></h2>
                    <p class="text-sm text-slate-600 mt-0.5">إنشاء الكورسات، الدروس، الأسعار، والتسجيلات</p>
                </div>
            </div>
            <a href="<?php echo e(route('admin.advanced-courses.create')); ?>"
               class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-bold text-white bg-gradient-to-r from-indigo-600 to-violet-600 rounded-xl shadow hover:from-indigo-700 hover:to-violet-700 transition-all">
                <i class="fas fa-plus"></i>
                إضافة كورس جديد
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 p-6">
            <?php $__currentLoopData = $statCards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-xs font-semibold text-slate-600"><?php echo e($card['label']); ?></p>
                            <p class="text-2xl font-black text-slate-900 mt-1"><?php echo e(number_format($card['value'])); ?></p>
                            <p class="text-[11px] text-slate-500 mt-1"><?php echo e($card['desc']); ?></p>
                        </div>
                        <div class="w-11 h-11 rounded-lg <?php echo e($card['bg']); ?> flex items-center justify-center <?php echo e($card['text']); ?> shrink-0">
                            <i class="fas <?php echo e($card['icon']); ?>"></i>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <?php if(($stats['featured'] ?? 0) > 0): ?>
        <div class="px-6 pb-4">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-amber-100 text-amber-800 border border-amber-200 text-xs font-semibold">
                <i class="fas fa-star"></i>
                مميّزة في الواجهة: <?php echo e(number_format($stats['featured'])); ?>

            </span>
        </div>
        <?php endif; ?>
    </section>

    
    <section class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/80 flex flex-wrap items-center justify-between gap-2">
            <h3 class="text-sm font-bold text-slate-900">بحث وتصفية</h3>
            <span class="text-xs text-slate-500">النتائج: <strong class="text-indigo-600"><?php echo e($courses->total()); ?></strong></span>
        </div>
        <form method="GET" action="<?php echo e(route('admin.advanced-courses.index')); ?>" class="p-5 grid grid-cols-1 md:grid-cols-12 gap-4">
            <div class="md:col-span-4">
                <label for="search" class="block text-xs font-semibold text-slate-600 mb-1.5">بحث</label>
                <div class="relative">
                    <span class="absolute inset-y-0 right-3 flex items-center text-slate-400 pointer-events-none">
                        <i class="fas fa-search text-sm"></i>
                    </span>
                    <input type="text" name="search" id="search" value="<?php echo e(request('search')); ?>"
                           placeholder="عنوان أو وصف الكورس"
                           class="w-full rounded-xl border border-slate-300 bg-white pr-10 pl-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>
            <div class="md:col-span-3">
                <label for="course_category_id" class="block text-xs font-semibold text-slate-600 mb-1.5">مسار الكورس</label>
                <select name="course_category_id" id="course_category_id"
                        class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500">
                    <option value="">جميع المسارات</option>
                    <?php $__currentLoopData = $courseCategoryOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($cc->id); ?>" <?php if((string) request('course_category_id') === (string) $cc->id): echo 'selected'; endif; ?>><?php echo e($cc->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="md:col-span-3">
                <label for="status" class="block text-xs font-semibold text-slate-600 mb-1.5">الحالة</label>
                <select name="status" id="status"
                        class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500">
                    <option value="">الكل</option>
                    <option value="active" <?php if(request('status') === 'active'): echo 'selected'; endif; ?>>نشط</option>
                    <option value="inactive" <?php if(request('status') === 'inactive'): echo 'selected'; endif; ?>>معطّل</option>
                </select>
            </div>
            <div class="md:col-span-2 flex items-end gap-2">
                <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-xl text-sm font-bold">
                    <i class="fas fa-filter"></i> تطبيق
                </button>
                <?php if(request()->anyFilled(['search', 'status', 'course_category_id'])): ?>
                <a href="<?php echo e(route('admin.advanced-courses.index')); ?>" class="px-3 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50" title="مسح">
                    <i class="fas fa-times"></i>
                </a>
                <?php endif; ?>
            </div>
        </form>
    </section>

    <?php if(Route::has('admin.online-enrollments.index')): ?>
    <div class="flex flex-wrap gap-2">
        <a href="<?php echo e(route('admin.online-enrollments.index')); ?>"
           class="inline-flex items-center gap-2 px-3 py-2 rounded-xl text-sm font-semibold border border-slate-200 text-slate-700 hover:border-indigo-300 hover:bg-indigo-50 transition-colors">
            <i class="fas fa-laptop text-indigo-600"></i> التسجيلات الأونلاين
        </a>
    </div>
    <?php endif; ?>

    <?php if($courses->count() > 0): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
            <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <article class="rounded-2xl bg-white border border-slate-200 shadow-sm hover:shadow-md transition-shadow flex flex-col overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100 flex items-start gap-3">
                        <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center text-white shrink-0 shadow-sm">
                            <i class="fas fa-book-open text-sm"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h3 class="font-bold text-slate-900 leading-snug line-clamp-2"><?php echo e($course->title); ?></h3>
                            <div class="flex flex-wrap gap-1.5 mt-2">
                                <span class="inline-flex px-2 py-0.5 rounded-lg text-[11px] font-semibold <?php echo e($course->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600'); ?>">
                                    <?php echo e($course->is_active ? 'نشط' : 'معطّل'); ?>

                                </span>
                                <?php if($course->is_featured): ?>
                                    <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded-lg text-[11px] font-semibold bg-amber-100 text-amber-800">
                                        <i class="fas fa-star text-[9px]"></i> مميّز
                                    </span>
                                <?php endif; ?>
                                <?php if($course->is_free): ?>
                                    <span class="inline-flex px-2 py-0.5 rounded-lg text-[11px] font-semibold bg-teal-100 text-teal-800">مجاني</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="px-5 py-4 flex-1 space-y-2 text-sm">
                        <?php if($course->description): ?>
                            <p class="text-slate-600 text-xs line-clamp-2"><?php echo e(Str::limit($course->description, 100)); ?></p>
                        <?php endif; ?>
                        <p class="flex items-center gap-2 text-slate-700">
                            <i class="fas fa-chalkboard-teacher text-slate-400 w-4 text-center text-xs"></i>
                            <span class="truncate"><?php echo e($course->instructor?->name ?? '—'); ?></span>
                        </p>
                        <?php if($course->category): ?>
                            <p class="flex items-center gap-2 text-slate-600 text-xs">
                                <i class="fas fa-folder text-slate-400 w-4 text-center"></i>
                                <span><?php echo e($course->category); ?></span>
                            </p>
                        <?php endif; ?>
                        <p class="flex items-center gap-2 font-semibold text-slate-800 tabular-nums">
                            <?php if(!$course->is_free && $course->effectivePurchasePrice() > 0): ?>
                                <?php if($course->hasPromotionalPrice()): ?>
                                    <span class="text-xs text-slate-400 line-through font-normal"><?php echo e(number_format($course->listPriceAmount())); ?></span>
                                <?php endif; ?>
                                <span><?php echo e(number_format($course->effectivePurchasePrice())); ?> <?php echo e(__('public.currency')); ?></span>
                            <?php else: ?>
                                <span class="text-teal-600">مجاني</span>
                            <?php endif; ?>
                        </p>
                    </div>

                    <div class="px-5 py-3 grid grid-cols-3 gap-2 border-t border-slate-100 bg-slate-50/60">
                        <div class="text-center rounded-lg bg-white border border-slate-100 py-2">
                            <p class="text-lg font-black text-slate-900"><?php echo e($course->lessons_count ?? 0); ?></p>
                            <p class="text-[10px] text-slate-500 font-semibold">درس</p>
                        </div>
                        <div class="text-center rounded-lg bg-white border border-slate-100 py-2">
                            <p class="text-lg font-black text-slate-900"><?php echo e($course->enrollments_count ?? 0); ?></p>
                            <p class="text-[10px] text-slate-500 font-semibold">مسجّل</p>
                        </div>
                        <div class="text-center rounded-lg bg-white border border-slate-100 py-2">
                            <p class="text-lg font-black text-slate-900"><?php echo e($course->orders_count ?? 0); ?></p>
                            <p class="text-[10px] text-slate-500 font-semibold">طلب</p>
                        </div>
                    </div>

                    <div class="px-5 py-4 border-t border-slate-100 space-y-3">
                        <div class="flex flex-wrap gap-1.5">
                            <a href="<?php echo e(route('admin.advanced-courses.show', $course)); ?>"
                               class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200" title="عرض">
                                <i class="fas fa-eye text-xs"></i>
                            </a>
                            <a href="<?php echo e(route('admin.courses.lessons.index', $course)); ?>"
                               class="w-8 h-8 flex items-center justify-center rounded-lg bg-emerald-100 text-emerald-700 hover:bg-emerald-200" title="الدروس">
                                <i class="fas fa-play-circle text-xs"></i>
                            </a>
                            <a href="<?php echo e(route('admin.courses.lessons.create', $course)); ?>"
                               class="w-8 h-8 flex items-center justify-center rounded-lg bg-teal-100 text-teal-700 hover:bg-teal-200" title="إضافة درس">
                                <i class="fas fa-plus text-xs"></i>
                            </a>
                            <a href="<?php echo e(route('admin.advanced-courses.orders', $course)); ?>"
                               class="w-8 h-8 flex items-center justify-center rounded-lg bg-amber-100 text-amber-700 hover:bg-amber-200" title="الطلبات">
                                <i class="fas fa-shopping-cart text-xs"></i>
                            </a>
                            <a href="<?php echo e(route('admin.advanced-courses.edit', $course)); ?>"
                               class="w-8 h-8 flex items-center justify-center rounded-lg bg-indigo-100 text-indigo-700 hover:bg-indigo-200" title="تعديل">
                                <i class="fas fa-edit text-xs"></i>
                            </a>
                            <button type="button" onclick="toggleCourseStatus(<?php echo e($course->id); ?>)"
                                    class="w-8 h-8 flex items-center justify-center rounded-lg <?php echo e($course->is_active ? 'bg-rose-100 text-rose-700 hover:bg-rose-200' : 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200'); ?>"
                                    title="<?php echo e($course->is_active ? 'إيقاف' : 'تفعيل'); ?>">
                                <i class="fas <?php echo e($course->is_active ? 'fa-pause' : 'fa-play'); ?> text-xs"></i>
                            </button>
                            <button type="button" onclick="toggleCourseFeatured(<?php echo e($course->id); ?>)"
                                    class="w-8 h-8 flex items-center justify-center rounded-lg <?php echo e($course->is_featured ? 'bg-amber-200 text-amber-800' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'); ?>"
                                    title="<?php echo e($course->is_featured ? 'إلغاء الترشيح' : 'ترشيح'); ?>">
                                <i class="fas fa-star text-xs"></i>
                            </button>
                            <form method="POST" action="<?php echo e(route('admin.advanced-courses.destroy', $course)); ?>" class="inline"
                                  onsubmit="return confirm('حذف هذا الكورس؟');">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit"
                                        class="w-8 h-8 flex items-center justify-center rounded-lg bg-rose-100 text-rose-700 hover:bg-rose-200" title="حذف">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </form>
                        </div>
                        <p class="text-[11px] text-slate-400"><?php echo e($course->created_at->format('Y-m-d')); ?></p>
                    </div>
                </article>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <?php if($courses->hasPages()): ?>
            <div class="rounded-2xl bg-white border border-slate-200 px-5 py-4">
                <?php echo e($courses->links()); ?>

            </div>
        <?php endif; ?>
    <?php else: ?>
        <section class="rounded-2xl bg-white border border-slate-200 shadow-sm px-6 py-16 text-center">
            <div class="w-14 h-14 mx-auto rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center mb-3">
                <i class="fas fa-graduation-cap text-2xl"></i>
            </div>
            <p class="font-bold text-slate-800">لا توجد كورسات</p>
            <p class="text-sm text-slate-500 mt-1">غيّر الفلاتر أو أضف أول كورس</p>
            <a href="<?php echo e(route('admin.advanced-courses.create')); ?>"
               class="inline-flex items-center gap-2 mt-4 px-4 py-2 text-sm font-bold text-white bg-indigo-600 rounded-xl hover:bg-indigo-700">
                <i class="fas fa-plus"></i> إضافة كورس
            </a>
        </section>
    <?php endif; ?>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function toggleCourseStatus(courseId) {
    if (!confirm('تغيير حالة هذا الكورس؟')) return;
    fetch(`/admin/advanced-courses/${courseId}/toggle-status`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>', 'Accept': 'application/json' },
    })
    .then(r => r.json())
    .then(data => { data.success ? location.reload() : alert('تعذّر تغيير الحالة'); })
    .catch(() => alert('تعذّر الاتصال بالخادم'));
}

function toggleCourseFeatured(courseId) {
    if (!confirm('تغيير حالة الترشيح؟')) return;
    fetch(`/admin/advanced-courses/${courseId}/toggle-featured`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>', 'Accept': 'application/json' },
    })
    .then(r => r.json())
    .then(data => { data.success ? location.reload() : alert('تعذّر تغيير الترشيح'); })
    .catch(() => alert('تعذّر الاتصال بالخادم'));
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sana\resources\views/admin/advanced-courses/index.blade.php ENDPATH**/ ?>