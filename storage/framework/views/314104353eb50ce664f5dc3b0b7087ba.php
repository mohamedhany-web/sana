<?php $__env->startSection('title', 'خدمات الموقع - ' . ($platformName ?? config('brand.name', config('app.name')))); ?>
<?php $__env->startSection('page_title', 'خدمات الموقع'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $totalServices = \App\Models\SiteService::count();
    $activeServices = \App\Models\SiteService::where('is_active', true)->count();
    $inactiveServices = max(0, $totalServices - $activeServices);
?>

<div class="admin-dashboard admin-list-page space-y-7">

    <?php echo $__env->make('admin.partials.alert-success', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php if (isset($component)) { $__componentOriginal2cbd75565df7390cff0c13630dbdb99a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2cbd75565df7390cff0c13630dbdb99a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.page-hero','data' => ['title' => 'خدمات الموقع','subtitle' => 'تظهر في الصفحة العامة /services وفي شريط التنقل. أضف الاسم والمقدمة والتفاصيل لكل خدمة.','icon' => 'fas fa-concierge-bell']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin.page-hero'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'خدمات الموقع','subtitle' => 'تظهر في الصفحة العامة /services وفي شريط التنقل. أضف الاسم والمقدمة والتفاصيل لكل خدمة.','icon' => 'fas fa-concierge-bell']); ?>
        <a href="<?php echo e(route('admin.site-services.create')); ?>" class="admin-btn admin-btn--primary">
            <i class="fas fa-plus"></i>
            خدمة جديدة
        </a>
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
        <div class="admin-mini-stat">
            <div class="admin-mini-stat__label">إجمالي الخدمات</div>
            <div class="admin-mini-stat__value"><?php echo e(number_format($totalServices)); ?></div>
            <div class="admin-mini-stat__meta">في قاعدة البيانات</div>
        </div>
        <div class="admin-mini-stat">
            <div class="admin-mini-stat__label">نشطة</div>
            <div class="admin-mini-stat__value"><?php echo e(number_format($activeServices)); ?></div>
            <div class="admin-mini-stat__meta">ظاهرة في الموقع</div>
        </div>
        <div class="admin-mini-stat <?php echo e($inactiveServices > 0 ? 'admin-mini-stat--highlight' : ''); ?>">
            <div class="admin-mini-stat__label">معطّلة</div>
            <div class="admin-mini-stat__value"><?php echo e(number_format($inactiveServices)); ?></div>
            <div class="admin-mini-stat__meta">مخفية عن الزوار</div>
        </div>
    </div>

    <div class="admin-panel">
        <div class="admin-panel__head">
            <h3><i class="fas fa-filter"></i> تصفية وبحث</h3>
        </div>
        <div class="admin-panel__body">
            <form method="GET" action="<?php echo e(route('admin.site-services.index')); ?>" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                <div class="admin-field md:col-span-1">
                    <label>بحث</label>
                    <input type="search" name="search" value="<?php echo e(request('search')); ?>" placeholder="الاسم أو الرابط..."
                           class="admin-input">
                </div>
                <div class="admin-field">
                    <label>الحالة</label>
                    <select name="status" class="admin-input">
                        <option value="">كل الحالات</option>
                        <option value="active" <?php if(request('status') === 'active'): echo 'selected'; endif; ?>>نشط</option>
                        <option value="inactive" <?php if(request('status') === 'inactive'): echo 'selected'; endif; ?>>معطل</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="admin-btn admin-btn--primary flex-1">
                        <i class="fas fa-filter"></i>
                        تصفية
                    </button>
                    <?php if(request()->anyFilled(['search', 'status'])): ?>
                        <a href="<?php echo e(route('admin.site-services.index')); ?>" class="admin-btn admin-btn--outline" title="مسح">
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
                <h2><i class="fas fa-list"></i> قائمة الخدمات</h2>
                <p class="admin-panel__sub"><?php echo e($services->total()); ?> خدمة</p>
            </div>
            <a href="<?php echo e(route('public.services.index')); ?>" target="_blank" rel="noopener" class="admin-btn admin-btn--outline text-xs">
                <i class="fas fa-external-link-alt"></i>
                معاينة الموقع
            </a>
        </div>

        <?php if($services->count() > 0): ?>
            <div class="admin-panel__body--flush overflow-x-auto">
                <table class="admin-data-table">
                    <thead>
                        <tr>
                            <th>الترتيب</th>
                            <th>صورة</th>
                            <th>الاسم</th>
                            <th>الرابط</th>
                            <th>الحالة</th>
                            <th class="text-center">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="<?php echo e(! $service->is_active ? '' : ''); ?>">
                                <td class="font-semibold text-slate-600"><?php echo e($service->sort_order); ?></td>
                                <td>
                                    <?php if($service->publicImageUrl()): ?>
                                        <img src="<?php echo e($service->publicImageUrl()); ?>" alt="" class="w-14 h-10 object-cover rounded-lg border border-slate-200">
                                    <?php else: ?>
                                        <span class="text-slate-300 text-xs">—</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <p class="font-bold text-slate-800"><?php echo e($service->name); ?></p>
                                    <?php if($service->summary): ?>
                                        <p class="text-xs text-slate-500 line-clamp-1 max-w-xs"><?php echo e($service->summary); ?></p>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="<?php echo e(route('public.services.show', $service)); ?>" target="_blank" rel="noopener"
                                       class="text-xs font-mono font-semibold" style="color: var(--admin-primary);">
                                        /services/<?php echo e($service->slug); ?>

                                    </a>
                                </td>
                                <td>
                                    <?php if($service->is_active): ?>
                                        <span class="admin-badge admin-badge--success">نشط</span>
                                    <?php else: ?>
                                        <span class="admin-badge admin-badge--warn">معطل</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="flex items-center justify-center gap-1.5">
                                        <a href="<?php echo e(route('admin.site-services.edit', $service)); ?>" class="admin-icon-btn" title="تعديل">
                                            <i class="fas fa-pen"></i>
                                        </a>
                                        <form action="<?php echo e(route('admin.site-services.destroy', $service)); ?>" method="POST" class="inline" onsubmit="return confirm('حذف هذه الخدمة؟');">
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
                <?php echo e($services->links()); ?>

            </div>
        <?php else: ?>
            <div class="admin-empty">
                <i class="fas fa-concierge-bell"></i>
                <p class="text-sm font-bold text-slate-600 mb-1">لا توجد خدمات</p>
                <p class="text-xs mb-3">ابدأ بإضافة أول خدمة للموقع العام.</p>
                <a href="<?php echo e(route('admin.site-services.create')); ?>" class="admin-btn admin-btn--primary">
                    <i class="fas fa-plus"></i>
                    إضافة خدمة
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sana\resources\views/admin/site-services/index.blade.php ENDPATH**/ ?>