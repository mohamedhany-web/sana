<?php $__env->startSection('title', 'وارد الإشعارات - ' . ($platformName ?? config('brand.name', config('app.name')))); ?>
<?php $__env->startSection('page_title', 'وارد الإشعارات'); ?>

<?php $__env->startSection('content'); ?>
<div class="admin-dashboard admin-list-page space-y-7">

    <?php if (isset($component)) { $__componentOriginal2cbd75565df7390cff0c13630dbdb99a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2cbd75565df7390cff0c13630dbdb99a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.page-hero','data' => ['title' => 'وارد الإشعارات','subtitle' => 'التنبيهات الموجهة لحسابك (مثل تذاكر الدعم). «مركز الإشعارات» في القائمة مخصص لإرسال تنبيهات للطلاب.','icon' => 'fas fa-inbox']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin.page-hero'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'وارد الإشعارات','subtitle' => 'التنبيهات الموجهة لحسابك (مثل تذاكر الدعم). «مركز الإشعارات» في القائمة مخصص لإرسال تنبيهات للطلاب.','icon' => 'fas fa-inbox']); ?>
        <?php if($stats['unread'] > 0): ?>
            <form action="<?php echo e(route('admin.notifications.inbox.mark-all-read')); ?>" method="post" class="inline" id="inbox-mark-all-form">
                <?php echo csrf_field(); ?>
                <button type="submit" class="admin-btn admin-btn--ghost">
                    <i class="fas fa-check-double"></i>
                    تعيين الكل كمقروء
                </button>
            </form>
        <?php endif; ?>
        <?php if(auth()->user()->isSuperAdmin()): ?>
            <a href="<?php echo e(route('admin.notifications.index')); ?>" class="admin-btn admin-btn--primary">
                <i class="fas fa-paper-plane"></i>
                إرسال للطلاب
            </a>
        <?php endif; ?>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2cbd75565df7390cff0c13630dbdb99a)): ?>
<?php $attributes = $__attributesOriginal2cbd75565df7390cff0c13630dbdb99a; ?>
<?php unset($__attributesOriginal2cbd75565df7390cff0c13630dbdb99a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2cbd75565df7390cff0c13630dbdb99a)): ?>
<?php $component = $__componentOriginal2cbd75565df7390cff0c13630dbdb99a; ?>
<?php unset($__componentOriginal2cbd75565df7390cff0c13630dbdb99a); ?>
<?php endif; ?>

    <div class="admin-mini-stats admin-mini-stats--3">
        <div class="admin-mini-stat <?php echo e($stats['unread'] > 0 ? 'admin-mini-stat--highlight' : ''); ?>">
            <div class="admin-mini-stat__label">غير مقروء</div>
            <div class="admin-mini-stat__value"><?php echo e(number_format($stats['unread'])); ?></div>
            <div class="admin-mini-stat__meta">يحتاج مراجعة</div>
        </div>
        <div class="admin-mini-stat">
            <div class="admin-mini-stat__label">الإجمالي</div>
            <div class="admin-mini-stat__value"><?php echo e(number_format($stats['total'])); ?></div>
            <div class="admin-mini-stat__meta">كل الإشعارات</div>
        </div>
        <div class="admin-mini-stat">
            <div class="admin-mini-stat__label">مقروء</div>
            <div class="admin-mini-stat__value"><?php echo e(number_format(max(0, $stats['total'] - $stats['unread']))); ?></div>
            <div class="admin-mini-stat__meta">تمت معالجتها</div>
        </div>
    </div>

    <div class="admin-panel">
        <div class="admin-panel__head">
            <div>
                <h2><i class="fas fa-list"></i> قائمة الإشعارات</h2>
                <p class="admin-panel__sub"><?php echo e($notifications->total()); ?> إشعار في هذه الصفحة</p>
            </div>
        </div>

        <div class="admin-filter-tabs">
            <a href="<?php echo e(route('admin.notifications.inbox')); ?>"
               class="admin-filter-tab <?php echo e(! request()->filled('status') ? 'is-active' : ''); ?>">الكل</a>
            <a href="<?php echo e(route('admin.notifications.inbox', ['status' => 'unread'])); ?>"
               class="admin-filter-tab <?php echo e(request('status') === 'unread' ? 'is-active' : ''); ?>">غير مقروء فقط</a>
            <a href="<?php echo e(route('admin.notifications.inbox', ['status' => 'read'])); ?>"
               class="admin-filter-tab <?php echo e(request('status') === 'read' ? 'is-active' : ''); ?>">مقروء</a>
        </div>

        <div class="admin-panel__body--flush">
            <?php $__empty_1 = true; $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    $notificationHref = $notification->action_url ?: route('admin.notifications.show', $notification);
                ?>
                <div class="admin-inbox-item <?php echo e(! $notification->is_read ? 'is-unread' : ''); ?>">
                    <a href="<?php echo e($notificationHref); ?>"
                       class="admin-inbox-item__link"
                       data-turbo="false">
                        <span class="admin-inbox-item__icon <?php echo e($notification->is_read ? 'admin-inbox-item__icon--read' : 'admin-inbox-item__icon--unread'); ?>">
                            <i class="<?php echo e($notification->type_icon); ?>"></i>
                        </span>
                        <span class="min-w-0 flex-1">
                            <span class="block text-sm font-bold text-slate-800 truncate"><?php echo e($notification->title); ?></span>
                            <span class="block text-xs text-slate-500 mt-1 line-clamp-2"><?php echo e($notification->message); ?></span>
                            <span class="block text-[10px] text-slate-400 mt-1.5"><?php echo e($notification->created_at->diffForHumans()); ?></span>
                        </span>
                        <?php if(! $notification->is_read): ?>
                            <span class="admin-inbox-item__dot" title="غير مقروء"></span>
                        <?php endif; ?>
                    </a>
                    <form action="<?php echo e(route('admin.notifications.inbox.destroy', $notification)); ?>"
                          method="post"
                          class="admin-inbox-item__delete"
                          data-turbo="false"
                          onsubmit="return confirm('هل تريد حذف هذا الإشعار من الوارد؟');">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="admin-inbox-item__delete-btn" title="حذف" aria-label="حذف الإشعار">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </form>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="admin-empty">
                    <i class="fas fa-inbox"></i>
                    <p class="text-sm font-medium">لا توجد إشعارات في الوارد حالياً.</p>
                </div>
            <?php endif; ?>
        </div>

        <?php if($notifications->hasPages()): ?>
            <div class="admin-pagination">
                <?php echo e($notifications->links()); ?>

            </div>
        <?php endif; ?>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
document.getElementById('inbox-mark-all-form')?.addEventListener('submit', function (e) {
    e.preventDefault();
    var form = this;
    var token = document.querySelector('meta[name="csrf-token"]');
    fetch(form.action, {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': token ? token.getAttribute('content') : ''
        },
        body: new FormData(form),
        credentials: 'same-origin'
    }).then(function () { window.location.reload(); }).catch(function () { form.submit(); });
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sana\resources\views/admin/notifications/inbox.blade.php ENDPATH**/ ?>