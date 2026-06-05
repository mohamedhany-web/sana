<?php $__env->startSection('title', 'اختيار معلم'); ?>
<?php $__env->startSection('header', 'اختيار معلم'); ?>

<?php echo $__env->make('student.tutor-lessons.partials.dashboard-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php $__env->startSection('content'); ?>
<?php
    $brandBlue = config('brand.colors.blue');
    $brandPurple = config('brand.colors.purple');
    $matchingLabel = \App\Models\StudentLearningProfile::matchingModeLabels()[$profile->matching_mode] ?? $profile->matching_mode;
    $remainingHours = max(0, (int) $profile->lesson_hours_quota - (int) $profile->lesson_hours_used);
    $subjectMap = $subjects->keyBy('id');
    $teacherCount = $profiles->count();
?>

<div class="sd-page space-y-6 pb-8 w-full">
    
    <div class="sd-hero">
        <div class="sd-hero-main relative z-[1]">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                <div class="min-w-0">
                    <p class="text-xs font-bold sd-tag mb-2">حصص مع المعلمين</p>
                    <h1 class="font-heading text-2xl sm:text-3xl font-black text-slate-800 leading-tight">
                        اختيار معلم
                    </h1>
                    <p class="text-slate-600 text-sm mt-2 max-w-2xl leading-relaxed">
                        تصفّح المعلمين المفعّلين لموادك ونمط حجزك، ثم احجز حصة مباشرة.
                    </p>
                    <div class="flex flex-wrap gap-2 mt-4">
                        <a href="<?php echo e(route('student.tutor-lessons.hub')); ?>" class="sd-btn-outline">
                            <i class="fas fa-arrow-right text-xs"></i>
                            <?php echo e(__('tutor.student_hub_title')); ?>

                        </a>
                        <a href="<?php echo e(route('student.tutor-lessons.profile')); ?>" class="sd-btn-outline">
                            <i class="fas fa-sliders text-xs"></i>
                            ملفي الدراسي
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="sd-motivation">
            <span class="w-11 h-11 rounded-xl bg-white/20 flex items-center justify-center text-xl">
                <i class="fas fa-user-graduate"></i>
            </span>
            <p class="font-bold text-sm leading-relaxed">
                <?php echo e($teacherCount > 0 ? $teacherCount.' معلم متاح' : 'لا معلمين لهذه المادة حالياً'); ?>

            </p>
            <p class="text-xs text-white/85">متبقي من باقتك: <strong><?php echo e($remainingHours); ?></strong> ساعة</p>
            <p class="text-[11px] text-white/75">نمط التوافق: <?php echo e($matchingLabel); ?></p>
        </div>
    </div>

    
    <?php if($subjects->isNotEmpty()): ?>
        <div class="sd-panel">
            <div class="sd-panel-head">
                <h2 class="font-heading font-bold text-slate-800 text-sm m-0">تصفية حسب المادة</h2>
            </div>
            <div class="sd-panel-body">
                <form method="get" action="<?php echo e(route('student.tutor-lessons.teachers')); ?>" class="sd-filter-bar">
                    <div>
                        <label class="text-xs font-bold text-slate-600 block mb-1">المادة</label>
                        <select name="subject_id" onchange="this.form.submit()">
                            <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($s->id); ?>" <?php if((int) $subjectId === (int) $s->id): echo 'selected'; endif; ?>><?php echo e($s->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <?php if($subjectId && $subjectMap->has($subjectId)): ?>
                        <p class="text-xs text-slate-500 mb-0 self-center">
                            تعرض المعلمين الذين يدرّسون: <strong class="text-slate-700"><?php echo e($subjectMap[$subjectId]->name); ?></strong>
                        </p>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    <?php endif; ?>

    
    <?php if($profiles->isNotEmpty()): ?>
        <div>
            <h2 class="text-sm font-bold text-slate-700 mb-3">المعلمون المتاحون</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
                <?php $__currentLoopData = $profiles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $instructor = $p->user;
                        $teacherSubjects = collect($p->tutor_subject_ids ?? [])
                            ->map(fn ($id) => $subjectMap->get($id)?->name)
                            ->filter()
                            ->take(3);
                    ?>
                    <article class="sd-teacher-card">
                        <div class="sd-teacher-card__head">
                            <div class="flex gap-3 items-start">
                                <div class="sd-teacher-card__avatar">
                                    <?php if($p->photo_url): ?>
                                        <img src="<?php echo e($p->photo_url); ?>" alt="">
                                    <?php else: ?>
                                        <?php echo e(mb_substr($instructor?->name ?? '?', 0, 1)); ?>

                                    <?php endif; ?>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h3 class="font-heading font-bold text-slate-800 truncate text-base m-0">
                                        <?php echo e($instructor?->name); ?>

                                    </h3>
                                    <?php if($p->headline): ?>
                                        <p class="text-sm text-slate-600 mt-1 line-clamp-2 leading-snug"><?php echo e($p->headline); ?></p>
                                    <?php endif; ?>
                                    <p class="text-xs text-slate-400 mt-1.5 flex items-center gap-1">
                                        <i class="fas fa-briefcase text-[10px]"></i>
                                        <?php echo e((int) ($p->tutor_years_experience ?? 0)); ?> سنوات خبرة
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="sd-teacher-card__body">
                            <?php if($teacherSubjects->isNotEmpty()): ?>
                                <div class="flex flex-wrap gap-1.5">
                                    <?php $__currentLoopData = $teacherSubjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <span class="sd-pill"><?php echo e($subName); ?></span>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            <?php endif; ?>
                            <?php if($p->bio): ?>
                                <p class="text-xs text-slate-500 line-clamp-3 leading-relaxed m-0"><?php echo e(Str::limit(strip_tags($p->bio), 120)); ?></p>
                            <?php endif; ?>
                            <p class="text-[11px] text-slate-400 mt-auto flex items-center gap-1">
                                <i class="fas fa-clock text-[10px]"></i>
                                مدة الحصة الافتراضية: <?php echo e((int) ($p->tutor_default_duration_minutes ?? 60)); ?> دقيقة
                            </p>
                        </div>
                        <div class="sd-teacher-card__foot">
                            <a href="<?php echo e(route('student.tutor-lessons.book', $instructor)); ?>" class="sd-btn-primary w-full">
                                <i class="fas fa-calendar-plus text-xs"></i>
                                حجز حصة
                            </a>
                        </div>
                    </article>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    <?php else: ?>
        <div class="sd-panel">
            <div class="sd-empty">
                <i class="fas fa-user-slash"></i>
                <p class="font-bold text-slate-700 mb-2">لا يوجد معلمون متاحون حالياً</p>
                <p class="text-sm max-w-md mx-auto leading-relaxed">
                    قد يكون السبب عدم تطابق المادة، أو أن المعلمين لم يُفعَّلوا بعد.
                    جرّب مادة أخرى من ملفك، أو تواصل مع الدعم.
                </p>
                <div class="flex flex-wrap justify-center gap-2 mt-5">
                    <a href="<?php echo e(route('student.tutor-lessons.profile')); ?>" class="sd-btn-outline">تعديل موادي</a>
                    <a href="<?php echo e(route('student.tutor-lessons.hub')); ?>" class="sd-btn-primary">العودة للرئيسية</a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sana\resources\views/student/tutor-lessons/teachers.blade.php ENDPATH**/ ?>