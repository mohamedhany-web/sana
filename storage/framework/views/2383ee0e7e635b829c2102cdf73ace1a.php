<?php $__env->startSection('title', 'آراء الموقع - ' . ($platformName ?? config('brand.name', config('app.name')))); ?>
<?php $__env->startSection('page_title', 'آراء الموقع'); ?>

<?php $__env->startSection('content'); ?>
<?php
    use App\Models\SiteTestimonial;
    $totalCount = SiteTestimonial::count();
    $activeCount = SiteTestimonial::where('is_active', true)->count();
    $featuredCount = SiteTestimonial::where('is_featured', true)->count();
    $imageCount = SiteTestimonial::where('content_type', SiteTestimonial::CONTENT_IMAGE)->count();
?>

<div class="admin-dashboard admin-list-page space-y-7">

    <?php echo $__env->make('admin.partials.alert-success', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php if (isset($component)) { $__componentOriginal2cbd75565df7390cff0c13630dbdb99a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2cbd75565df7390cff0c13630dbdb99a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.page-hero','data' => ['title' => 'آراء وتجارب المعلمين','subtitle' => 'تظهر في الصفحة الرئيسية (تمرير تلقائي) وفي صفحة الآراء العامة. يمكنك إضافة اقتباس نصي أو شهادة كصورة.','icon' => 'fas fa-quote-right']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin.page-hero'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'آراء وتجارب المعلمين','subtitle' => 'تظهر في الصفحة الرئيسية (تمرير تلقائي) وفي صفحة الآراء العامة. يمكنك إضافة اقتباس نصي أو شهادة كصورة.','icon' => 'fas fa-quote-right']); ?>
        <a href="<?php echo e(route('admin.site-testimonials.create')); ?>" class="admin-btn admin-btn--primary">
            <i class="fas fa-plus"></i>
            رأي جديد
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

    <div class="admin-mini-stats">
        <div class="admin-mini-stat">
            <div class="admin-mini-stat__label">إجمالي الآراء</div>
            <div class="admin-mini-stat__value"><?php echo e(number_format($totalCount)); ?></div>
            <div class="admin-mini-stat__meta">في قاعدة البيانات</div>
        </div>
        <div class="admin-mini-stat">
            <div class="admin-mini-stat__label">نشطة</div>
            <div class="admin-mini-stat__value"><?php echo e(number_format($activeCount)); ?></div>
            <div class="admin-mini-stat__meta">ظاهرة للزوار</div>
        </div>
        <div class="admin-mini-stat <?php echo e($featuredCount > 0 ? 'admin-mini-stat--highlight' : ''); ?>">
            <div class="admin-mini-stat__label">بطاقات مميزة</div>
            <div class="admin-mini-stat__value"><?php echo e(number_format($featuredCount)); ?></div>
            <div class="admin-mini-stat__meta">خلفية كحلية في الرئيسية</div>
        </div>
        <div class="admin-mini-stat">
            <div class="admin-mini-stat__label">شهادات صورة</div>
            <div class="admin-mini-stat__value"><?php echo e(number_format($imageCount)); ?></div>
            <div class="admin-mini-stat__meta">نوع «صورة»</div>
        </div>
    </div>

    <div class="admin-panel">
        <div class="admin-panel__head">
            <h3><i class="fas fa-filter"></i> تصفية وبحث</h3>
        </div>
        <div class="admin-panel__body">
            <form method="GET" action="<?php echo e(route('admin.site-testimonials.index')); ?>" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                <div class="admin-field md:col-span-1">
                    <label>بحث</label>
                    <input type="search" name="search" value="<?php echo e(request('search')); ?>" placeholder="النص أو اسم صاحب الرأي..."
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
                        <a href="<?php echo e(route('admin.site-testimonials.index')); ?>" class="admin-btn admin-btn--outline" title="مسح">
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
                <h2><i class="fas fa-list"></i> قائمة الآراء</h2>
                <p class="admin-panel__sub"><?php echo e($rows->total()); ?> رأي</p>
            </div>
            <a href="<?php echo e(route('public.testimonials')); ?>" target="_blank" rel="noopener" class="admin-btn admin-btn--outline text-xs">
                <i class="fas fa-external-link-alt"></i>
                معاينة صفحة الآراء
            </a>
        </div>

        <?php if($rows->count() > 0): ?>
            <div class="admin-panel__body--flush overflow-x-auto">
                <table class="admin-data-table">
                    <thead>
                        <tr>
                            <th>الترتيب</th>
                            <th>المعاينة</th>
                            <th>النوع</th>
                            <th>صاحب الرأي</th>
                            <th>مميز</th>
                            <th>الحالة</th>
                            <th class="text-center">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="<?php echo e(! $row->is_active ? 'opacity-75' : ''); ?>">
                                <td class="font-semibold text-slate-600 tabular-nums"><?php echo e($row->sort_order); ?></td>
                                <td class="min-w-[140px]">
                                    <?php if($row->isImageType() && $row->publicImageUrl()): ?>
                                        <div class="flex items-center gap-3">
                                            <img src="<?php echo e($row->publicImageUrl()); ?>" alt="" class="w-16 h-12 object-cover rounded-lg border border-slate-200 shadow-sm">
                                            <?php if($row->body): ?>
                                                <p class="text-xs text-slate-500 line-clamp-2 max-w-[10rem]"><?php echo e(Str::limit(strip_tags($row->body), 60)); ?></p>
                                            <?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        <p class="text-sm text-slate-700 line-clamp-3 max-w-xs leading-relaxed">
                                            <i class="fas fa-quote-right text-slate-300 text-xs ml-1"></i>
                                            <?php echo e(Str::limit(strip_tags($row->body ?? ''), 120)); ?>

                                        </p>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($row->content_type === SiteTestimonial::CONTENT_IMAGE): ?>
                                        <span class="admin-badge admin-badge--warn"><i class="fas fa-image ml-1"></i> صورة</span>
                                    <?php else: ?>
                                        <span class="admin-badge" style="background: rgba(29, 78, 219, 0.1); color: var(--admin-primary);"><i class="fas fa-align-right ml-1"></i> نص</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <p class="font-bold text-slate-800"><?php echo e($row->author_name ?: '—'); ?></p>
                                    <?php if($row->role_label): ?>
                                        <p class="text-xs text-slate-500 mt-0.5"><?php echo e($row->role_label); ?></p>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($row->is_featured): ?>
                                        <span class="inline-flex items-center gap-1 text-amber-600 font-semibold text-sm" title="بطاقة مميزة">
                                            <i class="fas fa-star"></i>
                                            مميز
                                        </span>
                                    <?php else: ?>
                                        <span class="text-slate-300">—</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($row->is_active): ?>
                                        <span class="admin-badge admin-badge--success">نشط</span>
                                    <?php else: ?>
                                        <span class="admin-badge admin-badge--warn">معطل</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="flex items-center justify-center gap-1.5">
                                        <a href="<?php echo e(route('admin.site-testimonials.edit', $row)); ?>" class="admin-icon-btn" title="تعديل">
                                            <i class="fas fa-pen"></i>
                                        </a>
                                        <form action="<?php echo e(route('admin.site-testimonials.destroy', $row)); ?>" method="POST" class="inline" onsubmit="return confirm('حذف هذا الرأي؟');">
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
                <?php echo e($rows->links()); ?>

            </div>
        <?php else: ?>
            <div class="admin-empty">
                <i class="fas fa-quote-right"></i>
                <p class="text-sm font-bold text-slate-600 mb-1">لا توجد آراء بعد</p>
                <p class="text-xs mb-3">أضف أول رأي ليظهر في الصفحة الرئيسية وصفحة <span class="font-mono text-slate-500">/testimonials</span>.</p>
                <a href="<?php echo e(route('admin.site-testimonials.create')); ?>" class="admin-btn admin-btn--primary">
                    <i class="fas fa-plus"></i>
                    إضافة رأي
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sana\resources\views\admin\site-testimonials\index.blade.php ENDPATH**/ ?>