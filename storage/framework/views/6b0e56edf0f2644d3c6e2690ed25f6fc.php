<?php $__env->startSection('title', 'رسائل التواصل - ' . ($platformName ?? config('brand.name', config('app.name')))); ?>
<?php $__env->startSection('page_title', 'رسائل التواصل'); ?>

<?php $__env->startSection('content'); ?>
<div class="admin-dashboard admin-list-page space-y-7">

    <?php if (isset($component)) { $__componentOriginal2cbd75565df7390cff0c13630dbdb99a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2cbd75565df7390cff0c13630dbdb99a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.page-hero','data' => ['title' => 'رسائل التواصل','subtitle' => 'عرض وإدارة رسائل الزوار من نموذج التواصل في الموقع العام.','icon' => 'fas fa-envelope-open-text']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin.page-hero'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'رسائل التواصل','subtitle' => 'عرض وإدارة رسائل الزوار من نموذج التواصل في الموقع العام.','icon' => 'fas fa-envelope-open-text']); ?>
        <?php if($stats['unread'] > 0): ?>
            <span class="admin-alert-inline">
                <i class="fas fa-circle text-[6px]"></i>
                <?php echo e(number_format($stats['unread'])); ?> غير مقروءة
            </span>
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

    <div class="admin-mini-stats">
        <div class="admin-mini-stat">
            <div class="admin-mini-stat__label">إجمالي الرسائل</div>
            <div class="admin-mini-stat__value"><?php echo e(number_format($stats['total'])); ?></div>
            <div class="admin-mini-stat__meta">جميع الرسائل</div>
        </div>
        <div class="admin-mini-stat <?php echo e($stats['unread'] > 0 ? 'admin-mini-stat--highlight' : ''); ?>">
            <div class="admin-mini-stat__label">غير مقروءة</div>
            <div class="admin-mini-stat__value"><?php echo e(number_format($stats['unread'])); ?></div>
            <div class="admin-mini-stat__meta">تحتاج مراجعة</div>
        </div>
        <div class="admin-mini-stat">
            <div class="admin-mini-stat__label">مقروءة</div>
            <div class="admin-mini-stat__value"><?php echo e(number_format($stats['read'])); ?></div>
            <div class="admin-mini-stat__meta">تمت المعالجة</div>
        </div>
        <div class="admin-mini-stat">
            <div class="admin-mini-stat__label">رسائل اليوم</div>
            <div class="admin-mini-stat__value"><?php echo e(number_format($stats['today'])); ?></div>
            <div class="admin-mini-stat__meta">وصلت اليوم</div>
        </div>
    </div>

    <div class="admin-panel">
        <div class="admin-panel__head">
            <h3><i class="fas fa-filter"></i> فلترة وبحث</h3>
        </div>
        <div class="admin-panel__body">
            <form method="GET" action="<?php echo e(route('admin.contact-messages.index')); ?>" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                <div class="admin-field">
                    <label>البحث</label>
                    <input type="text" name="search" value="<?php echo e(request('search')); ?>"
                           placeholder="الاسم، البريد، أو الموضوع..."
                           class="admin-input">
                </div>
                <div class="admin-field">
                    <label>الحالة</label>
                    <select name="status" class="admin-input">
                        <option value="">جميع الرسائل</option>
                        <option value="unread" <?php if(request('status') === 'unread'): echo 'selected'; endif; ?>>غير مقروءة</option>
                        <option value="read" <?php if(request('status') === 'read'): echo 'selected'; endif; ?>>مقروءة</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="admin-btn admin-btn--primary flex-1">
                        <i class="fas fa-search"></i>
                        بحث
                    </button>
                    <?php if(request()->anyFilled(['search', 'status'])): ?>
                        <a href="<?php echo e(route('admin.contact-messages.index')); ?>" class="admin-btn admin-btn--outline" title="مسح الفلتر">
                            <i class="fas fa-times"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <div class="admin-panel">
        <div class="admin-panel__head">
            <div>
                <h2><i class="fas fa-list"></i> سجل الرسائل</h2>
                <p class="admin-panel__sub"><span class="font-bold text-[var(--admin-primary)]"><?php echo e($messages->total()); ?></span> رسالة</p>
            </div>
            <span class="text-xs text-slate-400">آخر تحديث <?php echo e(now()->format('H:i')); ?></span>
        </div>

        <?php if($messages->count() > 0): ?>
            <div class="admin-panel__body--flush overflow-x-auto">
                <table class="admin-data-table">
                    <thead>
                        <tr>
                            <th>المرسل</th>
                            <th>الموضوع</th>
                            <th>الحالة</th>
                            <th>التاريخ</th>
                            <th class="text-center">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $messages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $message): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="<?php echo e(! $message->read_at ? 'is-unread' : ''); ?>">
                                <td>
                                    <div class="flex items-center gap-3">
                                        <span class="admin-avatar-sm"><?php echo e(mb_substr($message->name, 0, 1, 'UTF-8')); ?></span>
                                        <div class="min-w-0">
                                            <p class="font-bold text-slate-800 truncate"><?php echo e($message->name); ?></p>
                                            <p class="text-xs text-slate-500 truncate"><?php echo e($message->email); ?></p>
                                            <?php if($message->phone): ?>
                                                <p class="text-xs text-slate-400"><?php echo e($message->phone); ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="max-w-xs">
                                    <p class="font-semibold text-slate-800 mb-0.5"><?php echo e($message->subject); ?></p>
                                    <p class="text-xs text-slate-500 line-clamp-2"><?php echo e(Str::limit($message->message, 100)); ?></p>
                                    <?php if(strlen($message->message) > 100): ?>
                                        <a href="<?php echo e(route('admin.contact-messages.show', $message)); ?>" class="text-xs font-semibold mt-1 inline-block" style="color: var(--admin-primary);">قراءة المزيد</a>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($message->read_at): ?>
                                        <span class="admin-badge admin-badge--success"><i class="fas fa-check-circle"></i> مقروءة</span>
                                    <?php else: ?>
                                        <span class="admin-badge admin-badge--danger"><i class="fas fa-circle text-[6px]"></i> غير مقروءة</span>
                                    <?php endif; ?>
                                </td>
                                <td class="whitespace-nowrap">
                                    <p class="font-semibold text-slate-700"><?php echo e($message->created_at->format('d/m/Y')); ?></p>
                                    <p class="text-xs text-slate-400"><?php echo e($message->created_at->format('H:i')); ?></p>
                                </td>
                                <td>
                                    <div class="flex items-center justify-center gap-1.5">
                                        <a href="<?php echo e(route('admin.contact-messages.show', $message)); ?>" class="admin-icon-btn" title="عرض">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <?php if($message->read_at): ?>
                                            <form action="<?php echo e(route('admin.contact-messages.mark-as-unread', $message)); ?>" method="POST" class="inline">
                                                <?php echo csrf_field(); ?>
                                                <button type="submit" class="admin-icon-btn" title="غير مقروءة">
                                                    <i class="fas fa-envelope"></i>
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <form action="<?php echo e(route('admin.contact-messages.mark-as-read', $message)); ?>" method="POST" class="inline">
                                                <?php echo csrf_field(); ?>
                                                <button type="submit" class="admin-icon-btn admin-icon-btn--success" title="مقروءة">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                        <form action="<?php echo e(route('admin.contact-messages.destroy', $message)); ?>" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذه الرسالة؟')">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="admin-icon-btn admin-icon-btn--danger" title="حذف">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            <div class="admin-pagination">
                <?php echo e($messages->withQueryString()->links()); ?>

            </div>
        <?php else: ?>
            <div class="admin-empty">
                <i class="fas fa-inbox"></i>
                <p class="text-sm font-bold text-slate-600 mb-1">لا توجد رسائل</p>
                <p class="text-xs">لم يُستلم أي رسالة تواصل بعد.</p>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sana\resources\views\admin\contact-messages\index.blade.php ENDPATH**/ ?>