<?php $__env->startSection('title', __('student.calendar_title')); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('dashboard.partials.sanua-theme', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/main.min.css" rel="stylesheet" />
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="sanua-dash">

    <header class="sanua-page-head">
        <div>
            <h1 class="sanua-page-head__title"><?php echo e(__('student.calendar_title')); ?></h1>
            <p class="sanua-page-head__sub"><?php echo e(__('student.calendar_subtitle')); ?></p>
        </div>
    </header>

    <div class="sanua-stats-row">
        <div class="sanua-stat-pill">
            <span class="sanua-stat-pill__icon sanua-stat-pill__icon--purple" aria-hidden="true">
                <i class="fas fa-calendar-alt"></i>
            </span>
            <div class="sanua-stat-pill__body">
                <strong><?php echo e($stats['total'] ?? 0); ?></strong>
                <span><?php echo e(__('student.total_events')); ?></span>
            </div>
        </div>
        <div class="sanua-stat-pill">
            <span class="sanua-stat-pill__icon sanua-stat-pill__icon--red" aria-hidden="true">
                <i class="fas fa-clipboard-check"></i>
            </span>
            <div class="sanua-stat-pill__body">
                <strong><?php echo e($stats['exams'] ?? 0); ?></strong>
                <span><?php echo e(__('student.legend_exams')); ?></span>
            </div>
        </div>
        <div class="sanua-stat-pill">
            <span class="sanua-stat-pill__icon sanua-stat-pill__icon--gold" aria-hidden="true">
                <i class="fas fa-chalkboard-teacher"></i>
            </span>
            <div class="sanua-stat-pill__body">
                <strong><?php echo e($stats['lectures'] ?? 0); ?></strong>
                <span><?php echo e(__('student.legend_lectures')); ?></span>
            </div>
        </div>
        <div class="sanua-stat-pill">
            <span class="sanua-stat-pill__icon sanua-stat-pill__icon--amber" aria-hidden="true">
                <i class="fas fa-arrow-up"></i>
            </span>
            <div class="sanua-stat-pill__body">
                <strong><?php echo e($stats['upcoming'] ?? 0); ?></strong>
                <span>قادمة</span>
            </div>
        </div>
    </div>

    <div class="sanua-calendar-layout">
        <div class="sanua-calendar-panel sanua-fc-wrap">
            <div id="calendar"></div>
            <div class="sanua-event-legend">
                <span><i style="background:#EF4444"></i><?php echo e(__('student.legend_exams')); ?></span>
                <span><i style="background:#8B5CF6"></i><?php echo e(__('student.legend_lectures')); ?></span>
                <span><i style="background:#F59E0B"></i><?php echo e(__('student.legend_assignments')); ?></span>
                <span><i style="background:#22C55E"></i><?php echo e(__('student.other_events')); ?></span>
            </div>
        </div>

        <aside class="sanua-calendar-sidebar">
            <div class="sanua-panel">
                <div class="sanua-panel__head">
                    <h3><i class="fas fa-clock text-violet-500 ml-1"></i> الأحداث القادمة</h3>
                </div>
                <div class="sanua-panel__body" style="display:flex;flex-direction:column;gap:8px;max-height:360px;overflow-y:auto;">
                    <?php $__empty_1 = true; $__currentLoopData = $events->where('start_date', '>=', now())->take(10); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="sanua-calendar-event" onclick="window.location.href='<?php echo e($event->url ?? '#'); ?>'">
                            <div class="sanua-calendar-event__title"><?php echo e($event->title); ?></div>
                            <div class="sanua-calendar-event__meta">
                                <i class="fas fa-calendar ml-1" style="color:#8B5CF6"></i>
                                <?php echo e($event->start_date->format('d/m/Y')); ?>

                                <?php if(! $event->is_all_day): ?>
                                    <?php echo e($event->start_date->format('h:i A')); ?>

                                <?php endif; ?>
                                ·
                                <?php if($event->type == 'exam'): ?> امتحان
                                <?php elseif($event->type == 'lecture'): ?> محاضرة
                                <?php elseif($event->type == 'assignment'): ?> واجب
                                <?php else: ?> حدث
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="text-sm text-slate-500 text-center py-6 m-0">لا توجد أحداث قادمة</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="sanua-calendar-stats">
                <h3><i class="fas fa-chart-pie ml-1"></i> إحصائيات</h3>
                <div class="sanua-calendar-stats__row">
                    <span><i class="fas fa-clipboard-check ml-1"></i> الامتحانات</span>
                    <strong><?php echo e($stats['exams'] ?? 0); ?></strong>
                </div>
                <div class="sanua-calendar-stats__row">
                    <span><i class="fas fa-chalkboard-teacher ml-1"></i> المحاضرات</span>
                    <strong><?php echo e($stats['lectures'] ?? 0); ?></strong>
                </div>
                <div class="sanua-calendar-stats__row">
                    <span><i class="fas fa-tasks ml-1"></i> الواجبات</span>
                    <strong><?php echo e($stats['assignments'] ?? 0); ?></strong>
                </div>
                <div class="sanua-calendar-stats__row">
                    <span><i class="fas fa-arrow-up ml-1"></i> القادمة</span>
                    <strong><?php echo e($stats['upcoming'] ?? 0); ?></strong>
                </div>
            </div>
        </aside>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/locales/ar.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        locale: 'ar',
        direction: 'rtl',
        initialView: 'dayGridMonth',
        headerToolbar: {
            right: 'prev,next today',
            center: 'title',
            left: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        buttonText: { today: 'اليوم', month: 'شهر', week: 'أسبوع', day: 'يوم' },
        events: {
            url: '<?php echo e(route("calendar.events")); ?>',
            failure: function() { alert('حدث خطأ في تحميل الأحداث'); }
        },
        eventClick: function(info) {
            if (info.event.url) {
                window.open(info.event.url, '_self');
                info.jsEvent.preventDefault();
            }
        },
        height: 'auto',
        contentHeight: 560,
        firstDay: 6,
        navLinks: true,
        dayMaxEvents: 3,
        moreLinkClick: 'popover'
    });
    calendar.render();
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sana\resources\views\student\calendar\index.blade.php ENDPATH**/ ?>