<?php
    $selectedMode = old('instructor_portal_mode', $selectedMode ?? \App\Models\InstructorProfile::PORTAL_BOTH);
    $modes = [
        \App\Models\InstructorProfile::PORTAL_TUTOR_LESSONS => [
            'title' => 'حصص خاصة فقط',
            'desc' => 'يظهر للمعلم إدارة الحصص (الحجوزات، المواعيد، سجل العمل) دون نظام الكورسات.',
            'icon' => 'fa-user-clock',
            'active' => 'border-violet-300 bg-violet-50',
            'icon_color' => 'text-violet-600',
        ],
        \App\Models\InstructorProfile::PORTAL_COURSES => [
            'title' => 'كورسات فقط',
            'desc' => 'يظهر نظام الكورسات: محاضرات، واجبات، امتحانات، بنوك أسئلة، حضور، كلاس روم، وبث مباشر.',
            'icon' => 'fa-book-open',
            'active' => 'border-indigo-300 bg-indigo-50',
            'icon_color' => 'text-indigo-600',
        ],
        \App\Models\InstructorProfile::PORTAL_BOTH => [
            'title' => 'الاثنين معاً',
            'desc' => 'وصول كامل لنظام الحصص الخاصة ونظام الكورسات في لوحة المعلم.',
            'icon' => 'fa-layer-group',
            'active' => 'border-emerald-300 bg-emerald-50',
            'icon_color' => 'text-emerald-600',
        ],
    ];
?>

<div class="space-y-3">
    <label class="block text-xs font-semibold text-slate-500 mb-1">
        نوع لوحة المعلم <span class="text-rose-600">*</span>
    </label>
    <p class="text-xs text-slate-500 mb-3">حدد ما سيظهر للمعلم في حسابه بعد القبول. يمكن تعديله لاحقاً من صفحة التعديل.</p>
    <div class="space-y-2">
        <?php $__currentLoopData = $modes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $mode): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <label class="flex items-start gap-3 rounded-2xl border px-4 py-3 cursor-pointer transition-colors
                <?php echo e($selectedMode === $value ? $mode['active'] : 'border-slate-200 hover:bg-slate-50'); ?>">
                <input type="radio" name="instructor_portal_mode" value="<?php echo e($value); ?>"
                       class="mt-1" <?php if($selectedMode === $value): echo 'checked'; endif; ?> required>
                <span class="flex-1 min-w-0">
                    <span class="flex items-center gap-2 text-sm font-bold text-slate-900">
                        <i class="fas <?php echo e($mode['icon']); ?> <?php echo e($mode['icon_color']); ?>"></i>
                        <?php echo e($mode['title']); ?>

                    </span>
                    <span class="block text-xs text-slate-600 mt-1"><?php echo e($mode['desc']); ?></span>
                </span>
            </label>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <?php $__errorArgs = ['instructor_portal_mode'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-rose-600 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
</div>
<?php /**PATH C:\xampp\htdocs\sana\resources\views\admin\instructor-applications\partials\portal-mode-fields.blade.php ENDPATH**/ ?>