<?php $__env->startSection('title', 'جلسات البث المباشر'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('dashboard.partials.sanua-theme', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<?php
    $statusFilter = request('status');
    $liveCount = $liveSessions->count();
    $listScheduled = $sessions->getCollection()->where('status', 'scheduled');
    $scheduledOnPage = $listScheduled->count();
?>

<div class="sanua-dash">

    <header class="sanua-page-head">
        <div>
            <h1 class="sanua-page-head__title">جلسات البث المباشر</h1>
            <p class="sanua-page-head__sub">الجلسات المباشرة والمجدولة المتاحة لك وفق كورساتك</p>
        </div>
        <div class="sanua-page-head__actions">
            <?php if(Route::has('student.live-recordings.index')): ?>
                <a href="<?php echo e(route('student.live-recordings.index')); ?>" class="sanua-page-head__btn sanua-page-head__btn--ghost">
                    <i class="fas fa-play-circle"></i>
                    التسجيلات
                </a>
            <?php endif; ?>
            <a href="<?php echo e(route('my-courses.index')); ?>" class="sanua-page-head__btn">
                <i class="fas fa-book-open"></i>
                كورساتي
            </a>
        </div>
    </header>

    <div class="sanua-stats-row">
        <div class="sanua-stat-pill">
            <span class="sanua-stat-pill__icon sanua-stat-pill__icon--purple" aria-hidden="true">
                <i class="fas fa-broadcast-tower"></i>
            </span>
            <div class="sanua-stat-pill__body">
                <strong><?php echo e($sessions->total()); ?></strong>
                <span>إجمالي الجلسات</span>
            </div>
        </div>
        <div class="sanua-stat-pill">
            <span class="sanua-stat-pill__icon sanua-stat-pill__icon--red" aria-hidden="true">
                <i class="fas fa-circle"></i>
            </span>
            <div class="sanua-stat-pill__body">
                <strong><?php echo e($liveCount); ?></strong>
                <span>مباشر الآن</span>
            </div>
        </div>
        <div class="sanua-stat-pill">
            <span class="sanua-stat-pill__icon sanua-stat-pill__icon--gold" aria-hidden="true">
                <i class="fas fa-calendar-alt"></i>
            </span>
            <div class="sanua-stat-pill__body">
                <strong><?php echo e($scheduledOnPage); ?></strong>
                <span>مجدولة (هذه الصفحة)</span>
            </div>
        </div>
        <div class="sanua-stat-pill">
            <span class="sanua-stat-pill__icon sanua-stat-pill__icon--amber" aria-hidden="true">
                <i class="fas fa-video"></i>
            </span>
            <div class="sanua-stat-pill__body">
                <strong><?php echo e($sessions->currentPage()); ?>/<?php echo e(max(1, $sessions->lastPage())); ?></strong>
                <span>صفحة العرض</span>
            </div>
        </div>
    </div>

    <?php if(session('success')): ?>
        <div class="sanua-flash sanua-flash--success" role="alert"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="sanua-flash sanua-flash--error" role="alert"><?php echo e(session('error')); ?></div>
    <?php endif; ?>

    <div class="sanua-filter-tabs">
        <a href="<?php echo e(route('student.live-sessions.index')); ?>"
           class="sanua-filter-tab <?php echo e(! $statusFilter ? 'is-active' : ''); ?>">الكل</a>
        <a href="<?php echo e(route('student.live-sessions.index', ['status' => 'live'])); ?>"
           class="sanua-filter-tab is-live <?php echo e($statusFilter === 'live' ? 'is-active' : ''); ?>">
            <span class="sanua-filter-tab__dot"></span>
            مباشر
        </a>
        <a href="<?php echo e(route('student.live-sessions.index', ['status' => 'scheduled'])); ?>"
           class="sanua-filter-tab <?php echo e($statusFilter === 'scheduled' ? 'is-active' : ''); ?>">مجدولة</a>
    </div>

    <?php if($liveSessions->count() > 0 && (! $statusFilter || $statusFilter === 'live')): ?>
        <section class="sanua-section">
            <h2 class="sanua-section-label">
                <span class="sanua-section-label__pulse"></span>
                مباشر الآن
            </h2>

            <?php $__currentLoopData = $liveSessions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $live): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="sanua-live-card">
                    <div class="sanua-live-card__main">
                        <div class="sanua-live-card__badges">
                            <span class="sanua-badge sanua-badge--live">
                                <span class="sanua-badge__dot"></span>
                                مباشر
                            </span>
                            <?php if($live->course): ?>
                                <span class="sanua-badge sanua-badge--course"><?php echo e($live->course->title); ?></span>
                            <?php endif; ?>
                        </div>
                        <h3 class="sanua-live-card__title"><?php echo e($live->title); ?></h3>
                        <p class="sanua-live-card__meta">
                            <i class="fas fa-chalkboard-teacher"></i>
                            <?php echo e($live->instructor?->name ?? '—'); ?>

                            <?php if($live->started_at): ?>
                                · بدأ <?php echo e($live->started_at->diffForHumans()); ?>

                            <?php endif; ?>
                        </p>
                    </div>
                    <form method="POST" action="<?php echo e(route('student.live-sessions.join', $live)); ?>" class="shrink-0">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="sanua-btn sanua-btn--live">
                            <i class="fas fa-video"></i>
                            انضم الآن
                        </button>
                    </form>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </section>
    <?php endif; ?>

    <?php if($statusFilter !== 'live'): ?>
        <section class="sanua-section">
            <h2 class="sanua-section-title">
                📅 <?php echo e($statusFilter === 'scheduled' ? 'الجلسات المجدولة' : 'الجلسات القادمة'); ?>

            </h2>

            <?php
                $listSessions = $sessions->filter(fn ($s) => $s->status !== 'live');
            ?>

            <?php if($listSessions->isEmpty()): ?>
                <div class="sanua-empty">
                    <div class="sanua-empty__icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <h3>لا توجد جلسات مجدولة <?php echo e($statusFilter === 'scheduled' ? 'حالياً' : 'في هذه الصفحة'); ?></h3>
                    <p>ستُعرض الجلسات عند جدولتها من قبل المدرب</p>
                    <a href="<?php echo e(route('my-courses.index')); ?>" class="sanua-empty__btn">
                        <i class="fas fa-book-open"></i>
                        تصفح كورساتي
                    </a>
                </div>
            <?php else: ?>
                <div class="sanua-session-list">
                    <?php $__currentLoopData = $sessions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $session): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($session->status !== 'live'): ?>
                            <a href="<?php echo e(route('student.live-sessions.show', $session)); ?>" class="sanua-session-card">
                                <div class="sanua-session-card__row">
                                    <div class="sanua-session-card__main">
                                        <div class="sanua-live-card__badges">
                                            <span class="sanua-badge sanua-badge--scheduled">مجدولة</span>
                                            <?php if($session->course): ?>
                                                <span class="sanua-badge sanua-badge--course"><?php echo e($session->course->title); ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <h3 class="sanua-session-card__title"><?php echo e($session->title); ?></h3>
                                        <div class="sanua-session-card__details">
                                            <span><i class="fas fa-chalkboard-teacher"></i><?php echo e($session->instructor?->name ?? '—'); ?></span>
                                            <span><i class="fas fa-calendar"></i><?php echo e($session->scheduled_at?->format('Y/m/d')); ?></span>
                                            <span><i class="fas fa-clock"></i><?php echo e($session->scheduled_at?->format('H:i')); ?></span>
                                        </div>
                                        <?php if($session->description): ?>
                                            <p class="sanua-session-card__desc"><?php echo e(Str::limit($session->description, 120)); ?></p>
                                        <?php endif; ?>
                                    </div>
                                    <span class="sanua-session-card__action">
                                        التفاصيل
                                        <i class="fas fa-chevron-left"></i>
                                    </span>
                                </div>
                            </a>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>
        </section>
    <?php elseif($liveSessions->isEmpty()): ?>
        <div class="sanua-empty">
            <div class="sanua-empty__icon">
                <i class="fas fa-broadcast-tower"></i>
            </div>
            <h3>لا توجد جلسات مباشرة حالياً</h3>
            <p>عند بدء المدرب للبث ستظهر الجلسة في أعلى الصفحة</p>
            <a href="<?php echo e(route('my-courses.index')); ?>" class="sanua-empty__btn">
                <i class="fas fa-book-open"></i>
                كورساتي
            </a>
        </div>
    <?php endif; ?>

    <?php if($sessions->hasPages()): ?>
        <div class="sanua-pagination">
            <?php echo e($sessions->links()); ?>

        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sana\resources\views\student\live-sessions\index.blade.php ENDPATH**/ ?>