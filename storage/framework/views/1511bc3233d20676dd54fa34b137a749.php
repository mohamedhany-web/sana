

<?php $__env->startSection('title', 'حجز حصة لابنك'); ?>
<?php $__env->startSection('header', 'حجز مع '.$instructor->name); ?>

<?php echo $__env->make('student.tutor-lessons.partials.dashboard-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php $__env->startSection('content'); ?>
<?php
    $duration = (int) ($profile->tutor_default_duration_minutes ?? 60);
    $sessionLabels = \App\Models\StudentLearningProfile::sessionTypeLabels();
    $supportedSessions = $profile->tutor_session_types ?? ['one_to_one'];
    if (($groupOffers ?? collect())->isEmpty()) {
        $supportedSessions = array_values(array_filter($supportedSessions, fn ($s) => $s !== 'small_group'));
    }
    $defaultSession = old('session_type', $studentProfile->preferred_session_type ?? 'one_to_one');
    $availByDay = $availabilities->groupBy('day_of_week')->sortKeys();
?>

<div class="sd-page space-y-6 pb-8 w-full max-w-3xl mx-auto">
    <div class="sd-panel">
        <div class="sd-panel-head">
            <h1 class="font-heading font-bold text-slate-800 m-0">حجز حصة لـ <?php echo e($student->name); ?></h1>
            <p class="text-sm text-slate-600 mt-1 m-0">مع المعلم <?php echo e($instructor->name); ?></p>
        </div>
        <div class="sd-panel-body">
            <form method="post" action="<?php echo e(route('parent.tutor-lessons.book.store', $instructor)); ?>" class="sd-form space-y-5">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="student_id" value="<?php echo e($student->id); ?>">

                <?php if($errors->any()): ?>
                    <div class="sd-alert sd-alert-error space-y-1">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><p class="m-0"><?php echo e($e); ?></p><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php endif; ?>

                <div>
                    <label for="scheduled_at">الموعد *</label>
                    <input type="datetime-local" id="scheduled_at" name="scheduled_at" required
                           value="<?php echo e(old('scheduled_at')); ?>"
                           min="<?php echo e(now()->addHour()->format('Y-m-d\TH:i')); ?>">
                </div>

                <?php if(($subjects ?? collect())->isNotEmpty()): ?>
                <div>
                    <label for="academic_subject_id">المادة</label>
                    <select id="academic_subject_id" name="academic_subject_id">
                        <option value="">— اختر —</option>
                        <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($s->id); ?>" <?php if(old('academic_subject_id') == $s->id): echo 'selected'; endif; ?>><?php echo e($s->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <?php endif; ?>

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
                                <label class="sd-chip block !items-start w-full p-3">
                                    <input type="radio" name="tutor_group_offer_id" value="<?php echo e($offer->id); ?>"
                                           <?php if((int) old('tutor_group_offer_id') === (int) $offer->id): echo 'checked'; endif; ?>>
                                    <span class="flex-1">
                                        <strong class="block"><?php echo e($offer->title); ?></strong>
                                        <span class="text-xs text-slate-500">
                                            <?php echo e($offer->min_group_size); ?>–<?php echo e($offer->max_group_size); ?> طلاب · <?php echo e($offer->duration_minutes); ?> دقيقة
                                        </span>
                                    </span>
                                </label>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div>
                    <label for="student_notes">ملاحظات</label>
                    <textarea id="student_notes" name="student_notes" rows="3"><?php echo e(old('student_notes')); ?></textarea>
                </div>

                <button type="submit" class="sd-btn-primary">إرسال طلب الحصة</button>
            </form>
        </div>
    </div>
</div>

<?php if(($groupOffers ?? collect())->isNotEmpty()): ?>
<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var block = document.getElementById('group-offers-block');
    if (!block) return;
    document.querySelectorAll('input[name="session_type"]').forEach(function (r) {
        r.addEventListener('change', function () {
            var sel = document.querySelector('input[name="session_type"]:checked');
            block.classList.toggle('hidden', !sel || sel.value !== 'small_group');
        });
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sana\resources\views\parent\tutor-lessons\book.blade.php ENDPATH**/ ?>