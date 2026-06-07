<?php $__env->startSection('title', __('tutor.self_schedule_title')); ?>
<?php $__env->startSection('header', __('tutor.self_schedule_title')); ?>
<?php echo $__env->make('student.tutor-lessons.partials.dashboard-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->startSection('content'); ?>
<div class="sd-page w-full pb-8 max-w-3xl">
    <a href="<?php echo e(route('student.tutor-lessons.hub')); ?>" class="text-sm sd-link mb-4 inline-block">← <?php echo e(__('tutor.student_hub_title')); ?></a>
    <div class="sd-panel mb-4">
        <div class="sd-panel-body">
            <p class="text-sm text-slate-600"><?php echo e(__('tutor.self_schedule_hint')); ?></p>
            <p class="text-xs text-slate-500 mt-2">المتبقي من باقتك: <strong><?php echo e(max(0, (int) $profile->lesson_hours_quota - (int) $profile->lesson_hours_used)); ?></strong> ساعة</p>
        </div>
    </div>
    <?php if($subjects->isNotEmpty()): ?>
    <form method="get" class="mb-4 flex gap-2 items-end">
        <div class="flex-1">
            <label class="text-xs font-bold">المادة</label>
            <select name="subject_id" class="w-full border rounded-lg px-3 py-2" onchange="this.form.submit()">
                <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($s->id); ?>" <?php if($subjectId == $s->id): echo 'selected'; endif; ?>><?php echo e($s->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
    </form>
    <?php endif; ?>
    <?php if($errors->any()): ?>
        <div class="sd-panel mb-4 border-rose-200 bg-rose-50 text-rose-700 text-sm p-4">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><p><?php echo e($e); ?></p><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>
    <?php if(empty($slots)): ?>
        <div class="sd-panel sd-panel-body text-center text-slate-500 py-10"><?php echo e(__('tutor.no_slots')); ?></div>
    <?php else: ?>
        <p class="text-sm font-bold text-slate-700 mb-3"><?php echo e(__('tutor.pick_slot')); ?></p>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <?php $__currentLoopData = $slots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $slot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <form method="post" action="<?php echo e(route('student.tutor-lessons.schedule.store')); ?>" class="sd-panel hover:border-violet-400 transition-colors">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="scheduled_at" value="<?php echo e(\Carbon\Carbon::parse($slot['scheduled_at'])->format('Y-m-d H:i:s')); ?>">
                <?php if($subjectId): ?><input type="hidden" name="academic_subject_id" value="<?php echo e($subjectId); ?>"><?php endif; ?>
                <button type="submit" class="w-full text-right p-4">
                    <span class="font-bold text-slate-800 block"><?php echo e($slot['label']); ?></span>
                    <span class="text-xs text-violet-600 mt-1">حجز ← تعيين معلم تلقائياً</span>
                </button>
            </form>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sana\resources\views\student\tutor-lessons\schedule.blade.php ENDPATH**/ ?>