<?php $__env->startSection('title', 'حجز حصة'); ?>
<?php $__env->startSection('header', 'حجز مع '.$instructor->name); ?>

<?php echo $__env->make('student.tutor-lessons.partials.dashboard-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php $__env->startSection('content'); ?>
<?php
    $brandBlue = config('brand.colors.blue');
    $brandPurple = config('brand.colors.purple');
    $remainingHours = max(0, (int) $studentProfile->lesson_hours_quota - (int) $studentProfile->lesson_hours_used);
    $duration = (int) ($profile->tutor_default_duration_minutes ?? 60);
    $sessionLabels = \App\Models\StudentLearningProfile::sessionTypeLabels();
    $supportedSessions = $profile->tutor_session_types ?? ['one_to_one'];
    if (($groupOffers ?? collect())->isEmpty()) {
        $supportedSessions = array_values(array_filter($supportedSessions, fn ($s) => $s !== 'small_group'));
    }
    $availByDay = $availabilities->groupBy('day_of_week')->sortKeys();
    $instructorSubjects = \App\Models\AcademicSubject::whereIn('id', $profile->tutor_subject_ids ?? [])->get();
    $defaultSession = old('session_type', $studentProfile->preferred_session_type ?? 'one_to_one');
?>

<div class="sd-page space-y-6 pb-8 w-full">
    
    <div class="sd-hero">
        <div class="sd-hero-main relative z-[1]">
            <div class="flex flex-col lg:flex-row lg:items-center gap-5">
                <div class="sd-teacher-card__avatar !w-16 !h-16 !text-xl flex-shrink-0">
                    <?php if($profile->photo_url): ?>
                        <img src="<?php echo e($profile->photo_url); ?>" alt="">
                    <?php else: ?>
                        <?php echo e(mb_substr($instructor->name, 0, 1)); ?>

                    <?php endif; ?>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-bold sd-tag mb-2">حجز حصة</p>
                    <h1 class="font-heading text-2xl sm:text-3xl font-black text-slate-800 leading-tight m-0">
                        <?php echo e($instructor->name); ?>

                    </h1>
                    <?php if($profile->headline): ?>
                        <p class="text-slate-600 text-sm mt-1"><?php echo e($profile->headline); ?></p>
                    <?php endif; ?>
                    <div class="flex flex-wrap gap-2 mt-4">
                        <a href="<?php echo e(route('student.tutor-lessons.teachers')); ?>" class="sd-btn-outline">
                            <i class="fas fa-arrow-right text-xs"></i>
                            العودة للمعلمين
                        </a>
                        <a href="<?php echo e(route('student.tutor-lessons.hub')); ?>" class="sd-btn-outline">
                            <i class="fas fa-home text-xs"></i>
                            الرئيسية
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="sd-motivation">
            <span class="w-11 h-11 rounded-xl bg-white/20 flex items-center justify-center text-xl">
                <i class="fas fa-calendar-check"></i>
            </span>
            <p class="font-bold text-sm leading-relaxed">أكمل بيانات الحجز أدناه</p>
            <p class="text-xs text-white/85">متبقي من باقتك: <strong><?php echo e($remainingHours); ?></strong> ساعة</p>
            <p class="text-[11px] text-white/75">مدة الحصة: <?php echo e($duration); ?> دقيقة</p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        
        <div class="xl:col-span-2">
            <form method="post" action="<?php echo e(route('student.tutor-lessons.book.store', $instructor)); ?>" class="sd-panel sd-form">
                <div class="sd-panel-head">
                    <h2 class="font-heading font-bold text-slate-800 m-0">تفاصيل الحجز</h2>
                </div>
                <div class="sd-panel-body space-y-5">
                    <?php echo csrf_field(); ?>

                    <?php if($errors->any()): ?>
                        <div class="sd-alert sd-alert-error space-y-1">
                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <p class="m-0 flex items-start gap-2">
                                    <i class="fas fa-exclamation-circle mt-0.5"></i>
                                    <span><?php echo e($e); ?></span>
                                </p>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php endif; ?>

                    <div>
                        <label for="scheduled_at">الموعد *</label>
                        <input
                            type="datetime-local"
                            id="scheduled_at"
                            name="scheduled_at"
                            value="<?php echo e(old('scheduled_at')); ?>"
                            required
                            min="<?php echo e(now()->addHour()->format('Y-m-d\TH:i')); ?>"
                        >
                        <p class="text-[11px] text-slate-500 mt-1.5">اختر وقتاً ضمن جدول المعلم الأسبوعي (انظر الجانب).</p>
                    </div>

                    <div>
                        <label for="academic_subject_id">المادة</label>
                        <select id="academic_subject_id" name="academic_subject_id">
                            <option value="">— اختر مادة —</option>
                            <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($s->id); ?>" <?php if(old('academic_subject_id') == $s->id): echo 'selected'; endif; ?>><?php echo e($s->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php if($subjects->isEmpty()): ?>
                            <p class="text-xs text-amber-700 mt-1">حدّث موادك من <a href="<?php echo e(route('student.tutor-lessons.profile')); ?>" class="sd-link">الملف الدراسي</a>.</p>
                        <?php endif; ?>
                    </div>

                    <?php if(count($supportedSessions) > 1): ?>
                        <div>
                            <label class="mb-2">نوع الحصة</label>
                            <div class="flex flex-wrap gap-2">
                                <?php $__currentLoopData = $supportedSessions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stype): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if(isset($sessionLabels[$stype])): ?>
                                        <label class="sd-chip">
                                            <input type="radio" name="session_type" value="<?php echo e($stype); ?>" <?php if($defaultSession === $stype): echo 'checked'; endif; ?>>
                                            <?php echo e($sessionLabels[$stype]); ?>

                                        </label>
                                    <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <input type="hidden" name="session_type" value="<?php echo e($supportedSessions[0] ?? 'one_to_one'); ?>">
                    <?php endif; ?>

                    <?php if(($groupOffers ?? collect())->isNotEmpty()): ?>
                        <div id="group-offers-block" class="<?php echo e($defaultSession === 'small_group' ? '' : 'hidden'); ?>">
                            <label class="mb-2">عرض المجموعة *</label>
                            <div class="space-y-2">
                                <?php $__currentLoopData = $groupOffers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $offer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <label class="sd-chip block !items-start !text-right w-full p-3">
                                        <input type="radio" name="tutor_group_offer_id" value="<?php echo e($offer->id); ?>"
                                               data-duration="<?php echo e($offer->duration_minutes); ?>"
                                               <?php if((int) old('tutor_group_offer_id') === (int) $offer->id): echo 'checked'; endif; ?>>
                                        <span class="flex-1">
                                            <strong class="block text-slate-800"><?php echo e($offer->title); ?></strong>
                                            <span class="text-xs text-slate-500">
                                                <?php echo e($offer->min_group_size); ?>–<?php echo e($offer->max_group_size); ?> طلاب
                                                · <?php echo e($offer->duration_minutes); ?> دقيقة
                                                <?php if($offer->display_price): ?>
                                                    · <?php echo e(number_format((float) $offer->display_price, 0)); ?> ر.س
                                                <?php endif; ?>
                                            </span>
                                            <?php if($offer->description): ?>
                                                <span class="text-[11px] text-slate-500 block mt-0.5"><?php echo e(Str::limit($offer->description, 120)); ?></span>
                                            <?php endif; ?>
                                        </span>
                                    </label>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                            <?php if(($groupLimits['max_size'] ?? 0) > 0): ?>
                                <p class="text-[11px] text-slate-500 mt-1.5">باقتك تسمح بمجموعات حتى <?php echo e($groupLimits['max_size']); ?> طلاب.</p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <div>
                        <label for="student_notes">ملاحظات للمعلم</label>
                        <textarea
                            id="student_notes"
                            name="student_notes"
                            rows="4"
                            placeholder="المنهج، نقاط الضعف، الوقت المناسب، أو أي تفاصيل تساعد المعلم..."
                        ><?php echo e(old('student_notes')); ?></textarea>
                    </div>

                    <div class="pt-2 border-t border-slate-100">
                        <button type="submit" class="sd-btn-primary w-full sm:w-auto min-w-[200px]">
                            <i class="fas fa-paper-plane text-xs"></i>
                            إرسال طلب الحصة
                        </button>
                        <p class="text-[11px] text-slate-500 mt-2 m-0">
                            يُخصم من باقتك بعد تأكيد وإنهاء الحصة. يصل للمعلم إشعار بالطلب.
                        </p>
                    </div>
                </div>
            </form>
        </div>

        
        <div class="space-y-4">
            <div class="sd-panel">
                <div class="sd-panel-head">
                    <h2 class="font-heading font-bold text-slate-800 text-sm m-0">عن المعلم</h2>
                </div>
                <div class="sd-panel-body">
                    <ul class="sd-info-list m-0 p-0 list-none">
                        <li class="sd-info-item">
                            <i class="fas fa-briefcase"></i>
                            <span><?php echo e((int) ($profile->tutor_years_experience ?? 0)); ?> سنوات خبرة</span>
                        </li>
                        <li class="sd-info-item">
                            <i class="fas fa-clock"></i>
                            <span>مدة الحصة الافتراضية: <?php echo e($duration); ?> دقيقة</span>
                        </li>
                        <?php if($instructorSubjects->isNotEmpty()): ?>
                            <li class="sd-info-item">
                                <i class="fas fa-book"></i>
                                <span>
                                    المواد:
                                    <?php echo e($instructorSubjects->pluck('name')->join('، ')); ?>

                                </span>
                            </li>
                        <?php endif; ?>
                    </ul>
                    <?php if($profile->bio): ?>
                        <p class="text-xs text-slate-500 leading-relaxed mt-3 mb-0 border-t border-slate-100 pt-3">
                            <?php echo e(Str::limit(strip_tags($profile->bio), 200)); ?>

                        </p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="sd-panel">
                <div class="sd-panel-head">
                    <h2 class="font-heading font-bold text-slate-800 text-sm m-0">الجدول الأسبوعي</h2>
                </div>
                <div class="sd-panel-body">
                    <?php if($availByDay->isNotEmpty()): ?>
                        <?php $__currentLoopData = $availByDay; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day => $slots): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $dayLabel = \App\Models\TutorAvailability::dayLabels()[$day] ?? $day; ?>
                            <div class="mb-3 last:mb-0">
                                <p class="text-xs font-bold text-slate-700 mb-1"><?php echo e($dayLabel); ?></p>
                                <?php $__currentLoopData = $slots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="sd-avail-row">
                                        <span class="sd-avail-time">
                                            <?php echo e(substr($a->start_time, 0, 5)); ?> – <?php echo e(substr($a->end_time, 0, 5)); ?>

                                        </span>
                                        <span class="text-[10px] text-emerald-600 font-bold">متاح</span>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php else: ?>
                        <div class="text-center py-6 text-slate-500 text-sm">
                            <i class="fas fa-calendar-xmark text-2xl opacity-30 block mb-2"></i>
                            لم يُحدد جدول أسبوعي بعد — اختر موعداً يتوافق مع تواصل المعلم.
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="sd-panel border border-amber-100 bg-amber-50/40">
                <div class="sd-panel-body text-sm text-amber-900">
                    <p class="font-bold m-0 mb-1 flex items-center gap-2">
                        <i class="fas fa-lightbulb text-amber-600"></i>
                        نصيحة
                    </p>
                    <p class="text-xs leading-relaxed m-0 text-amber-800/90">
                        إذا لم يُقبل الموعد، جرّب وقتاً آخر ضمن الفترات المعروضة أو غيّر المعلم من قائمة المعلمين.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if(($groupOffers ?? collect())->isNotEmpty()): ?>
<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var block = document.getElementById('group-offers-block');
    if (!block) return;
    var radios = document.querySelectorAll('input[name="session_type"]');
    function sync() {
        var selected = document.querySelector('input[name="session_type"]:checked');
        var isGroup = selected && selected.value === 'small_group';
        block.classList.toggle('hidden', !isGroup);
    }
    radios.forEach(function (r) { r.addEventListener('change', sync); });
    sync();
});
</script>
<?php $__env->stopPush(); ?>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sana\resources\views\student\tutor-lessons\book.blade.php ENDPATH**/ ?>