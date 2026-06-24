<?php $__env->startSection('title', 'إضافة مرحلة دراسية'); ?>
<?php $__env->startSection('header', 'إضافة مرحلة دراسية'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full max-w-3xl mx-auto px-4 py-6 space-y-6">
    <div class="bg-gradient-to-l from-[#1D4EDB] via-indigo-600 to-violet-600 rounded-2xl p-6 text-white shadow-lg">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <nav class="text-sm text-white/80 mb-2">
                    <a href="<?php echo e(route('admin.dashboard')); ?>" class="hover:text-white">لوحة التحكم</a>
                    <span class="mx-2">/</span>
                    <a href="<?php echo e(route('admin.academic-years.index')); ?>" class="hover:text-white"><?php echo e(__('admin.academic_years')); ?></a>
                    <span class="mx-2">/</span>
                    <span>إضافة</span>
                </nav>
                <h1 class="text-xl font-bold">إضافة مرحلة دراسية</h1>
                <p class="text-sm text-white/90 mt-1">مثال: أول ابتدائي، ثالث متوسط، ثالث ثانوي علمي.</p>
            </div>
            <a href="<?php echo e(route('admin.academic-years.index')); ?>" class="inline-flex items-center gap-2 bg-white/15 hover:bg-white/25 text-white px-4 py-2 rounded-xl text-sm font-medium border border-white/25">
                <i class="fas fa-arrow-right"></i> رجوع
            </a>
        </div>
    </div>

    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100">
            <h2 class="font-bold text-slate-900">بيانات المرحلة</h2>
        </div>
        <form method="POST" action="<?php echo e(route('admin.academic-years.store')); ?>" enctype="multipart/form-data" class="p-6 space-y-6">
            <?php echo csrf_field(); ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="name" class="block text-sm font-semibold text-slate-700 mb-1">اسم المرحلة <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" id="name" value="<?php echo e(old('name')); ?>" required
                           class="w-full rounded-xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500/20"
                           placeholder="مثال: الثالث الثانوي">
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
                    <label for="code" class="block text-sm font-semibold text-slate-700 mb-1">الرمز <span class="text-rose-500">*</span></label>
                    <input type="text" name="code" id="code" value="<?php echo e(old('code')); ?>" required
                           class="w-full rounded-xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500/20"
                           placeholder="مثال: G12">
                    <?php $__errorArgs = ['code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-rose-600 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-semibold text-slate-700 mb-1">الوصف</label>
                    <textarea name="description" id="description" rows="3"
                              class="w-full rounded-xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500/20"
                              placeholder="وصف مختصر للمرحلة (اختياري)"><?php echo e(old('description')); ?></textarea>
                    <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-rose-600 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label for="icon" class="block text-sm font-semibold text-slate-700 mb-1">الأيقونة</label>
                    <select name="icon" id="icon" class="w-full rounded-xl border-slate-200 text-sm">
                        <?php $__currentLoopData = [
                            'fas fa-layer-group' => 'مرحلة دراسية',
                            'fas fa-child' => 'ابتدائي',
                            'fas fa-user-graduate' => 'ثانوي',
                            'fas fa-book-open' => 'تعليم عام',
                            'fas fa-school' => 'مدرسة',
                        ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($value); ?>" <?php if(old('icon', 'fas fa-layer-group') === $value): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label for="color" class="block text-sm font-semibold text-slate-700 mb-1">اللون</label>
                    <input type="color" name="color" id="color" value="<?php echo e(old('color', '#1D4EDB')); ?>"
                           class="w-full h-11 rounded-xl border border-slate-200 cursor-pointer">
                </div>
                <div>
                    <label for="order" class="block text-sm font-semibold text-slate-700 mb-1">ترتيب الظهور</label>
                    <input type="number" name="order" id="order" value="<?php echo e(old('order', 0)); ?>" min="0"
                           class="w-full rounded-xl border-slate-200 text-sm">
                </div>
                <div>
                    <label for="price" class="block text-sm font-semibold text-slate-700 mb-1">السعر (<?php echo e(__('public.currency')); ?>)</label>
                    <input type="number" name="price" id="price" value="<?php echo e(old('price', 0)); ?>" min="0" step="0.01"
                           class="w-full rounded-xl border-slate-200 text-sm">
                    <p class="text-xs text-slate-500 mt-1">0 = مجاني</p>
                </div>
                <div class="md:col-span-2">
                    <label for="thumbnail" class="block text-sm font-semibold text-slate-700 mb-1">صورة (اختياري)</label>
                    <input type="file" name="thumbnail" id="thumbnail" accept="image/*"
                           class="w-full rounded-xl border-slate-200 text-sm">
                </div>
            </div>

            <label class="flex items-start gap-3 p-4 rounded-xl bg-slate-50 border border-slate-200 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" class="mt-1 rounded border-slate-300 text-indigo-600" <?php if(old('is_active', true)): echo 'checked'; endif; ?>>
                <span>
                    <span class="block text-sm font-semibold text-slate-800">مرحلة نشطة</span>
                    <span class="block text-xs text-slate-500 mt-0.5">المراحل النشطة فقط تظهر للطلاب ويمكن إضافة مواد تحتها.</span>
                </span>
            </label>

            <div class="flex flex-wrap gap-3 pt-2 border-t border-slate-100">
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-indigo-600 text-white text-sm font-bold hover:bg-indigo-700">
                    <i class="fas fa-save"></i> حفظ المرحلة
                </button>
                <a href="<?php echo e(route('admin.academic-years.index')); ?>" class="inline-flex items-center px-5 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50">إلغاء</a>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sana\resources\views\admin\academic-years\create.blade.php ENDPATH**/ ?>