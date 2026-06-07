<?php $__env->startSection('title', $liveSession->title); ?>
<?php $__env->startSection('header', $liveSession->title); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full px-4 sm:px-6 lg:px-8 py-6 space-y-6 max-w-4xl mx-auto">
    
    <div class="flex flex-wrap items-center gap-2 text-sm">
        <a href="<?php echo e(route('student.live-sessions.index')); ?>" class="text-sky-600 hover:text-sky-800 font-medium inline-flex items-center gap-1">
            <i class="fas fa-arrow-right"></i>
            جلسات البث
        </a>
        <span class="text-gray-300">|</span>
        <span class="text-gray-600 truncate max-w-[200px] sm:max-w-md"><?php echo e($liveSession->title); ?></span>
    </div>

    <?php if(session('success')): ?>
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-900 px-4 py-3 text-sm font-medium"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="rounded-xl border border-red-200 bg-red-50 text-red-900 px-4 py-3 text-sm font-medium"><?php echo e(session('error')); ?></div>
    <?php endif; ?>

    
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 sm:p-6">
        <div class="flex flex-wrap items-start gap-3">
            <div class="w-12 h-12 rounded-xl bg-red-50 text-red-600 flex items-center justify-center shrink-0">
                <i class="fas fa-broadcast-tower text-lg"></i>
            </div>
            <div class="min-w-0 flex-1">
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900"><?php echo e($liveSession->title); ?></h1>
                <p class="text-sm text-gray-500 mt-1">
                    <?php if($liveSession->isLive()): ?>
                        <span class="inline-flex items-center gap-1.5 text-red-600 font-semibold">
                            <span class="w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
                            بث مباشر الآن
                        </span>
                    <?php elseif($liveSession->isScheduled()): ?>
                        مجدولة — <?php echo e($liveSession->scheduled_at?->diffForHumans()); ?>

                    <?php else: ?>
                        <span class="text-gray-600">منتهية</span>
                    <?php endif; ?>
                </p>
            </div>
        </div>

        <?php if($liveSession->isLive()): ?>
        <div class="mt-6 pt-6 border-t border-gray-200">
            <form method="POST" action="<?php echo e(route('student.live-sessions.join', $liveSession)); ?>">
                <?php echo csrf_field(); ?>
                <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-3.5 bg-red-600 hover:bg-red-700 text-white rounded-xl font-bold shadow-md shadow-red-500/20 transition-colors text-base">
                    <i class="fas fa-video"></i>
                    انضم إلى البث الآن
                </button>
            </form>
        </div>
        <?php endif; ?>
    </div>

    
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 sm:p-6 space-y-4">
        <h2 class="text-sm font-bold text-gray-500 uppercase tracking-wide">تفاصيل الجلسة</h2>
        <div class="grid sm:grid-cols-2 gap-4">
            <div class="flex items-start gap-3 p-4 rounded-xl bg-gray-50 border border-gray-100">
                <i class="fas fa-chalkboard-teacher text-sky-600 mt-0.5"></i>
                <div>
                    <p class="text-xs font-semibold text-gray-500 mb-0.5">المدرب</p>
                    <p class="font-semibold text-gray-900"><?php echo e($liveSession->instructor?->name ?? '—'); ?></p>
                </div>
            </div>
            <div class="flex items-start gap-3 p-4 rounded-xl bg-gray-50 border border-gray-100">
                <i class="fas fa-graduation-cap text-emerald-600 mt-0.5"></i>
                <div>
                    <p class="text-xs font-semibold text-gray-500 mb-0.5">الكورس</p>
                    <p class="font-semibold text-gray-900"><?php echo e($liveSession->course?->title ?? 'جلسة عامة'); ?></p>
                </div>
            </div>
            <div class="flex items-start gap-3 p-4 rounded-xl bg-gray-50 border border-gray-100">
                <i class="fas fa-calendar text-amber-600 mt-0.5"></i>
                <div>
                    <p class="text-xs font-semibold text-gray-500 mb-0.5">الموعد المجدول</p>
                    <p class="font-semibold text-gray-900"><?php echo e($liveSession->scheduled_at?->format('Y/m/d — H:i') ?? '—'); ?></p>
                </div>
            </div>
            <div class="flex items-start gap-3 p-4 rounded-xl bg-gray-50 border border-gray-100">
                <i class="fas fa-info-circle text-violet-600 mt-0.5"></i>
                <div>
                    <p class="text-xs font-semibold text-gray-500 mb-0.5">الحالة</p>
                    <p class="font-semibold">
                        <?php if($liveSession->isLive()): ?>
                            <span class="text-red-600">مباشر</span>
                        <?php elseif($liveSession->isScheduled()): ?>
                            <span class="text-sky-600">مجدولة</span>
                        <?php else: ?>
                            <span class="text-gray-600">منتهية</span>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        </div>

        <?php if($liveSession->description): ?>
        <div class="pt-4 border-t border-gray-200">
            <h3 class="text-sm font-bold text-gray-700 mb-2">وصف الجلسة</h3>
            <div class="text-sm text-gray-600 leading-relaxed whitespace-pre-wrap"><?php echo e($liveSession->description); ?></div>
        </div>
        <?php endif; ?>
    </div>

    <?php if($liveSession->isScheduled()): ?>
    <div class="rounded-xl border border-sky-200 bg-sky-50 p-5 sm:p-6">
        <div class="flex gap-4">
            <div class="w-11 h-11 rounded-xl bg-white border border-sky-200 flex items-center justify-center shrink-0 text-sky-600">
                <i class="fas fa-clock text-lg"></i>
            </div>
            <div>
                <p class="font-bold text-sky-900">لم يبدأ البث بعد</p>
                <p class="text-sm text-sky-800/90 mt-1">الجلسة ستبدأ <?php echo e($liveSession->scheduled_at?->diffForHumans()); ?>. عند بدء المدرب ستجد زر الانضمام في هذه الصفحة أو من قائمة «مباشر الآن».</p>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if($liveSession->status === 'ended' && $liveSession->recordings && $liveSession->recordings->count() > 0): ?>
    <div class="bg-white rounded-xl border border-emerald-200 shadow-sm p-5 sm:p-6">
        <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
            <i class="fas fa-play-circle text-emerald-600"></i>
            تسجيلات الجلسة
        </h3>
        <ul class="space-y-2">
            <?php $__currentLoopData = $liveSession->recordings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rec): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li>
                <a href="<?php echo e(route('student.live-recordings.show', $rec)); ?>" class="flex items-center justify-between gap-3 p-4 rounded-xl bg-gray-50 border border-gray-200 hover:border-emerald-300 transition-colors">
                    <span class="font-medium text-gray-900"><?php echo e($rec->title ?? 'تسجيل #' . $rec->id); ?></span>
                    <span class="text-xs text-gray-500 shrink-0"><?php echo e($rec->duration_for_humans); ?></span>
                </a>
            </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
        <a href="<?php echo e(route('student.live-recordings.index')); ?>" class="inline-flex items-center gap-2 mt-4 text-sm font-semibold text-emerald-600 hover:text-emerald-700">
            كل التسجيلات
            <i class="fas fa-chevron-left text-xs"></i>
        </a>
    </div>
    <?php endif; ?>

    <a href="<?php echo e(route('student.live-sessions.index')); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl border border-gray-200 bg-white text-gray-700 text-sm font-semibold hover:border-sky-300 transition-colors">
        <i class="fas fa-list"></i>
        العودة لقائمة الجلسات
    </a>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sana\resources\views\student\live-sessions\show.blade.php ENDPATH**/ ?>