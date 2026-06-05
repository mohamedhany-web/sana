<?php $__env->startSection('title', 'ملفي الدراسي'); ?>
<?php $__env->startSection('header', 'ملفي الدراسي'); ?>

<?php echo $__env->make('student.tutor-lessons.partials.dashboard-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php $__env->startSection('content'); ?>
<?php
    $brandBlue = config('brand.colors.blue');
    $brandPurple = config('brand.colors.purple');
    $matchingLabels = \App\Models\StudentLearningProfile::matchingModeLabels();
    $sessionLabels = \App\Models\StudentLearningProfile::sessionTypeLabels();
    $matchingLabel = $matchingLabels[$profile->matching_mode] ?? $profile->matching_mode;
    $remainingHours = max(0, (int) $profile->lesson_hours_quota - (int) $profile->lesson_hours_used);
    $selectedSubjects = $subjects->whereIn('id', $profile->subject_ids ?? []);
    $yearName = $years->firstWhere('id', $profile->academic_year_id)?->name;
    $modeHints = [
        'assisted' => 'المنصة تساعدك في إيجاد معلم مناسب عبر طلب مساعدة.',
        'self_schedule' => 'تختار موعداً من الأوقات المتاحة ويُعيَّن معلم تلقائياً.',
        'pick_teacher' => 'تتصفح المعلمين وتحجز مع من يناسبك مباشرة.',
    ];
?>

<div class="sd-page space-y-6 pb-8 w-full">
    
    <div class="sd-hero">
        <div class="sd-hero-main relative z-[1]">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                <div class="min-w-0">
                    <p class="text-xs font-bold sd-tag mb-2">حصص مع المعلمين</p>
                    <h1 class="font-heading text-2xl sm:text-3xl font-black text-slate-800 leading-tight">
                        ملفي الدراسي
                    </h1>
                    <p class="text-slate-600 text-sm mt-2 max-w-2xl leading-relaxed">
                        حدّد مرحلتك وموادك ونمط التوافق مع المعلم — يُستخدم هذا لعرض المعلمين المناسبين وطريقة الحجز.
                    </p>
                    <div class="flex flex-wrap gap-2 mt-4">
                        <a href="<?php echo e(route('student.tutor-lessons.hub')); ?>" class="sd-btn-outline">
                            <i class="fas fa-arrow-right text-xs"></i>
                            <?php echo e(__('tutor.student_hub_title')); ?>

                        </a>
                        <?php if($profile->matching_mode === 'assisted'): ?>
                            <a href="<?php echo e(route('student.tutor-lessons.assisted')); ?>" class="sd-btn-primary">
                                <i class="fas fa-hands-helping text-xs"></i>
                                طلب مساعدة
                            </a>
                        <?php elseif($profile->matching_mode === 'self_schedule'): ?>
                            <a href="<?php echo e(route('student.tutor-lessons.schedule')); ?>" class="sd-btn-primary">
                                <i class="fas fa-calendar-plus text-xs"></i>
                                <?php echo e(__('tutor.self_schedule_title')); ?>

                            </a>
                        <?php else: ?>
                            <a href="<?php echo e(route('student.tutor-lessons.teachers')); ?>" class="sd-btn-primary">
                                <i class="fas fa-user-graduate text-xs"></i>
                                اختيار معلم
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="sd-motivation">
            <span class="w-11 h-11 rounded-xl bg-white/20 flex items-center justify-center text-xl">
                <i class="fas fa-id-card"></i>
            </span>
            <p class="font-bold text-sm leading-relaxed">نمطك الحالي: <?php echo e($matchingLabel); ?></p>
            <p class="text-xs text-white/85">متبقي: <strong><?php echo e($remainingHours); ?></strong> من <?php echo e((int) $profile->lesson_hours_quota); ?> ساعة</p>
            <?php if($profile->assessed_at): ?>
                <p class="text-[11px] text-white/75">آخر تحديث: <?php echo e($profile->updated_at?->diffForHumans()); ?></p>
            <?php endif; ?>
        </div>
    </div>

    
    <div>
        <h2 class="text-sm font-bold text-slate-700 mb-3">ملخص باقة الحصص</h2>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="sd-kpi">
                <div class="flex items-start justify-between gap-2 mb-3">
                    <span class="sd-kpi-icon" style="background:linear-gradient(135deg,<?php echo e($brandBlue); ?>,#2563eb)"><i class="fas fa-box"></i></span>
                </div>
                <p class="text-2xl font-black text-slate-800 tabular-nums"><?php echo e((int) $profile->lesson_hours_quota); ?></p>
                <p class="text-xs font-bold text-slate-600 mt-0.5">ساعات الباقة</p>
            </div>
            <div class="sd-kpi">
                <div class="flex items-start justify-between gap-2 mb-3">
                    <span class="sd-kpi-icon" style="background:linear-gradient(135deg,#f59e0b,#ea580c)"><i class="fas fa-hourglass-half"></i></span>
                </div>
                <p class="text-2xl font-black text-slate-800 tabular-nums"><?php echo e((int) $profile->lesson_hours_used); ?></p>
                <p class="text-xs font-bold text-slate-600 mt-0.5">ساعات مستهلكة</p>
            </div>
            <div class="sd-kpi">
                <div class="flex items-start justify-between gap-2 mb-3">
                    <span class="sd-kpi-icon" style="background:linear-gradient(135deg,#10b981,#059669)"><i class="fas fa-clock"></i></span>
                </div>
                <p class="text-2xl font-black text-slate-800 tabular-nums"><?php echo e($remainingHours); ?></p>
                <p class="text-xs font-bold text-slate-600 mt-0.5">ساعات متبقية</p>
            </div>
            <div class="sd-kpi">
                <div class="flex items-start justify-between gap-2 mb-3">
                    <span class="sd-kpi-icon" style="background:linear-gradient(135deg,<?php echo e($brandPurple); ?>,#6d28d9)"><i class="fas fa-book-open"></i></span>
                </div>
                <p class="text-2xl font-black text-slate-800 tabular-nums"><?php echo e($selectedSubjects->count()); ?></p>
                <p class="text-xs font-bold text-slate-600 mt-0.5">مواد محددة</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        
        <div class="xl:col-span-2 space-y-4">
            <?php if(session('success')): ?>
                <div class="sd-alert sd-alert-success">
                    <i class="fas fa-check-circle me-1"></i>
                    <?php echo e(session('success')); ?>

                </div>
            <?php endif; ?>

            <form method="post" action="<?php echo e(route('student.tutor-lessons.profile.update')); ?>" class="sd-panel sd-form">
                <div class="sd-panel-head">
                    <h2 class="font-heading font-bold text-slate-800 m-0">بيانات التعلم</h2>
                </div>
                <div class="sd-panel-body space-y-6">
                    <?php echo csrf_field(); ?>

                    <?php if($errors->any()): ?>
                        <div class="sd-alert sd-alert-error space-y-1">
                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <p class="m-0"><?php echo e($e); ?></p>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php endif; ?>

                    <section class="space-y-3">
                        <h3 class="text-sm font-bold text-slate-800 m-0 flex items-center gap-2">
                            <i class="fas fa-graduation-cap text-violet-600 text-xs"></i>
                            المرحلة والمواد
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="academic_year_id">المرحلة / المسار</label>
                                <select id="academic_year_id" name="academic_year_id">
                                    <option value="">— اختر —</option>
                                    <?php $__currentLoopData = $years; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $y): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($y->id); ?>" <?php if(old('academic_year_id', $profile->academic_year_id) == $y->id): echo 'selected'; endif; ?>><?php echo e($y->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div>
                                <label>الصف / المرحلة الدراسية</label>
                                <input name="grade_stage" value="<?php echo e(old('grade_stage', $profile->grade_stage)); ?>" placeholder="مثال: الصف الثالث الثانوي">
                            </div>
                        </div>
                        <div>
                            <label>المنهج</label>
                            <input name="curriculum_label" value="<?php echo e(old('curriculum_label', $profile->curriculum_label)); ?>" placeholder="مثال: منهج وطني، IGCSE، SAT...">
                        </div>
                        <div>
                            <label>المواد *</label>
                            <?php if($subjects->isEmpty()): ?>
                                <p class="text-sm text-rose-600">لا توجد مواد نشطة في المنصة.</p>
                            <?php else: ?>
                                <div class="flex flex-wrap gap-2 mt-1">
                                    <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <label class="sd-chip">
                                            <input type="checkbox" name="subject_ids[]" value="<?php echo e($s->id); ?>" <?php if(in_array($s->id, old('subject_ids', $profile->subject_ids ?? []))): echo 'checked'; endif; ?>>
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
                    </section>

                    <section class="space-y-3 pt-2 border-t border-slate-100">
                        <h3 class="text-sm font-bold text-slate-800 m-0 flex items-center gap-2">
                            <i class="fas fa-route text-violet-600 text-xs"></i>
                            نمط التوافق مع المعلم *
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <?php $__currentLoopData = $matchingLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $lbl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <label class="sd-panel !rounded-2xl cursor-pointer border-2 border-slate-100 transition-colors has-[:checked]:border-violet-400 has-[:checked]:bg-violet-50/50 p-0 overflow-hidden">
                                    <div class="p-4">
                                        <input type="radio" name="matching_mode" value="<?php echo e($k); ?>" class="sr-only" <?php if(old('matching_mode', $profile->matching_mode) === $k): echo 'checked'; endif; ?>>
                                        <span class="block font-bold text-slate-800 text-sm"><?php echo e($lbl); ?></span>
                                        <span class="block text-xs text-slate-500 mt-1 leading-relaxed"><?php echo e($modeHints[$k] ?? ''); ?></span>
                                    </div>
                                </label>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <?php $__errorArgs = ['matching_mode'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </section>

                    <section class="space-y-3 pt-2 border-t border-slate-100">
                        <h3 class="text-sm font-bold text-slate-800 m-0 flex items-center gap-2">
                            <i class="fas fa-users text-violet-600 text-xs"></i>
                            تفضيلات الحصة
                        </h3>
                        <div>
                            <label class="mb-2 block">نوع الحصة المفضل *</label>
                            <div class="flex flex-wrap gap-2">
                                <?php $__currentLoopData = $sessionLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sk => $slbl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <label class="sd-chip">
                                        <input type="radio" name="preferred_session_type" value="<?php echo e($sk); ?>" <?php if(old('preferred_session_type', $profile->preferred_session_type) === $sk): echo 'checked'; endif; ?>>
                                        <?php echo e($slbl); ?>

                                    </label>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                        <div>
                            <label for="assessment_notes">تقييمك واحتياجاتك / الوقت المناسب</label>
                            <textarea id="assessment_notes" name="assessment_notes" rows="4" placeholder="اذكر نقاط ضعفك، أهدافك، وأوقاتك المناسبة للحصص..."><?php echo e(old('assessment_notes', $profile->assessment_notes)); ?></textarea>
                            <p class="text-[11px] text-slate-500 mt-1">يساعد المعلمين والإدارة على اقتراح الأنسب لك.</p>
                        </div>
                    </section>

                    <div class="pt-2 border-t border-slate-100 flex flex-wrap gap-3">
                        <button type="submit" class="sd-btn-primary min-w-[180px]">
                            <i class="fas fa-save text-xs"></i>
                            حفظ الملف
                        </button>
                        <a href="<?php echo e(route('student.tutor-lessons.hub')); ?>" class="sd-btn-outline">إلغاء</a>
                    </div>
                </div>
            </form>
        </div>

        
        <div class="space-y-4">
            <div class="sd-panel">
                <div class="sd-panel-head">
                    <h2 class="font-heading font-bold text-slate-800 text-sm m-0">ملخصك الحالي</h2>
                </div>
                <div class="sd-panel-body">
                    <ul class="sd-info-list m-0 p-0 list-none">
                        <li class="sd-info-item">
                            <i class="fas fa-layer-group"></i>
                            <span><strong>المرحلة:</strong> <?php echo e($yearName ?? '—'); ?></span>
                        </li>
                        <li class="sd-info-item">
                            <i class="fas fa-book"></i>
                            <span>
                                <strong>المواد:</strong>
                                <?php if($selectedSubjects->isNotEmpty()): ?>
                                    <?php echo e($selectedSubjects->pluck('name')->join('، ')); ?>

                                <?php else: ?>
                                    لم تُحدد بعد
                                <?php endif; ?>
                            </span>
                        </li>
                        <li class="sd-info-item">
                            <i class="fas fa-compass"></i>
                            <span><strong>نمط التوافق:</strong> <?php echo e($matchingLabel); ?></span>
                        </li>
                        <li class="sd-info-item">
                            <i class="fas fa-chalkboard"></i>
                            <span><strong>نوع الحصة:</strong> <?php echo e($sessionLabels[$profile->preferred_session_type] ?? $profile->preferred_session_type); ?></span>
                        </li>
                        <?php if($profile->curriculum_label): ?>
                            <li class="sd-info-item">
                                <i class="fas fa-map"></i>
                                <span><strong>المنهج:</strong> <?php echo e($profile->curriculum_label); ?></span>
                            </li>
                        <?php endif; ?>
                        <?php if($profile->grade_stage): ?>
                            <li class="sd-info-item">
                                <i class="fas fa-school"></i>
                                <span><strong>الصف:</strong> <?php echo e($profile->grade_stage); ?></span>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>

            <div class="sd-panel">
                <div class="sd-panel-head">
                    <h2 class="font-heading font-bold text-slate-800 text-sm m-0">ماذا بعد الحفظ؟</h2>
                </div>
                <div class="sd-panel-body space-y-2 text-sm">
                    <?php if($profile->matching_mode === 'assisted'): ?>
                        <a href="<?php echo e(route('student.tutor-lessons.assisted')); ?>" class="flex items-center gap-3 p-3 rounded-xl border border-slate-100 hover:border-purple-200 hover:bg-purple-50/50 transition no-underline text-inherit">
                            <span class="sd-kpi-icon !w-10 !h-10 text-sm" style="background:linear-gradient(135deg,#0ea5e9,#06b6d4)"><i class="fas fa-hands-helping"></i></span>
                            <span class="font-bold text-slate-700">إرسال طلب مساعدة</span>
                        </a>
                    <?php elseif($profile->matching_mode === 'self_schedule'): ?>
                        <a href="<?php echo e(route('student.tutor-lessons.schedule')); ?>" class="flex items-center gap-3 p-3 rounded-xl border border-slate-100 hover:border-purple-200 hover:bg-purple-50/50 transition no-underline text-inherit">
                            <span class="sd-kpi-icon !w-10 !h-10 text-sm" style="background:var(--sd-gradient)"><i class="fas fa-calendar-plus"></i></span>
                            <span class="font-bold text-slate-700">حجز موعد تلقائي</span>
                        </a>
                    <?php else: ?>
                        <a href="<?php echo e(route('student.tutor-lessons.teachers')); ?>" class="flex items-center gap-3 p-3 rounded-xl border border-slate-100 hover:border-purple-200 hover:bg-purple-50/50 transition no-underline text-inherit">
                            <span class="sd-kpi-icon !w-10 !h-10 text-sm" style="background:linear-gradient(135deg,#10b981,#059669)"><i class="fas fa-user-graduate"></i></span>
                            <span class="font-bold text-slate-700">اختيار معلم وحجز</span>
                        </a>
                    <?php endif; ?>
                    <a href="<?php echo e(route('student.tutor-lessons.bookings.index')); ?>" class="flex items-center gap-3 p-3 rounded-xl border border-slate-100 hover:border-purple-200 hover:bg-purple-50/50 transition no-underline text-inherit">
                        <span class="sd-kpi-icon !w-10 !h-10 text-sm" style="background:linear-gradient(135deg,<?php echo e($brandPurple); ?>,#6d28d9)"><i class="fas fa-list"></i></span>
                        <span class="font-bold text-slate-700">حجوزاتي</span>
                    </a>
                </div>
            </div>

            <?php if($remainingHours <= 0 && (int) $profile->lesson_hours_quota > 0): ?>
                <div class="sd-panel border border-amber-200 bg-amber-50/50">
                    <div class="sd-panel-body text-sm text-amber-900">
                        <p class="font-bold m-0 mb-1">انتهت ساعات الباقة</p>
                        <p class="text-xs m-0">تواصل مع الإدارة أو رقِّ اشتراكك لمواصلة الحجز.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sana\resources\views/student/tutor-lessons/profile.blade.php ENDPATH**/ ?>