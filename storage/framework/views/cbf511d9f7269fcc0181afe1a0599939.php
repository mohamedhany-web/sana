<?php $__env->startSection('title', $assignment->title . ' - ' . config('app.name', 'Sana')); ?>
<?php $__env->startSection('header', $assignment->title); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5 sm:p-6 mb-6">
        <nav class="text-sm text-slate-500 mb-2">
            <a href="<?php echo e(route('instructor.assignments.index')); ?>" class="hover:text-sky-600"><?php echo e(__('instructor.assignments')); ?></a>
            <span class="mx-2">/</span>
            <span class="text-slate-700 font-semibold"><?php echo e($assignment->title); ?></span>
        </nav>
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-slate-800"><?php echo e($assignment->title); ?></h1>
                <p class="text-sm text-slate-600 mt-0.5"><?php echo e($assignment->course->title ?? '—'); ?></p>
            </div>
            <div class="flex items-center gap-2">
                <a href="<?php echo e(route('instructor.assignments.edit', $assignment)); ?>" class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold">
                    <i class="fas fa-edit"></i> <?php echo e(__('common.edit')); ?>

                </a>
                <a href="<?php echo e(route('instructor.assignments.submissions', $assignment)); ?>" class="inline-flex items-center gap-2 px-4 py-2.5 bg-sky-500 hover:bg-sky-600 text-white rounded-xl font-semibold">
                    <i class="fas fa-inbox"></i> <?php echo e(__('instructor.submissions_title')); ?> (<?php echo e($submissionStats['total'] ?? 0); ?>)
                </a>
            </div>
        </div>
    </div>

    <?php if($assignment->description): ?>
        <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5 mb-6">
            <h3 class="font-bold text-slate-800 mb-2"><?php echo e(__('instructor.description')); ?></h3>
            <p class="text-slate-600"><?php echo e($assignment->description); ?></p>
        </div>
    <?php endif; ?>

    <?php
        $instrRes = is_array($assignment->resource_attachments) ? $assignment->resource_attachments : [];
    ?>
    <?php if(count($instrRes) > 0): ?>
        <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5 mb-6">
            <h3 class="font-bold text-slate-800 mb-3">مرفقات الواجب (يستطيع الطلاب رؤيتها)</h3>
            <ul class="space-y-2 text-sm">
                <?php $__currentLoopData = $instrRes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $att): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $p = is_array($att) ? ($att['path'] ?? '') : '';
                        $u = $p ? (\App\Services\AssignmentFileStorage::publicUrl($p) ?? '#') : '#';
                        $lb = is_array($att) ? ($att['original_name'] ?? basename($p)) : '';
                    ?>
                    <li><a href="<?php echo e($u); ?>" target="_blank" rel="noopener" class="text-sky-600 hover:underline"><?php echo e($lb); ?></a></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 bg-slate-50 font-bold text-slate-800"><?php echo e(__('instructor.last_submissions')); ?></div>
        <div class="overflow-x-auto">
            <?php if($submissions->count() > 0): ?>
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-right text-xs font-bold text-slate-700"><?php echo e(__('instructor.student')); ?></th>
                            <th class="px-4 py-3 text-right text-xs font-bold text-slate-700"><?php echo e(__('instructor.submission_date')); ?></th>
                            <th class="px-4 py-3 text-right text-xs font-bold text-slate-700"><?php echo e(__('common.status')); ?></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        <?php $__currentLoopData = $submissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td class="px-4 py-3 text-sm text-slate-800"><?php echo e($sub->student->name ?? '—'); ?></td>
                                <td class="px-4 py-3 text-sm text-slate-600"><?php echo e($sub->submitted_at?->format('Y/m/d H:i')); ?></td>
                                <td class="px-4 py-3">
                                    <span class="text-xs font-semibold px-2 py-1 rounded <?php echo e($sub->status === 'graded' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600'); ?>"><?php echo e($sub->status); ?></span>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
                <div class="p-3 border-t border-slate-200"><?php echo e($submissions->links()); ?></div>
            <?php else: ?>
                <p class="p-6 text-center text-slate-500"><?php echo e(__('instructor.no_submissions')); ?></p>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sana\resources\views\instructor\assignments\show.blade.php ENDPATH**/ ?>