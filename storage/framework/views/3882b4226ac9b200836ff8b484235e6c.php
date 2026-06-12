<?php $__env->startSection('title', 'تصنيفات دعم الطلاب'); ?>
<?php $__env->startSection('header', 'تصنيفات دعم الطلاب'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $statCards = [
        [
            'label' => 'إجمالي التصنيفات',
            'value' => number_format($stats['total'] ?? 0),
            'icon' => 'fa-tags',
            'bg' => 'bg-indigo-100',
            'text' => 'text-indigo-600',
            'desc' => 'كل التصنيفات المعرّفة',
        ],
        [
            'label' => 'نشطة للطلاب',
            'value' => number_format($stats['active'] ?? 0),
            'icon' => 'fa-eye',
            'bg' => 'bg-emerald-100',
            'text' => 'text-emerald-600',
            'desc' => 'تظهر عند فتح تذكرة',
        ],
        [
            'label' => 'معطّلة',
            'value' => number_format($stats['inactive'] ?? 0),
            'icon' => 'fa-eye-slash',
            'bg' => 'bg-slate-100',
            'text' => 'text-slate-500',
            'desc' => 'مخفية عن نموذج الطالب',
        ],
        [
            'label' => 'تذاكر نشطة',
            'value' => number_format($stats['active_tickets'] ?? 0),
            'icon' => 'fa-ticket-alt',
            'bg' => 'bg-sky-100',
            'text' => 'text-sky-600',
            'desc' => 'مفتوحة أو قيد المعالجة',
        ],
    ];
?>

<div class="space-y-6">
    <?php if(session('success')): ?>
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm font-medium flex items-center gap-2">
            <i class="fas fa-check-circle"></i>
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <?php if($errors->any()): ?>
        <div class="rounded-xl bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 text-sm">
            <p class="font-bold mb-1">يرجى تصحيح الأخطاء:</p>
            <ul class="list-disc pr-5 space-y-0.5">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-5 bg-gradient-to-l from-indigo-50 to-white border-b border-slate-200 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-600 to-violet-600 flex items-center justify-center text-white shadow-md shadow-indigo-500/25">
                    <i class="fas fa-tags text-lg"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-black text-slate-900">تصنيفات دعم الطلاب</h2>
                    <p class="text-sm text-slate-600 mt-0.5">يختار الطالب التصنيف المناسب عند فتح تذكرة — التصنيفات المعطّلة لا تظهر في لوحته</p>
                </div>
            </div>
            <a href="<?php echo e(route('admin.support-tickets.index')); ?>"
               class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-sky-700 bg-sky-50 border border-sky-200 rounded-xl hover:bg-sky-100 transition-colors shrink-0">
                <i class="fas fa-headset"></i>
                العودة لدعم الطلاب
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 p-6">
            <?php $__currentLoopData = $statCards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-xs font-semibold text-slate-600"><?php echo e($card['label']); ?></p>
                            <p class="text-2xl font-black text-slate-900 mt-1"><?php echo e($card['value']); ?></p>
                            <p class="text-[11px] text-slate-500 mt-1"><?php echo e($card['desc']); ?></p>
                        </div>
                        <div class="w-10 h-10 rounded-lg <?php echo e($card['bg']); ?> <?php echo e($card['text']); ?> flex items-center justify-center shrink-0">
                            <i class="fas <?php echo e($card['icon']); ?>"></i>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </section>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        
        <aside class="xl:col-span-1">
            <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden sticky top-4">
                <div class="px-5 py-4 border-b border-slate-200 bg-gradient-to-l from-emerald-50/80 to-white">
                    <h3 class="text-sm font-black text-slate-900 flex items-center gap-2">
                        <i class="fas fa-plus-circle text-emerald-600"></i>
                        تصنيف جديد
                    </h3>
                    <p class="text-xs text-slate-500 mt-1">أمثلة: مشكلة تقنية، حصص مع المعلم، الاشتراك والدفع</p>
                </div>
                <form method="POST" action="<?php echo e(route('admin.support-inquiry-categories.store')); ?>" class="p-5 space-y-4" data-turbo="false">
                    <?php echo csrf_field(); ?>
                    <div>
                        <label for="new_cat_name" class="block text-xs font-bold text-slate-700 mb-1.5">اسم التصنيف</label>
                        <input type="text" id="new_cat_name" name="name" value="<?php echo e(old('name')); ?>" required maxlength="120"
                               placeholder="مثال: مشكلة في الحصص المباشرة"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-rose-600 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div>
                        <label for="new_cat_sort" class="block text-xs font-bold text-slate-700 mb-1.5">ترتيب العرض</label>
                        <input type="number" id="new_cat_sort" name="sort_order" value="<?php echo e(old('sort_order', 0)); ?>" min="0" max="9999"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-indigo-500">
                        <p class="text-[11px] text-slate-500 mt-1">الأصغر يظهر أولاً في قائمة الطالب</p>
                    </div>
                    <div class="rounded-xl border border-slate-200 bg-slate-50/60 px-4 py-3 flex items-center justify-between gap-3">
                        <div>
                            <p class="text-sm font-bold text-slate-800">نشط للطلاب</p>
                            <p class="text-[11px] text-slate-500">يظهر في نموذج إنشاء التذكرة</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer shrink-0">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" checked class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-200 rounded-full peer peer-checked:bg-emerald-500 peer-focus:ring-2 peer-focus:ring-emerald-400 after:content-[''] after:absolute after:top-0.5 after:right-[2px] after:bg-white after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:-translate-x-5"></div>
                        </label>
                    </div>
                    <button type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-gradient-to-r from-indigo-600 to-violet-600 text-white text-sm font-bold shadow-md hover:from-indigo-700 hover:to-violet-700 transition-all">
                        <i class="fas fa-plus"></i>
                        إضافة التصنيف
                    </button>
                </form>
            </section>

            <section class="rounded-2xl border border-indigo-200 bg-indigo-50/50 p-5 mt-4 shadow-sm">
                <h3 class="text-sm font-black text-indigo-900 mb-2 flex items-center gap-2">
                    <i class="fas fa-info-circle"></i>
                    ملاحظة
                </h3>
                <ul class="text-xs text-indigo-900/90 space-y-2 leading-relaxed">
                    <li class="flex gap-2"><span class="text-indigo-500">•</span> لا تحذف تصنيفاً عليه تذاكر نشطة إلا بعد نقلها أو إغلاقها.</li>
                    <li class="flex gap-2"><span class="text-indigo-500">•</span> التعطيل يخفي التصنيف عن الطلاب الجدد دون حذف التذاكر القديمة.</li>
                </ul>
            </section>
        </aside>

        
        <section class="xl:col-span-2 rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200 bg-slate-50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div>
                    <h3 class="text-base font-black text-slate-900">التصنيفات الحالية</h3>
                    <p class="text-xs text-slate-500 mt-0.5">عدّل الاسم أو الترتيب ثم اضغط حفظ</p>
                </div>
                <span class="text-xs font-semibold text-slate-600 bg-white border border-slate-200 px-3 py-1 rounded-full">
                    <?php echo e($categories->count()); ?> تصنيف
                </span>
            </div>

            <?php $__empty_1 = true; $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="border-b border-slate-100 last:border-b-0 <?php echo e($cat->is_active ? '' : 'bg-slate-50/80'); ?>">
                    <div class="p-5">
                        <div class="flex flex-wrap items-start justify-between gap-3 mb-4">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 <?php echo e($cat->is_active ? 'bg-indigo-100 text-indigo-600' : 'bg-slate-200 text-slate-500'); ?>">
                                    <i class="fas fa-folder-open"></i>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-base font-black text-slate-900 truncate"><?php echo e($cat->name); ?></p>
                                    <div class="flex flex-wrap items-center gap-2 mt-1">
                                        <?php if($cat->is_active): ?>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">نشط</span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-200 text-slate-600">معطّل</span>
                                        <?php endif; ?>
                                        <span class="text-[11px] text-slate-500">ترتيب: <?php echo e($cat->sort_order); ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-wrap gap-2 text-xs">
                                <?php if(($cat->active_tickets_count ?? 0) > 0): ?>
                                    <a href="<?php echo e(route('admin.support-tickets.index', ['category_id' => $cat->id, 'view' => 'active'])); ?>"
                                       class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-amber-50 text-amber-800 border border-amber-200 font-bold hover:bg-amber-100">
                                        <i class="fas fa-clock"></i>
                                        <?php echo e($cat->active_tickets_count); ?> نشطة
                                    </a>
                                <?php endif; ?>
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 font-semibold">
                                    <i class="fas fa-ticket-alt text-slate-400"></i>
                                    <?php echo e($cat->tickets_count ?? 0); ?> إجمالي
                                </span>
                            </div>
                        </div>

                        <form method="POST" action="<?php echo e(route('admin.support-inquiry-categories.update', $cat)); ?>"
                              class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end" data-turbo="false">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PUT'); ?>
                            <div class="sm:col-span-6">
                                <label class="block text-[11px] font-semibold text-slate-500 mb-1">الاسم</label>
                                <input type="text" name="name" value="<?php echo e(old('name', $cat->name)); ?>" required maxlength="120"
                                       class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-indigo-500">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-[11px] font-semibold text-slate-500 mb-1">ترتيب</label>
                                <input type="number" name="sort_order" value="<?php echo e(old('sort_order', $cat->sort_order)); ?>" min="0"
                                       class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-indigo-500">
                            </div>
                            <div class="sm:col-span-2 flex items-center pb-2">
                                <label class="inline-flex items-center gap-2 text-sm text-slate-700 cursor-pointer">
                                    <input type="hidden" name="is_active" value="0">
                                    <input type="checkbox" name="is_active" value="1" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" <?php echo e($cat->is_active ? 'checked' : ''); ?>>
                                    <span class="text-xs font-semibold">نشط</span>
                                </label>
                            </div>
                            <div class="sm:col-span-2">
                                <button type="submit" class="w-full px-3 py-2 rounded-xl bg-slate-800 text-white text-sm font-bold hover:bg-slate-900 transition-colors">
                                    حفظ
                                </button>
                            </div>
                        </form>

                        <div class="mt-3 pt-3 border-t border-slate-100 flex justify-end">
                            <form method="POST" action="<?php echo e(route('admin.support-inquiry-categories.destroy', $cat)); ?>" data-turbo="false"
                                  onsubmit="return confirm('حذف «<?php echo e(addslashes($cat->name)); ?>»؟ التذاكر المرتبطة ستبقى دون تصنيف.');">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit"
                                        class="inline-flex items-center gap-1 text-xs font-bold text-rose-600 hover:text-rose-800 disabled:opacity-40"
                                        <?php if(($cat->active_tickets_count ?? 0) > 0): ?> disabled title="أغلق التذاكر النشطة أولاً" <?php endif; ?>>
                                    <i class="fas fa-trash-alt"></i>
                                    حذف التصنيف
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="px-6 py-16 text-center">
                    <div class="w-16 h-16 rounded-2xl bg-indigo-100 text-indigo-500 flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-tags text-2xl"></i>
                    </div>
                    <p class="text-sm font-bold text-slate-800">لا توجد تصنيفات بعد</p>
                    <p class="text-xs text-slate-500 mt-2 max-w-xs mx-auto">أضف أول تصنيف من النموذج على اليسار حتى يتمكن الطلاب من فتح تذاكر الدعم.</p>
                </div>
            <?php endif; ?>
        </section>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sana\resources\views\admin\support-inquiry-categories\index.blade.php ENDPATH**/ ?>