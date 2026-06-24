<?php
    $eval = $application->application_evaluation ?? [];
    $criteria = config('tutor_application.evaluation_criteria', []);
    $decisions = config('tutor_application.evaluation_decisions', []);
?>

<section class="rounded-3xl bg-white border border-indigo-200 shadow-lg p-6 sm:p-8">
    <h3 class="text-lg font-bold text-indigo-900 mb-1">١٣. تقييم فريق التوظيف</h3>
    <p class="text-xs text-slate-500 mb-4">للاستخدام الإداري فقط — لا يظهر للمتقدم</p>

    <form method="post" action="<?php echo e(route('admin.instructor-applications.evaluation', $application)); ?>" data-turbo="false" class="space-y-4">
        <?php echo csrf_field(); ?>
        <div class="overflow-x-auto">
            <table class="w-full text-sm border border-slate-100 rounded-xl overflow-hidden">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="p-2 text-right">المعيار</th>
                        <?php $__currentLoopData = [1 => 'ضعيف', 2 => 'مقبول', 3 => 'جيد', 4 => 'ممتاز']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $n => $lbl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <th class="p-2 text-center text-xs"><?php echo e($n); ?><br><?php echo e($lbl); ?></th>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tr>
                </thead>
                <tbody>
                <?php $__currentLoopData = $criteria; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="border-t border-slate-50">
                        <td class="p-2 font-medium"><?php echo e($label); ?></td>
                        <?php for($n = 1; $n <= 4; $n++): ?>
                        <td class="p-2 text-center">
                            <input type="radio" name="scores[<?php echo e($key); ?>]" value="<?php echo e($n); ?>" <?php if((int)($eval['scores'][$key] ?? 0) === $n): echo 'checked'; endif; ?>>
                        </td>
                        <?php endfor; ?>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>

        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">قرار مبدئي</label>
                <select name="decision" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                    <option value="">—</option>
                    <?php $__currentLoopData = $decisions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $lbl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($val); ?>" <?php if(($eval['decision'] ?? '') === $val): echo 'selected'; endif; ?>><?php echo e($lbl); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">اسم المقيم</label>
                <input type="text" name="reviewer_name" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm"
                       value="<?php echo e(old('reviewer_name', $eval['reviewer_name'] ?? auth()->user()?->name)); ?>">
            </div>
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1">ملاحظات</label>
            <textarea name="notes" rows="3" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm"><?php echo e(old('notes', $eval['notes'] ?? '')); ?></textarea>
        </div>
        <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-indigo-600 text-white text-sm font-bold hover:bg-indigo-700">
            <i class="fas fa-save"></i> حفظ التقييم
        </button>
    </form>
</section>
<?php /**PATH C:\xampp\htdocs\sana\resources\views\admin\instructor-applications\partials\evaluation-form.blade.php ENDPATH**/ ?>