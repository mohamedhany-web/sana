<?php $__env->startSection('title', 'إعداد معلم الحصص'); ?>
<?php $__env->startSection('header', 'إعداد معلم الحصص'); ?>
<?php echo $__env->make('instructor.tutor-lessons.partials.dashboard-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->startSection('content'); ?>
<div class="id-tutor-page space-y-6 pb-6 w-full">
    <?php if(session('success')): ?>
        <div class="ins-flash bg-emerald-50 border border-emerald-200 text-emerald-800"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if(session('info')): ?>
        <div class="ins-flash bg-sky-50 border border-sky-200 text-sky-800"><?php echo e(session('info')); ?></div>
    <?php endif; ?>
    <?php if($errors->any()): ?>
        <div class="ins-flash bg-rose-50 border border-rose-200 text-rose-800">
            <p class="font-bold mb-2">تعذّر الحفظ — راجع الحقول التالية:</p>
            <ul class="list-disc list-inside text-sm space-y-1">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $err): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($err); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if($profile->isTutorActivated()): ?>
        <div class="ins-flash bg-emerald-50 border border-emerald-300 text-emerald-900">
            <i class="fas fa-check-circle me-1"></i>
            حسابك <strong>مفعّل</strong> — يظهر للطلاب في «اختيار معلم» عند تطابق المادة ونمط الحجز.
        </div>
    <?php elseif(!empty($activationBlockers)): ?>
        <div class="ins-flash bg-amber-50 border border-amber-200 text-amber-900">
            <p class="font-bold mb-1">لإظهار حسابك للطلاب:</p>
            <ul class="list-disc list-inside text-sm space-y-0.5">
                <?php $__currentLoopData = $activationBlockers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hint): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($hint); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <section class="id-hero">
        <div class="id-hero-main relative z-[1]">
            <p class="text-xs font-bold id-tag mb-1">إعداد الحصص</p>
            <h2 class="text-xl font-black text-slate-900 m-0">ملف المعلم والجدول</h2>
            <p class="text-sm text-slate-600 mt-2 mb-0">احفظ ملفك، أضف أوقاتك الأسبوعية، وفعّل «اختيار معلم» — بعدها يظهر اسمك للطلاب تلقائياً.</p>
        </div>
    </section>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <form method="post" action="<?php echo e(route('instructor.tutor-lessons.setup.profile')); ?>" class="id-panel id-form">
            <div class="id-panel-head"><h3 class="font-bold m-0">الملف التعريفي</h3></div>
            <div class="id-panel-body space-y-4">
                <?php echo csrf_field(); ?>
                <div>
                    <label>العنوان المختصر</label>
                    <input name="headline" value="<?php echo e(old('headline', $profile->headline)); ?>" required>
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
                    <label>نبذة</label>
                    <textarea name="bio" rows="4" required><?php echo e(old('bio', $profile->bio)); ?></textarea>
                    <?php $__errorArgs = ['bio'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-rose-600 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label>سنوات الخبرة</label>
                    <input type="number" name="years_experience" min="0" value="<?php echo e(old('years_experience', $profile->tutor_years_experience ?? 0)); ?>" required>
                </div>
                <div>
                    <label>المواد *</label>
                    <?php if($subjects->isEmpty()): ?>
                        <p class="text-sm text-rose-600">لا توجد مواد نشطة — أضفها من لوحة الإدارة أولاً.</p>
                    <?php else: ?>
                        <div class="flex flex-wrap gap-2">
                            <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <label class="id-chip">
                                    <input type="checkbox" name="subject_ids[]" value="<?php echo e($s->id); ?>" <?php if(in_array($s->id, old('subject_ids', $profile->tutor_subject_ids ?? []))): echo 'checked'; endif; ?>>
                                    <?php echo e($s->name); ?>

                                </label>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php endif; ?>
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
                    <label>المراحل / المسارات *</label>
                    <?php if($years->isEmpty()): ?>
                        <p class="text-sm text-rose-600">لا توجد مراحل نشطة — أضفها من لوحة الإدارة.</p>
                    <?php else: ?>
                        <div class="flex flex-wrap gap-2">
                            <?php $__currentLoopData = $years; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $y): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <label class="id-chip">
                                    <input type="checkbox" name="academic_year_ids[]" value="<?php echo e($y->id); ?>" <?php if(in_array($y->id, old('academic_year_ids', $profile->tutor_academic_year_ids ?? []))): echo 'checked'; endif; ?>>
                                    <?php echo e($y->name); ?>

                                </label>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php endif; ?>
                    <?php $__errorArgs = ['academic_year_ids'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-rose-600 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label>أنماط التوافق * <span class="text-xs font-normal text-slate-500">(يجب تفعيل «اختيار معلم» على الأقل)</span></label>
                    <?php $oldModes = old('matching_modes', $profile->tutor_matching_modes ?? ['pick_teacher']); ?>
                    <?php $__currentLoopData = ['assisted'=>'مساعدة','self_schedule'=>'حجز ذاتي','pick_teacher'=>'اختيار معلم']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$lbl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <label class="id-chip me-1">
                            <input type="checkbox" name="matching_modes[]" value="<?php echo e($k); ?>" <?php if(in_array($k, $oldModes, true)): echo 'checked'; endif; ?>> <?php echo e($lbl); ?>

                        </label>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
                    <label>أنواع الحصص *</label>
                    <label class="id-chip"><input type="checkbox" name="session_types[]" value="one_to_one" <?php if(in_array('one_to_one', old('session_types', $profile->tutor_session_types ?? ['one_to_one']), true)): echo 'checked'; endif; ?>> فردي</label>
                    <label class="id-chip"><input type="checkbox" name="session_types[]" value="small_group" <?php if(in_array('small_group', old('session_types', $profile->tutor_session_types ?? []), true)): echo 'checked'; endif; ?>> مجموعة</label>
                </div>
                <div>
                    <label>مدة الحصة الافتراضية (دقيقة)</label>
                    <input type="number" name="default_duration" value="<?php echo e(old('default_duration', $profile->tutor_default_duration_minutes ?? 60)); ?>" min="30" max="180">
                </div>
                <button type="submit" class="id-btn-primary w-full justify-center">حفظ الملف</button>
            </div>
        </form>

        <div class="space-y-6">
            <div class="id-panel id-form">
                <div class="id-panel-head"><h3 class="font-bold m-0">الجدول الأسبوعي</h3></div>
                <div class="id-panel-body">
                    <form method="post" action="<?php echo e(route('instructor.tutor-lessons.availability.store')); ?>" class="grid grid-cols-1 sm:grid-cols-4 gap-3 items-end mb-4">
                        <?php echo csrf_field(); ?>
                        <div>
                            <label>اليوم</label>
                            <select name="day_of_week">
                                <?php $__currentLoopData = \App\Models\TutorAvailability::dayLabels(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d=>$l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($d); ?>" <?php if(old('day_of_week') == $d): echo 'selected'; endif; ?>><?php echo e($l); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div>
                            <label>من</label>
                            <input type="time" name="start_time" value="<?php echo e(old('start_time', '09:00')); ?>" required>
                            <?php $__errorArgs = ['start_time'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div>
                            <label>إلى</label>
                            <input type="time" name="end_time" value="<?php echo e(old('end_time', '17:00')); ?>" required>
                            <?php $__errorArgs = ['end_time'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <button type="submit" class="id-btn-ghost w-full justify-center">إضافة</button>
                    </form>
                    <?php $__empty_1 = true; $__currentLoopData = $availabilities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="flex justify-between items-center text-sm py-2 border-b border-slate-100">
                            <span class="font-semibold"><?php echo e($a->dayLabel()); ?> <?php echo e(substr($a->start_time,0,5)); ?>-<?php echo e(substr($a->end_time,0,5)); ?></span>
                            <form method="post" action="<?php echo e(route('instructor.tutor-lessons.availability.destroy', $a)); ?>">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="text-rose-600 text-xs font-bold">حذف</button>
                            </form>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="text-sm text-slate-500">لم تُضف فترات بعد — أضف فترة واحدة على الأقل.</p>
                    <?php endif; ?>
                </div>
            </div>

            <?php if($needsTrial && ! $profile->isTutorActivated()): ?>
            <div class="id-panel border-2 border-amber-200">
                <div class="id-panel-head bg-amber-50/80"><h3 class="font-bold text-amber-900 m-0">الجلسة التجريبية (اختياري)</h3></div>
                <div class="id-panel-body">
                    <p class="text-sm text-amber-900 mb-3">إن كان التفعيل التلقائي معطّلاً، يمكن إتمام جلسة تجريبية ثم إنهاؤها من «حجوزاتي».</p>
                    <?php if($trialBooking): ?>
                        <p class="text-sm mb-3">طلب تجريبي: <?php echo e($trialBooking->scheduled_at?->format('Y-m-d H:i')); ?> — <?php echo e($trialBooking->statusLabel()); ?></p>
                    <?php endif; ?>
                    <form method="post" action="<?php echo e(route('instructor.tutor-lessons.setup.trial')); ?>" class="flex flex-col sm:flex-row gap-2">
                        <?php echo csrf_field(); ?>
                        <input type="datetime-local" name="scheduled_at" class="flex-1" required>
                        <button type="submit" class="id-btn-primary">حجز تجريبي</button>
                    </form>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sana\resources\views\instructor\tutor-lessons\setup.blade.php ENDPATH**/ ?>