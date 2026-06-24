<?php $__env->startSection('title', __('student.notifications_title')); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('dashboard.partials.sanua-theme', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<?php
    $notifIconClass = fn ($color) => match ($color) {
        'blue' => 'sanua-notif-icon--blue',
        'green' => 'sanua-notif-icon--green',
        'yellow' => 'sanua-notif-icon--yellow',
        'red' => 'sanua-notif-icon--red',
        'purple' => 'sanua-notif-icon--purple',
        'orange' => 'sanua-notif-icon--orange',
        default => 'sanua-notif-icon--gray',
    };
    $priorityBadgeClass = fn ($color) => match ($color) {
        'red' => 'sanua-badge--danger',
        'yellow' => 'sanua-badge--pending',
        default => 'sanua-badge--locked',
    };
?>

<div class="sanua-dash">

    <header class="sanua-page-head">
        <div>
            <h1 class="sanua-page-head__title"><?php echo e(__('student.notifications_title')); ?></h1>
            <p class="sanua-page-head__sub">آخر التحديثات والرسائل المهمة من المنصة</p>
        </div>
        <div class="sanua-page-head__actions">
            <?php if($stats['unread'] > 0): ?>
                <button type="button" onclick="markAllAsRead()" class="sanua-page-head__btn">
                    <i class="fas fa-check-double"></i>
                    <?php echo e(__('student.mark_all_read')); ?>

                </button>
            <?php endif; ?>
            <button type="button" onclick="cleanup()" class="sanua-page-head__btn sanua-page-head__btn--ghost">
                <i class="fas fa-broom"></i>
                <?php echo e(__('student.cleanup_btn')); ?>

            </button>
        </div>
    </header>

    <div class="sanua-stats-row">
        <div class="sanua-stat-pill">
            <span class="sanua-stat-pill__icon sanua-stat-pill__icon--purple" aria-hidden="true">
                <i class="fas fa-bell"></i>
            </span>
            <div class="sanua-stat-pill__body">
                <strong><?php echo e($stats['total']); ?></strong>
                <span><?php echo e(__('student.total_notifications')); ?></span>
            </div>
        </div>
        <div class="sanua-stat-pill">
            <span class="sanua-stat-pill__icon sanua-stat-pill__icon--gold" aria-hidden="true">
                <i class="fas fa-envelope"></i>
            </span>
            <div class="sanua-stat-pill__body">
                <strong><?php echo e($stats['unread']); ?></strong>
                <span><?php echo e(__('student.unread_label')); ?></span>
            </div>
        </div>
        <div class="sanua-stat-pill">
            <span class="sanua-stat-pill__icon sanua-stat-pill__icon--green" aria-hidden="true">
                <i class="fas fa-calendar-day"></i>
            </span>
            <div class="sanua-stat-pill__body">
                <strong><?php echo e($stats['today']); ?></strong>
                <span><?php echo e(__('student.today_label')); ?></span>
            </div>
        </div>
        <div class="sanua-stat-pill">
            <span class="sanua-stat-pill__icon sanua-stat-pill__icon--red" aria-hidden="true">
                <i class="fas fa-exclamation-circle"></i>
            </span>
            <div class="sanua-stat-pill__body">
                <strong><?php echo e($stats['urgent']); ?></strong>
                <span><?php echo e(__('student.urgent_label')); ?></span>
            </div>
        </div>
    </div>

    <section class="sanua-section">
        <div class="sanua-panel">
            <div class="sanua-panel__head">
                <h3><i class="fas fa-filter ml-1"></i> تصفية الإشعارات</h3>
            </div>
            <div class="sanua-panel__body">
                <form method="GET" class="sanua-filter-form">
                    <div class="sanua-filter-form__field">
                        <label for="type"><?php echo e(__('student.notification_type_label')); ?></label>
                        <select name="type" id="type">
                            <option value=""><?php echo e(__('student.all_types')); ?></option>
                            <?php $__currentLoopData = $notificationTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($key); ?>" <?php echo e(request('type') == $key ? 'selected' : ''); ?>><?php echo e($type); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="sanua-filter-form__field">
                        <label for="status"><?php echo e(__('common.status')); ?></label>
                        <select name="status" id="status">
                            <option value=""><?php echo e(__('student.all_statuses')); ?></option>
                            <option value="unread" <?php echo e(request('status') == 'unread' ? 'selected' : ''); ?>><?php echo e(__('student.unread_label')); ?></option>
                            <option value="read" <?php echo e(request('status') == 'read' ? 'selected' : ''); ?>><?php echo e(__('student.read_filter')); ?></option>
                        </select>
                    </div>
                    <div class="sanua-filter-form__field">
                        <label for="priority"><?php echo e(__('student.priority_label')); ?></label>
                        <select name="priority" id="priority">
                            <option value=""><?php echo e(__('student.all_priorities')); ?></option>
                            <?php $__currentLoopData = $priorities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $priority): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($key); ?>" <?php echo e(request('priority') == $key ? 'selected' : ''); ?>><?php echo e($priority); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="sanua-filter-form__field">
                        <button type="submit" class="sanua-btn sanua-btn--purple" style="width:100%;justify-content:center;">
                            <i class="fas fa-search"></i>
                            <?php echo e(__('student.filter_btn')); ?>

                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <?php if($notifications->count() > 0): ?>
        <section class="sanua-section">
            <div class="sanua-notification-list">
                <?php $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <article class="sanua-notification-card <?php echo e(!$notification->is_read ? 'sanua-notification-card--unread' : ''); ?>">
                        <div class="sanua-notification-card__row">
                            <div class="sanua-notification-card__main">
                                <span class="sanua-notif-icon <?php echo e($notifIconClass($notification->type_color)); ?>" aria-hidden="true">
                                    <i class="<?php echo e($notification->type_icon); ?>"></i>
                                </span>
                                <div class="sanua-notification-card__content">
                                    <div class="sanua-notification-card__head">
                                        <h3 class="sanua-notification-card__title"><?php echo e($notification->title); ?></h3>
                                        <?php if($notification->priority !== 'normal'): ?>
                                            <span class="sanua-badge <?php echo e($priorityBadgeClass($notification->priority_color)); ?>">
                                                <?php echo e($priorities[$notification->priority] ?? $notification->priority); ?>

                                            </span>
                                        <?php endif; ?>
                                        <?php if(!$notification->is_read): ?>
                                            <span class="sanua-badge sanua-badge--submitted">
                                                <span class="sanua-badge__dot"></span> جديد
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <p class="sanua-notification-card__message"><?php echo e($notification->message); ?></p>
                                    <div class="sanua-notification-card__meta">
                                        <span><i class="fas fa-user"></i> من: <?php echo e($notification->sender->name ?? 'النظام'); ?></span>
                                        <span><i class="fas fa-clock"></i> <?php echo e($notification->created_at->diffForHumans()); ?></span>
                                        <?php if($notification->expires_at): ?>
                                            <span><i class="fas fa-hourglass-end"></i> ينتهي <?php echo e($notification->expires_at->diffForHumans()); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <?php if($notification->action_url && $notification->action_text): ?>
                                        <a href="<?php echo e(route('notifications.go', $notification)); ?>" class="sanua-notification-card__link">
                                            <?php echo e($notification->action_text); ?>

                                            <i class="fas fa-external-link-alt"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="sanua-notification-card__actions">
                                <?php if(!$notification->is_read): ?>
                                    <button type="button" onclick="markAsRead(<?php echo e($notification->id); ?>)" class="sanua-icon-btn sanua-icon-btn--read" title="تحديد كمقروء">
                                        <i class="fas fa-check"></i>
                                    </button>
                                <?php endif; ?>
                                <a href="<?php echo e(route('notifications.show', $notification)); ?>" class="sanua-icon-btn sanua-icon-btn--view" title="عرض">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <button type="button" onclick="deleteNotification(<?php echo e($notification->id); ?>)" class="sanua-icon-btn sanua-icon-btn--delete" title="حذف">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </article>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </section>

        <?php if($notifications->hasPages()): ?>
            <div class="sanua-pagination"><?php echo e($notifications->appends(request()->query())->links()); ?></div>
        <?php endif; ?>
    <?php else: ?>
        <div class="sanua-empty">
            <div class="sanua-empty__icon"><i class="fas fa-bell-slash"></i></div>
            <h3>لا توجد إشعارات</h3>
            <p>ستظهر هنا آخر التحديثات والرسائل المهمة</p>
        </div>
    <?php endif; ?>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function markAsRead(notificationId) {
    fetch(`/notifications/${notificationId}/mark-read`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function markAllAsRead() {
    if (confirm('هل تريد تحديد جميع الإشعارات كمقروءة؟')) {
        fetch('/notifications/mark-all-read', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
}

function deleteNotification(notificationId) {
    if (confirm('هل تريد حذف هذا الإشعار؟')) {
        fetch(`/notifications/${notificationId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
}

function cleanup() {
    if (confirm('هل تريد حذف الإشعارات المقروءة الأقدم من 30 يوم؟')) {
        fetch('/notifications/cleanup', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sana\resources\views\student\notifications\index.blade.php ENDPATH**/ ?>