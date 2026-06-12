

<?php $__env->startSection('title', 'تعديل طلب معلم - ' . config('app.name', 'Sana')); ?>
<?php $__env->startSection('header', 'تعديل طلب انضمام معلم'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $user = $application->user;
    $selectedSubjects = old('subject_ids', $application->tutor_subject_ids ?? []);
    $selectedYears = old('academic_year_ids', $application->tutor_academic_year_ids ?? []);
    $selectedModes = old('matching_modes', $application->tutor_matching_modes ?? []);
    $selectedSessions = old('session_types', $application->tutor_session_types ?? []);
?>

<div class="space-y-6 sm:space-y-8">
    <?php if(session('error')): ?>
        <div class="rounded-xl bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3"><?php echo e(session('error')); ?></div>
    <?php endif; ?>

    <div class="flex flex-wrap items-center gap-3">
        <a href="<?php echo e(route('admin.instructor-applications.show', $application)); ?>" data-turbo="false"
           class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
            <i class="fas fa-arrow-right"></i>
            العودة للتفاصيل
        </a>
    </div>

    <form method="POST" action="<?php echo e(route('admin.instructor-applications.update', $application)); ?>" data-turbo="false"
          class="space-y-6">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

        <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg p-6 sm:p-8 space-y-5">
            <h2 class="text-lg font-bold text-slate-900">بيانات الحساب</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-2">الاسم</label>
                    <input type="text" name="name" value="<?php echo e(old('name', $user?->name)); ?>" required
                           class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm">
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
                    <label class="block text-xs font-semibold text-slate-500 mb-2">البريد</label>
                    <input type="email" name="email" value="<?php echo e(old('email', $user?->email)); ?>" required dir="ltr"
                           class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm">
                    <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-rose-600 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-2">الجوال</label>
                    <input type="text" name="phone" value="<?php echo e(old('phone', $user?->phone)); ?>" dir="ltr"
                           class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm">
                    <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-rose-600 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-2">سنوات الخبرة</label>
                    <input type="number" name="years_experience" min="0" max="50"
                           value="<?php echo e(old('years_experience', $application->tutor_years_experience)); ?>" required
                           class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm">
                    <?php $__errorArgs = ['years_experience'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-rose-600 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>
        </section>

        <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg p-6 sm:p-8 space-y-5">
            <h2 class="text-lg font-bold text-slate-900">الملف التعريفي</h2>
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-2">العنوان المختصر</label>
                <input type="text" name="headline" value="<?php echo e(old('headline', $application->headline)); ?>" required
                       class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm">
                <?php $__errorArgs = ['headline'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-rose-600 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-2">نبذة</label>
                <textarea name="bio" rows="5" required
                          class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm"><?php echo e(old('bio', $application->bio)); ?></textarea>
                <?php $__errorArgs = ['bio'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-rose-600 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
        </section>

        <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg p-6 sm:p-8 space-y-5">
            <h2 class="text-lg font-bold text-slate-900">المواد والمراحل</h2>
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-2">المواد</label>
                <div class="flex flex-wrap gap-2">
                    <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <label class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-3 py-2 text-sm cursor-pointer hover:bg-slate-50">
                            <input type="checkbox" name="subject_ids[]" value="<?php echo e($subject->id); ?>"
                                   <?php if(in_array($subject->id, $selectedSubjects)): echo 'checked'; endif; ?>>
                            <span><?php echo e($subject->name); ?></span>
                        </label>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <?php $__errorArgs = ['subject_ids'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-rose-600 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-2">المراحل / المسارات</label>
                <div class="flex flex-wrap gap-2">
                    <?php $__currentLoopData = $years; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $year): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <label class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-3 py-2 text-sm cursor-pointer hover:bg-slate-50">
                            <input type="checkbox" name="academic_year_ids[]" value="<?php echo e($year->id); ?>"
                                   <?php if(in_array($year->id, $selectedYears)): echo 'checked'; endif; ?>>
                            <span><?php echo e($year->name); ?></span>
                        </label>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <?php $__errorArgs = ['academic_year_ids'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-rose-600 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
        </section>

        <?php if($application->status === \App\Models\InstructorProfile::STATUS_APPROVED): ?>
        <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg p-6 sm:p-8">
            <h2 class="text-lg font-bold text-slate-900 mb-4">نوع لوحة المعلم</h2>
            <?php echo $__env->make('admin.instructor-applications.partials.portal-mode-fields', [
                'selectedMode' => old('instructor_portal_mode', $application->instructor_portal_mode),
            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </section>
        <?php endif; ?>

        <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg p-6 sm:p-8 space-y-5">
            <h2 class="text-lg font-bold text-slate-900">تفضيلات الحجز</h2>
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-2">أنماط الحجز</label>
                <div class="flex flex-wrap gap-2">
                    <?php $__currentLoopData = ['assisted', 'self_schedule', 'pick_teacher']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mode): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <label class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-3 py-2 text-sm cursor-pointer hover:bg-slate-50">
                            <input type="checkbox" name="matching_modes[]" value="<?php echo e($mode); ?>"
                                   <?php if(in_array($mode, $selectedModes)): echo 'checked'; endif; ?>>
                            <span><?php echo e(__('tutor.matching_'.$mode)); ?></span>
                        </label>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <?php $__errorArgs = ['matching_modes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-rose-600 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-2">أنواع الحصص</label>
                <div class="flex flex-wrap gap-2">
                    <?php $__currentLoopData = ['one_to_one', 'small_group']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <label class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-3 py-2 text-sm cursor-pointer hover:bg-slate-50">
                            <input type="checkbox" name="session_types[]" value="<?php echo e($type); ?>"
                                   <?php if(in_array($type, $selectedSessions)): echo 'checked'; endif; ?>>
                            <span><?php echo e(__('tutor.session_'.$type)); ?></span>
                        </label>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <?php $__errorArgs = ['session_types'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-rose-600 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
        </section>

        <div class="flex flex-wrap gap-3">
            <button type="submit"
                    class="inline-flex items-center gap-2 rounded-2xl bg-sky-600 px-6 py-3 text-sm font-bold text-white hover:bg-sky-700">
                <i class="fas fa-save"></i>
                حفظ التعديلات
            </button>
            <a href="<?php echo e(route('admin.instructor-applications.show', $application)); ?>" data-turbo="false"
               class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 px-6 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                إلغاء
            </a>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sana\resources\views\admin\instructor-applications\edit.blade.php ENDPATH**/ ?>