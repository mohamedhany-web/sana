

<?php $__env->startSection('title', 'منشئ نموذج التوظيف - ' . config('app.name')); ?>
<?php $__env->startSection('header', 'منشئ نموذج توظيف المعلمين'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $typeOptions = $typeOptions ?? [];
    $optionSources = [
        'specializations' => 'التخصصات (من الإعدادات)',
        'curricula' => 'المناهج',
        'stages' => 'المراحل',
        'lesson_formats' => 'أنواع الحصص',
        'tech_skills' => 'المهارات التقنية',
        'commitments' => 'بنود الالتزام',
        'weekdays' => 'أيام الأسبوع',
    ];
?>

<div class="space-y-6">
    <?php $__currentLoopData = ['success', 'error', 'info']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $flash): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if(session($flash)): ?>
            <div class="rounded-xl px-4 py-3 text-sm border
                <?php echo e($flash === 'success' ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : ''); ?>

                <?php echo e($flash === 'error' ? 'bg-rose-50 border-rose-200 text-rose-800' : ''); ?>

                <?php echo e($flash === 'info' ? 'bg-sky-50 border-sky-200 text-sky-800' : ''); ?>

            "><?php echo e(session($flash)); ?></div>
        <?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <section class="rounded-3xl bg-white border border-slate-200 shadow-lg p-6 sm:p-8">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-slate-900 m-0">التحكم الكامل في نموذج `/tutor/apply`</h2>
                <p class="text-sm text-slate-500 mt-2 m-0">
                    حدّد الخطوات، انقل الحقول بينها، غيّر الإلزامي/الاختياري، وأضف خانات بأنواع مختلفة.
                    الحقول النظامية لا تُحذف حتى لا ينكسر التسجيل الحالي — يمكن إيقافها أو جعلها اختيارية (ما عدا الاسم والبريد وكلمة المرور).
                </p>
                <p class="text-xs mt-2 m-0">
                    الحالة:
                    <?php if($enabled): ?>
                        <span class="inline-flex px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-bold">مفعّل — النموذج الديناميكي يعمل</span>
                    <?php else: ?>
                        <span class="inline-flex px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 font-bold">غير مفعّل — يُستخدم النموذج القديم حتى تُزرع الخطوات</span>
                    <?php endif; ?>
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="<?php echo e(route('tutor.apply')); ?>" target="_blank" rel="noopener"
                   class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    <i class="fas fa-external-link-alt"></i> معاينة النموذج
                </a>
                <?php if($steps->isEmpty()): ?>
                <form method="POST" action="<?php echo e(route('admin.tutor-form-builder.seed')); ?>">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="inline-flex items-center gap-2 rounded-2xl bg-violet-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-violet-700">
                        <i class="fas fa-magic"></i> زرع الهيكل الافتراضي
                    </button>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </section>

    
    <section class="rounded-3xl bg-white border border-slate-200 shadow p-6">
        <h3 class="font-bold text-slate-900 mb-4">إضافة خطوة جديدة</h3>
        <form method="POST" action="<?php echo e(route('admin.tutor-form-builder.steps.store')); ?>" class="grid sm:grid-cols-4 gap-3 items-end">
            <?php echo csrf_field(); ?>
            <div class="sm:col-span-2">
                <label class="block text-xs font-semibold text-slate-500 mb-1">عنوان الخطوة</label>
                <input type="text" name="title" required class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" placeholder="مثال: أسئلة إضافية">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1">النوع</label>
                <select name="step_type" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                    <option value="fields">حقول</option>
                    <option value="intro">مقدمة</option>
                    <option value="review">مراجعة / إرسال</option>
                </select>
            </div>
            <button type="submit" class="rounded-xl bg-sky-600 text-white px-4 py-2.5 text-sm font-bold hover:bg-sky-700">إضافة</button>
            <div class="sm:col-span-4">
                <input type="text" name="description" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" placeholder="وصف اختياري يظهر أعلى الخطوة">
            </div>
        </form>
    </section>

    <?php $__empty_1 = true; $__currentLoopData = $steps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <section class="rounded-3xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 bg-slate-50 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h3 class="font-bold text-slate-900 m-0 flex items-center gap-2">
                    <span class="inline-flex w-8 h-8 items-center justify-center rounded-full bg-sky-100 text-sky-700 text-sm font-black"><?php echo e($step->sort_order); ?></span>
                    <?php echo e($step->title); ?>

                    <?php if($step->is_system): ?><span class="text-[10px] bg-slate-200 text-slate-600 px-2 py-0.5 rounded-full">نظامي</span><?php endif; ?>
                    <?php if(!$step->is_active): ?><span class="text-[10px] bg-rose-100 text-rose-700 px-2 py-0.5 rounded-full">متوقف</span><?php endif; ?>
                </h3>
                <p class="text-xs text-slate-500 m-0 mt-1">slug: <?php echo e($step->slug); ?> — نوع: <?php echo e($step->step_type); ?> — <?php echo e($step->fields->count()); ?> حقل</p>
            </div>
        </div>

        <div class="p-5 space-y-4">
            <form method="POST" action="<?php echo e(route('admin.tutor-form-builder.steps.update', $step)); ?>" class="grid sm:grid-cols-6 gap-3 items-end border border-slate-100 rounded-2xl p-4">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-500 mb-1">العنوان</label>
                    <input type="text" name="title" value="<?php echo e($step->title); ?>" required class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">الترتيب</label>
                    <input type="number" name="sort_order" value="<?php echo e($step->sort_order); ?>" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">النوع</label>
                    <select name="step_type" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                        <?php $__currentLoopData = ['fields'=>'حقول','intro'=>'مقدمة','review'=>'مراجعة']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tv => $tl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($tv); ?>" <?php if($step->step_type === $tv): echo 'selected'; endif; ?>><?php echo e($tl); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <label class="inline-flex items-center gap-2 text-sm text-slate-700 pb-2">
                    <input type="checkbox" name="is_active" value="1" <?php if($step->is_active): echo 'checked'; endif; ?>> نشطة
                </label>
                <button type="submit" class="rounded-xl bg-slate-800 text-white px-3 py-2 text-sm font-semibold">حفظ الخطوة</button>
                <div class="sm:col-span-6">
                    <input type="text" name="description" value="<?php echo e($step->description); ?>" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" placeholder="الوصف">
                </div>
            </form>

            <?php if (! ($step->is_system)): ?>
            <form method="POST" action="<?php echo e(route('admin.tutor-form-builder.steps.destroy', $step)); ?>" onsubmit="return confirm('حذف الخطوة وكل حقولها؟')">
                <?php echo csrf_field(); ?>
                <?php echo method_field('DELETE'); ?>
                <button type="submit" class="text-xs text-rose-600 font-semibold hover:underline">حذف الخطوة</button>
            </form>
            <?php endif; ?>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-3 py-2 text-right">#</th>
                            <th class="px-3 py-2 text-right">الحقل</th>
                            <th class="px-3 py-2 text-right">النوع</th>
                            <th class="px-3 py-2 text-right">إلزامي</th>
                            <th class="px-3 py-2 text-right">نشط</th>
                            <th class="px-3 py-2 text-right">الخطوة</th>
                            <th class="px-3 py-2 text-center">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php $__currentLoopData = $step->fields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="<?php echo e($field->is_active ? '' : 'opacity-50'); ?>">
                            <td class="px-3 py-3 align-top"><?php echo e($field->sort_order); ?></td>
                            <td class="px-3 py-3 align-top">
                                <div class="font-semibold text-slate-900"><?php echo e($field->label); ?></div>
                                <div class="text-[11px] text-slate-400 font-mono" dir="ltr"><?php echo e($field->field_key); ?></div>
                                <?php if($field->is_system): ?><span class="text-[10px] bg-slate-100 px-1.5 rounded">نظامي</span><?php endif; ?>
                            </td>
                            <td class="px-3 py-3 align-top whitespace-nowrap"><?php echo e($field->typeLabel()); ?></td>
                            <td class="px-3 py-3 align-top"><?php echo e($field->is_required ? 'نعم' : 'لا'); ?></td>
                            <td class="px-3 py-3 align-top"><?php echo e($field->is_active ? 'نعم' : 'لا'); ?></td>
                            <td class="px-3 py-3 align-top text-xs text-slate-500"><?php echo e($step->title); ?></td>
                            <td class="px-3 py-3 align-top">
                                <details class="text-right">
                                    <summary class="cursor-pointer text-sky-700 font-semibold text-xs list-none">تعديل</summary>
                                    <form method="POST" action="<?php echo e(route('admin.tutor-form-builder.fields.update', $field)); ?>" class="mt-3 space-y-2 min-w-[18rem] bg-slate-50 border border-slate-200 rounded-xl p-3">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PUT'); ?>
                                        <input type="text" name="label" value="<?php echo e($field->label); ?>" required class="w-full rounded-lg border border-slate-200 px-2 py-1.5 text-xs" placeholder="العنوان">
                                        <select name="step_id" class="w-full rounded-lg border border-slate-200 px-2 py-1.5 text-xs">
                                            <?php $__currentLoopData = $steps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($s->id); ?>" <?php if($s->id === $field->step_id): echo 'selected'; endif; ?>><?php echo e($s->sort_order); ?>. <?php echo e($s->title); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                        <?php if($field->is_system): ?>
                                            <input type="hidden" name="field_type" value="<?php echo e($field->field_type); ?>">
                                            <p class="text-[10px] text-slate-500 m-0">النوع ثابت للحقول النظامية: <?php echo e($field->typeLabel()); ?></p>
                                        <?php else: ?>
                                            <select name="field_type" class="w-full rounded-lg border border-slate-200 px-2 py-1.5 text-xs">
                                                <?php $__currentLoopData = $typeOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tv => $tl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($tv); ?>" <?php if($field->field_type === $tv): echo 'selected'; endif; ?>><?php echo e($tl); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        <?php endif; ?>
                                        <div class="grid grid-cols-2 gap-2">
                                            <select name="width" class="rounded-lg border border-slate-200 px-2 py-1.5 text-xs">
                                                <option value="full" <?php if($field->width === 'full'): echo 'selected'; endif; ?>>عرض كامل</option>
                                                <option value="half" <?php if($field->width === 'half'): echo 'selected'; endif; ?>>نصف</option>
                                            </select>
                                            <input type="number" name="sort_order" value="<?php echo e($field->sort_order); ?>" class="rounded-lg border border-slate-200 px-2 py-1.5 text-xs" placeholder="ترتيب">
                                        </div>
                                        <input type="text" name="help_text" value="<?php echo e($field->help_text); ?>" class="w-full rounded-lg border border-slate-200 px-2 py-1.5 text-xs" placeholder="نص مساعدة">
                                        <input type="text" name="placeholder" value="<?php echo e($field->placeholder); ?>" class="w-full rounded-lg border border-slate-200 px-2 py-1.5 text-xs" placeholder="placeholder">
                                        <?php if (! ($field->is_system)): ?>
                                        <?php
                                            $optionsText = '';
                                            if (! empty($field->options['items']) && is_array($field->options['items'])) {
                                                foreach ($field->options['items'] as $it) {
                                                    $optionsText .= ($it['value'] ?? '').'|'.($it['label'] ?? '')."\n";
                                                }
                                            }
                                        ?>
                                        <textarea name="options_text" rows="3" class="w-full rounded-lg border border-slate-200 px-2 py-1.5 text-xs" placeholder="خيارات (سطر لكل خيار) قيمة|عنوان أو عنوان فقط"><?php echo e($optionsText); ?></textarea>
                                        <select name="options_source" class="w-full rounded-lg border border-slate-200 px-2 py-1.5 text-xs">
                                            <option value="">بدون مصدر إعدادات</option>
                                            <?php $__currentLoopData = $optionSources; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sv => $sl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($sv); ?>" <?php if(($field->options['source'] ?? null) === $sv): echo 'selected'; endif; ?>><?php echo e($sl); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                        <?php endif; ?>
                                        <label class="inline-flex items-center gap-2 text-xs">
                                            <input type="checkbox" name="is_required" value="1" <?php if($field->is_required): echo 'checked'; endif; ?> <?php if(in_array($field->field_key, ['name','email','password'], true)): echo 'disabled'; endif; ?>>
                                            إلزامي
                                        </label>
                                        <label class="inline-flex items-center gap-2 text-xs">
                                            <input type="checkbox" name="is_active" value="1" <?php if($field->is_active): echo 'checked'; endif; ?> <?php if(in_array($field->field_key, ['name','email','password'], true)): echo 'disabled'; endif; ?>>
                                            نشط
                                        </label>
                                        <button type="submit" class="w-full rounded-lg bg-sky-600 text-white text-xs font-bold py-2">حفظ الحقل</button>
                                    </form>
                                    <?php if (! ($field->is_system)): ?>
                                    <form method="POST" action="<?php echo e(route('admin.tutor-form-builder.fields.destroy', $field)); ?>" class="mt-2" onsubmit="return confirm('حذف الحقل؟')">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="text-xs text-rose-600 font-semibold">حذف</button>
                                    </form>
                                    <?php endif; ?>
                                </details>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>

            <?php if($step->step_type === 'fields' || $step->step_type === 'review'): ?>
            <details class="rounded-2xl border border-dashed border-violet-200 bg-violet-50/40 p-4">
                <summary class="cursor-pointer font-bold text-violet-800 text-sm">+ إضافة حقل مخصص لهذه الخطوة</summary>
                <form method="POST" action="<?php echo e(route('admin.tutor-form-builder.fields.store')); ?>" class="mt-4 grid sm:grid-cols-2 gap-3">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="step_id" value="<?php echo e($step->id); ?>">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">عنوان الحقل</label>
                        <input type="text" name="label" required class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">مفتاح تقني (اختياري)</label>
                        <input type="text" name="field_key" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" dir="ltr" placeholder="custom_question_1">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">النوع</label>
                        <select name="field_type" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                            <?php $__currentLoopData = ['text','textarea','email','url','number','tel','date','select','radio','checkbox_group','multiselect','file','info']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($tv); ?>"><?php echo e($typeOptions[$tv] ?? $tv); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">العرض</label>
                        <select name="width" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                            <option value="full">كامل</option>
                            <option value="half">نصف</option>
                        </select>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-500 mb-1">خيارات القائمة (سطر لكل خيار: قيمة|عنوان)</label>
                        <textarea name="options_text" rows="3" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" placeholder="yes|نعم&#10;no|لا"></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">أو مصدر من الإعدادات</label>
                        <select name="options_source" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                            <option value="">—</option>
                            <?php $__currentLoopData = $optionSources; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sv => $sl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($sv); ?>"><?php echo e($sl); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="flex items-center gap-4">
                        <label class="inline-flex items-center gap-2 text-sm"><input type="checkbox" name="is_required" value="1" checked> إلزامي</label>
                    </div>
                    <div class="sm:col-span-2">
                        <button type="submit" class="rounded-xl bg-violet-600 text-white px-4 py-2.5 text-sm font-bold hover:bg-violet-700">إضافة الحقل</button>
                    </div>
                </form>
            </details>
            <?php endif; ?>
        </div>
    </section>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <div class="rounded-3xl border border-amber-200 bg-amber-50 p-8 text-center text-amber-900">
        لا يوجد مخطط بعد. اضغط «زرع الهيكل الافتراضي» لنسخ النموذج الحالي كما هو، ثم عدّله بحرية.
    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sana\resources\views/admin/tutor-form-builder/index.blade.php ENDPATH**/ ?>