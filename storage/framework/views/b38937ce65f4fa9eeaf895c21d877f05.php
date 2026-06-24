<?php $__env->startSection('title', 'لوحة الإدارة - ' . ($platformName ?? config('brand.name', config('app.name')))); ?>
<?php $__env->startSection('page_title', 'لوحة الإدارة'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $u = auth()->user();
    $dashFull = $u->isAdmin() && ! $u->hasAssignedRbacRoles();
?>
<div class="admin-dashboard space-y-7">

    
    <div class="admin-dashboard-hero animate-fade-in">
        <div class="admin-dashboard-hero-inner flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div>
                <p class="hero-date mb-1"><?php echo e(now()->translatedFormat('l، j F Y')); ?></p>
                <h2 class="hero-title text-2xl sm:text-3xl font-heading font-bold">مرحباً، <?php echo e(\App\Support\PlatformBranding::greetingDisplayName(auth()->user()->name)); ?></h2>
                <p class="hero-sub text-sm mt-2 max-w-xl leading-relaxed">
                    نظرة عامة على منصة <?php echo e($platformName ?? config('brand.name', config('app.name'))); ?> — الإحصائيات والأقسام أدناه حسب صلاحيات حسابك.
                </p>
            </div>
            <div class="flex flex-wrap gap-2 shrink-0">
                <a href="<?php echo e(route('home')); ?>" target="_blank" rel="noopener"
                   class="admin-dashboard-hero__btn admin-dashboard-hero__btn--ghost">
                    <i class="fas fa-globe"></i> الموقع العام
                </a>
                <?php if($dashFull || $u->hasPermission('manage.system-settings')): ?>
                <a href="<?php echo e(route('admin.system-settings.edit')); ?>"
                   class="admin-dashboard-hero__btn admin-dashboard-hero__btn--solid">
                    <i class="fas fa-sliders-h"></i> الإعدادات
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    
    <div class="dash-quick-panel animate-fade-in animate-fade-in-1">
        <div class="dash-quick-panel__grid">
        <?php if($dashFull || $u->hasPermission('manage.courses')): ?>
        <a href="<?php echo e(route('admin.advanced-courses.index')); ?>" class="admin-quick-link">
            <span class="bg-gradient-to-br from-[var(--admin-primary)] to-[var(--admin-purple)]"><i class="fas fa-book"></i></span>
            <span class="text-xs font-bold">الكورسات</span>
        </a>
        <?php endif; ?>
        <?php if($dashFull || $u->hasPermission('manage.users')): ?>
        <a href="<?php echo e(route('admin.users.index')); ?>" class="admin-quick-link">
            <span class="bg-gradient-to-br from-emerald-500 to-teal-600"><i class="fas fa-users"></i></span>
            <span class="text-xs font-bold">المستخدمون</span>
        </a>
        <?php endif; ?>
        <?php if(($dashFull || $u->hasPermission('manage.enrollments')) && Route::has('admin.online-enrollments.index')): ?>
        <a href="<?php echo e(route('admin.online-enrollments.index')); ?>" class="admin-quick-link">
            <span class="bg-gradient-to-br from-violet-500 to-purple-600"><i class="fas fa-user-check"></i></span>
            <span class="text-xs font-bold">التسجيلات</span>
        </a>
        <?php endif; ?>
        <?php if(($dashFull || $u->hasPermission('manage.orders')) && Route::has('admin.orders.index')): ?>
        <a href="<?php echo e(route('admin.orders.index')); ?>" class="admin-quick-link">
            <span class="bg-gradient-to-br from-amber-500 to-orange-500"><i class="fas fa-shopping-cart"></i></span>
            <span class="text-xs font-bold">الطلبات</span>
        </a>
        <?php endif; ?>
        <?php if($dashFull || $u->hasPermission('manage.contact-messages')): ?>
        <a href="<?php echo e(route('admin.contact-messages.index')); ?>" class="admin-quick-link">
            <span class="bg-gradient-to-br from-sky-500 to-blue-600"><i class="fas fa-envelope-open-text"></i></span>
            <span class="text-xs font-bold">الرسائل</span>
        </a>
        <?php endif; ?>
        <a href="<?php echo e(route('admin.notifications.inbox')); ?>" class="admin-quick-link">
            <span class="bg-gradient-to-br from-rose-500 to-pink-600"><i class="fas fa-inbox"></i></span>
            <span class="text-xs font-bold">الإشعارات</span>
        </a>
        </div>
    </div>

    <?php
        $ds = $dashboardShow ?? [];
        $hasAnyDashboardWidget = collect($ds)->filter(fn ($v) => (bool) $v)->isNotEmpty();
    ?>
    <?php if(isset($dashboardShow) && ! $hasAnyDashboardWidget): ?>
        <div class="rounded-xl border border-amber-200 bg-amber-50/80 px-4 py-3 text-sm text-amber-900">
            <i class="fas fa-info-circle ml-1"></i>
            لا توجد بطاقات إحصائية لعرضها حالياً. اطلب من المسؤول إسناد الصلاحيات المناسبة لدورك (مثل الكورسات، الطلبات، الفواتير، …).
        </div>
    <?php endif; ?>

    
    <section class="dash-section animate-fade-in">
        <?php echo $__env->make('admin.dashboard.partials.section-heading', [
            'title' => 'المؤشرات الرئيسية',
            'subtitle' => 'المستخدمون، الطلاب، المدربون، والكورسات',
            'icon' => 'fas fa-chart-pie',
            'tone' => 'primary',
        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <div class="dash-grid dash-grid--metrics">
        <?php if(!empty($ds['users_metric'])): ?>
        <?php $usersMetric = $metrics['users'] ?? null; $usersTrend = $usersMetric['trend'] ?? null; ?>
        <div class="stat-card animate-fade-in animate-fade-in-1" style="--before-bg: #6366f1;">
            <div class="flex items-start justify-between">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-slate-500 mb-1">إجمالي المستخدمين</p>
                    <p class="text-3xl font-heading font-bold text-slate-800"><?php echo e(number_format($usersMetric['total'] ?? 0)); ?></p>
                </div>
                <div class="stat-icon shadow-lg" style="background: linear-gradient(135deg, var(--admin-primary), var(--admin-purple));">
                    <i class="fas fa-users"></i>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-2 text-sm">
                <span class="text-slate-400">هذا الشهر:</span>
                <span class="font-semibold text-slate-700"><?php echo e(number_format($usersMetric['new_this_month'] ?? 0)); ?></span>
                <?php if($usersTrend): ?>
                    <?php $percent = $usersTrend['percent']; $positive = $percent >= 0; ?>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold <?php echo e($positive ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600'); ?>">
                        <?php echo e($positive ? '+' : ''); ?><?php echo e(number_format($percent, 1)); ?>%
                    </span>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if(!empty($ds['students_metric'])): ?>
        <?php $studentsMetric = $metrics['students'] ?? null; $studentsTrend = $studentsMetric['trend'] ?? null; ?>
        <div class="stat-card animate-fade-in animate-fade-in-2">
            <div class="flex items-start justify-between">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-slate-500 mb-1">الطلاب</p>
                    <p class="text-3xl font-heading font-bold text-slate-800"><?php echo e(number_format($studentsMetric['total'] ?? 0)); ?></p>
                </div>
                <div class="stat-icon bg-gradient-to-br from-emerald-500 to-emerald-600 shadow-lg shadow-emerald-500/25">
                    <i class="fas fa-user-graduate"></i>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-2 text-sm">
                <span class="text-slate-400">هذا الشهر:</span>
                <span class="font-semibold text-slate-700"><?php echo e(number_format($studentsMetric['new_this_month'] ?? 0)); ?></span>
                <?php if($studentsTrend): ?>
                    <?php $percent = $studentsTrend['percent']; $positive = $percent >= 0; ?>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold <?php echo e($positive ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600'); ?>">
                        <?php echo e($positive ? '+' : ''); ?><?php echo e(number_format($percent, 1)); ?>%
                    </span>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if(!empty($ds['instructors_metric'])): ?>
        <?php $instructorsMetric = $metrics['instructors'] ?? null; $instructorsTrend = $instructorsMetric['trend'] ?? null; ?>
        <div class="stat-card animate-fade-in animate-fade-in-3">
            <div class="flex items-start justify-between">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-slate-500 mb-1">المدربين</p>
                    <p class="text-3xl font-heading font-bold text-slate-800"><?php echo e(number_format($instructorsMetric['total'] ?? 0)); ?></p>
                </div>
                <div class="stat-icon bg-gradient-to-br from-violet-500 to-violet-600 shadow-lg shadow-violet-500/25">
                    <i class="fas fa-user-tie"></i>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-2 text-sm">
                <span class="text-slate-400">هذا الشهر:</span>
                <span class="font-semibold text-slate-700"><?php echo e(number_format($instructorsMetric['new_this_month'] ?? 0)); ?></span>
                <?php if($instructorsTrend): ?>
                    <?php $percent = $instructorsTrend['percent']; $positive = $percent >= 0; ?>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold <?php echo e($positive ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600'); ?>">
                        <?php echo e($positive ? '+' : ''); ?><?php echo e(number_format($percent, 1)); ?>%
                    </span>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if(!empty($ds['courses_metric'])): ?>
        <?php $coursesMetric = $metrics['courses'] ?? null; $coursesTrend = $coursesMetric['trend'] ?? null; ?>
        <div class="stat-card animate-fade-in animate-fade-in-4">
            <div class="flex items-start justify-between">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-slate-500 mb-1">الكورسات</p>
                    <p class="text-3xl font-heading font-bold text-slate-800"><?php echo e(number_format($coursesMetric['total'] ?? 0)); ?></p>
                </div>
                <div class="stat-icon bg-gradient-to-br from-amber-500 to-orange-500 shadow-lg shadow-amber-500/25">
                    <i class="fas fa-book"></i>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-2 text-sm">
                <span class="text-slate-400">هذا الشهر:</span>
                <span class="font-semibold text-slate-700"><?php echo e(number_format($coursesMetric['new_this_month'] ?? 0)); ?></span>
                <?php if($coursesTrend): ?>
                    <?php $percent = $coursesTrend['percent']; $positive = $percent >= 0; ?>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold <?php echo e($positive ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600'); ?>">
                        <?php echo e($positive ? '+' : ''); ?><?php echo e(number_format($percent, 1)); ?>%
                    </span>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
    </section>

    <?php
        $hasFinancialMetrics = !empty($ds['revenue_total']) || !empty($ds['monthly_revenue'])
            || !empty($ds['pending_invoices_metric']) || !empty($ds['enrollments_metric']);
    ?>
    <?php if($hasFinancialMetrics): ?>
    
    <section class="dash-section animate-fade-in">
        <?php echo $__env->make('admin.dashboard.partials.section-heading', [
            'title' => 'المؤشرات المالية',
            'subtitle' => 'الإيرادات، الفواتير، والتسجيلات النشطة',
            'icon' => 'fas fa-coins',
            'tone' => 'emerald',
        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <div class="dash-grid dash-grid--metrics">
        <?php if(!empty($ds['revenue_total'])): ?>
        <div class="stat-card animate-fade-in">
            <div class="flex items-start justify-between">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-slate-500 mb-1">إجمالي الإيرادات</p>
                    <p class="text-2xl font-heading font-bold text-slate-800"><?php echo e(number_format($stats['total_revenue'] ?? 0, 2)); ?> <span class="text-base font-normal text-slate-400"><?php echo e(__('public.currency')); ?></span></p>
                </div>
                <div class="stat-icon bg-gradient-to-br from-emerald-500 to-teal-600 shadow-lg shadow-emerald-500/25">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if(!empty($ds['monthly_revenue'])): ?>
        <?php $revenueMetric = $metrics['monthly_revenue'] ?? null; $revenueTrend = $revenueMetric['trend'] ?? null; ?>
        <div class="stat-card animate-fade-in">
            <div class="flex items-start justify-between">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-slate-500 mb-1">إيرادات الشهر</p>
                    <p class="text-2xl font-heading font-bold text-slate-800"><?php echo e(number_format($revenueMetric['current'] ?? 0, 2)); ?> <span class="text-base font-normal text-slate-400"><?php echo e(__('public.currency')); ?></span></p>
                </div>
                <div class="stat-icon bg-gradient-to-br from-sky-500 to-blue-600 shadow-lg shadow-sky-500/25">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
            <?php if($revenueTrend): ?>
                <?php $diff = $revenueTrend['difference']; $percent = $revenueTrend['percent']; $positive = $diff >= 0; ?>
                <div class="mt-3 flex items-center gap-2 text-sm">
                    <span class="font-semibold <?php echo e($positive ? 'text-emerald-600' : 'text-rose-500'); ?>"><?php echo e($positive ? '+' : ''); ?><?php echo e(number_format($diff, 2)); ?> <?php echo e(__('public.currency')); ?></span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold <?php echo e($positive ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600'); ?>">
                        <?php echo e($percent >= 0 ? '+' : ''); ?><?php echo e(number_format($percent, 1)); ?>%
                    </span>
                </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php if(!empty($ds['pending_invoices_metric'])): ?>
        <?php $pendingMetric = $metrics['pending_invoices'] ?? null; $pendingTrend = $pendingMetric['trend'] ?? null; ?>
        <div class="stat-card animate-fade-in">
            <div class="flex items-start justify-between">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-slate-500 mb-1">فواتير معلقة</p>
                    <p class="text-3xl font-heading font-bold text-slate-800"><?php echo e(number_format($pendingMetric['total'] ?? 0)); ?></p>
                </div>
                <div class="stat-icon bg-gradient-to-br from-amber-400 to-orange-500 shadow-lg shadow-amber-500/25">
                    <i class="fas fa-file-invoice"></i>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-2 text-sm">
                <span class="text-slate-400">هذا الشهر:</span>
                <span class="font-semibold text-slate-700"><?php echo e(number_format($pendingMetric['new_this_month'] ?? 0)); ?></span>
            </div>
        </div>
        <?php endif; ?>

        <?php if(!empty($ds['enrollments_metric'])): ?>
        <?php $enrollmentsMetric = $metrics['enrollments'] ?? null; $enrollmentsTrend = $enrollmentsMetric['trend'] ?? null; ?>
        <div class="stat-card animate-fade-in">
            <div class="flex items-start justify-between">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-slate-500 mb-1">التسجيلات النشطة</p>
                    <p class="text-3xl font-heading font-bold text-slate-800"><?php echo e(number_format($enrollmentsMetric['total'] ?? 0)); ?></p>
                </div>
                <div class="stat-icon bg-gradient-to-br from-fuchsia-500 to-purple-600 shadow-lg shadow-fuchsia-500/25">
                    <i class="fas fa-user-check"></i>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-2 text-sm">
                <span class="text-slate-400">هذا الشهر:</span>
                <span class="font-semibold text-slate-700"><?php echo e(number_format($enrollmentsMetric['new_this_month'] ?? 0)); ?></span>
                <?php if($enrollmentsTrend): ?>
                    <?php $percent = $enrollmentsTrend['percent']; $positive = $percent >= 0; ?>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold <?php echo e($positive ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600'); ?>">
                        <?php echo e($positive ? '+' : ''); ?><?php echo e(number_format($percent, 1)); ?>%
                    </span>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
    </section>
    <?php endif; ?>

    
    <?php if(!empty($ds['activity_feed']) || !empty($ds['exam_attempts'])): ?>
    <section class="dash-section">
        <?php echo $__env->make('admin.dashboard.partials.section-heading', [
            'title' => 'النشاط والامتحانات',
            'subtitle' => 'آخر التحديثات على المنصة',
            'icon' => 'fas fa-bolt',
            'tone' => 'violet',
        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <div class="dash-grid dash-grid--panels">
        <?php if(!empty($ds['activity_feed'])): ?>
        <div class="section-card animate-fade-in">
            <div class="section-card-header">
                <h3 class="text-base font-heading font-bold text-slate-800 flex items-center gap-2">
                    <i class="fas fa-history text-indigo-500"></i> آخر النشاطات
                </h3>
            </div>
            <div class="dash-list">
                <?php if(isset($stats['recent_activities']) && $stats['recent_activities']->count() > 0): ?>
                    <?php $__currentLoopData = $stats['recent_activities']->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="list-row">
                            <div class="w-9 h-9 bg-indigo-50 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-history text-indigo-500 text-xs"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-slate-700 truncate"><?php echo e($activity->user->name ?? 'مستخدم محذوف'); ?></p>
                                <p class="text-xs text-slate-400"><?php echo e($activity->action); ?> &middot; <?php echo e($activity->created_at->diffForHumans()); ?></p>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <div class="section-card-footer-link">
                        <a href="<?php echo e(route('admin.activity-log')); ?>" class="text-sm font-semibold text-indigo-600 hover:text-indigo-700 flex items-center gap-1.5 transition-colors">
                            عرض جميع النشاطات <i class="fas fa-arrow-left text-[10px]"></i>
                        </a>
                    </div>
                <?php else: ?>
                    <div class="dash-empty">
                        <i class="fas fa-history"></i>
                        <p class="text-sm">لا توجد أنشطة بعد</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if(!empty($ds['exam_attempts'])): ?>
        <div class="section-card animate-fade-in">
            <div class="section-card-header">
                <h3 class="text-base font-heading font-bold text-slate-800 flex items-center gap-2">
                    <i class="fas fa-clipboard-check text-violet-500"></i> آخر محاولات الامتحانات
                </h3>
            </div>
            <div class="dash-list">
                <?php if(isset($stats['recent_exam_attempts']) && $stats['recent_exam_attempts']->count() > 0): ?>
                    <?php $__currentLoopData = $stats['recent_exam_attempts']->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attempt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="list-row">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-slate-700"><?php echo e($attempt->student->name ?? 'طالب محذوف'); ?></p>
                                <p class="text-xs text-slate-400"><?php echo e($attempt->exam->title ?? 'امتحان محذوف'); ?></p>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold flex-shrink-0
                                <?php echo e($attempt->score >= 80 ? 'bg-emerald-50 text-emerald-600' : ($attempt->score >= 60 ? 'bg-amber-50 text-amber-600' : 'bg-rose-50 text-rose-600')); ?>">
                                <?php echo e($attempt->score); ?>%
                            </span>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                    <div class="dash-empty">
                        <i class="fas fa-clipboard-check"></i>
                        <p class="text-sm">لا توجد محاولات امتحانات بعد</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
    </section>
    <?php endif; ?>

    
    <?php if((!empty($ds['recent_users']) && isset($recent_users)) || (!empty($ds['recent_courses']) && isset($recent_courses))): ?>
    <section class="dash-section">
        <?php echo $__env->make('admin.dashboard.partials.section-heading', [
            'title' => 'آخر الإضافات',
            'subtitle' => 'المستخدمون والكورسات المسجّلة حديثاً',
            'icon' => 'fas fa-clock',
            'tone' => 'amber',
        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <div class="dash-grid dash-grid--panels">
        <?php if(!empty($ds['recent_users']) && isset($recent_users)): ?>
        <div class="section-card animate-fade-in">
            <div class="section-card-header">
                <h3 class="text-base font-heading font-bold text-slate-800 flex items-center gap-2">
                    <i class="fas fa-users text-sky-500"></i> آخر المستخدمين
                </h3>
                <a href="<?php echo e(route('admin.users.index')); ?>" class="text-sm font-semibold text-indigo-600 hover:text-indigo-700 flex items-center gap-1.5 transition-colors">
                    عرض الكل <i class="fas fa-arrow-left text-[10px]"></i>
                </a>
            </div>
            <div class="dash-list">
                <?php $__currentLoopData = $recent_users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="list-row">
                    <div class="w-9 h-9 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-lg flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                        <?php echo e(mb_substr($user->name, 0, 1)); ?>

                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-slate-700 truncate"><?php echo e($user->name); ?></p>
                        <p class="text-xs text-slate-400"><?php echo e($user->phone ?? $user->email); ?></p>
                    </div>
                    <div class="text-left flex-shrink-0">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold
                            <?php if($user->role === 'student'): ?> bg-emerald-50 text-emerald-600
                            <?php elseif($user->role === 'instructor'): ?> bg-violet-50 text-violet-600
                            <?php elseif($user->role === 'super_admin'): ?> bg-rose-50 text-rose-600
                            <?php else: ?> bg-slate-100 text-slate-500 <?php endif; ?>">
                            <?php if($user->role === 'student'): ?> طالب
                            <?php elseif($user->role === 'instructor'): ?> مدرب
                            <?php elseif($user->role === 'super_admin'): ?> مدير عام
                            <?php else: ?> غير محدد <?php endif; ?>
                        </span>
                        <p class="text-[11px] text-slate-300 mt-0.5"><?php echo e($user->created_at->diffForHumans()); ?></p>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if(!empty($ds['recent_courses']) && isset($recent_courses)): ?>
        <div class="section-card animate-fade-in">
            <div class="section-card-header">
                <h3 class="text-base font-heading font-bold text-slate-800 flex items-center gap-2">
                    <i class="fas fa-book text-amber-500"></i> آخر الكورسات
                </h3>
                <a href="<?php echo e(route('admin.advanced-courses.index')); ?>" class="text-sm font-semibold text-indigo-600 hover:text-indigo-700 flex items-center gap-1.5 transition-colors">
                    عرض الكل <i class="fas fa-arrow-left text-[10px]"></i>
                </a>
            </div>
            <div class="dash-list">
                <?php $__empty_1 = true; $__currentLoopData = $recent_courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="list-row">
                    <div class="w-10 h-10 bg-gradient-to-br from-amber-400 to-orange-500 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-book text-white text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-slate-700 truncate"><?php echo e($course->title); ?></p>
                        <p class="text-xs text-slate-400"><?php echo e($course->academicSubject->name ?? 'غير محدد'); ?></p>
                    </div>
                    <div class="text-left flex-shrink-0">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold
                            <?php if($course->is_active): ?> bg-emerald-50 text-emerald-600
                            <?php else: ?> bg-slate-100 text-slate-500 <?php endif; ?>">
                            <?php if($course->is_active): ?> نشط <?php else: ?> غير نشط <?php endif; ?>
                        </span>
                        <p class="text-[11px] text-slate-300 mt-0.5"><?php echo e($course->created_at->diffForHumans()); ?></p>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="dash-empty">
                    <i class="fas fa-book"></i>
                    <p class="text-sm">لا توجد كورسات بعد</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
    </section>
    <?php endif; ?>

    
    <?php if(!empty($salesSection) || !empty($hrSection)): ?>
    <section class="dash-section">
        <?php echo $__env->make('admin.dashboard.partials.section-heading', [
            'title' => 'العمليات الداخلية',
            'subtitle' => 'المبيعات والموارد البشرية',
            'icon' => 'fas fa-building',
            'tone' => 'primary',
        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <div class="dash-grid dash-grid--panels">
        <?php if(!empty($salesSection)): ?>
        <div class="section-card animate-fade-in">
            <div class="section-card-header">
                <h3 class="text-base font-heading font-bold text-slate-800 flex items-center gap-2">
                    <i class="fas fa-shopping-cart text-emerald-500"></i> قسم المبيعات
                </h3>
                <a href="<?php echo e(route('admin.orders.index')); ?>" class="text-sm font-semibold text-indigo-600 hover:text-indigo-700 flex items-center gap-1.5 transition-colors">
                    عرض الكل <i class="fas fa-arrow-left text-[10px]"></i>
                </a>
            </div>
            <div class="section-card-body section-card-body--padded">
                <div class="dash-kpi-grid">
                    <div class="dash-kpi">
                        <p class="dash-kpi__label">طلبات معلقة</p>
                        <p class="dash-kpi__value"><?php echo e($salesSection['orders_pending']); ?></p>
                    </div>
                    <div class="dash-kpi">
                        <p class="dash-kpi__label">معتمدة هذا الشهر</p>
                        <p class="dash-kpi__value"><?php echo e($salesSection['orders_approved_month']); ?></p>
                    </div>
                    <div class="dash-kpi dash-kpi--wide dash-kpi--emerald">
                        <p class="dash-kpi__label">إيرادات الشهر (طلبات معتمدة)</p>
                        <p class="dash-kpi__value"><?php echo e(number_format($salesSection['revenue_month'] ?? 0, 2)); ?> <span class="text-sm font-semibold"><?php echo e(__('public.currency')); ?></span></p>
                    </div>
                </div>
                <p class="dash-subsection-title">آخر الطلبات</p>
                <div class="dash-list rounded-xl border border-slate-100 overflow-hidden">
                <?php $__empty_1 = true; $__currentLoopData = $salesSection['recent_orders'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="list-row">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-slate-700 truncate"><?php echo e($order->user->name ?? '—'); ?></p>
                        <p class="text-xs text-slate-400 truncate"><?php echo e($order->course->title ?? '—'); ?></p>
                    </div>
                    <div class="text-left flex-shrink-0">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold
                            <?php if($order->status === 'approved'): ?> bg-emerald-50 text-emerald-600
                            <?php elseif($order->status === 'pending'): ?> bg-amber-50 text-amber-600
                            <?php else: ?> bg-rose-50 text-rose-600 <?php endif; ?>">
                            <?php if($order->status === 'approved'): ?> معتمد
                            <?php elseif($order->status === 'pending'): ?> معلق
                            <?php else: ?> مرفوض <?php endif; ?>
                        </span>
                        <p class="text-sm font-bold text-slate-700 mt-0.5"><?php echo e(number_format($order->amount ?? 0, 0)); ?> <?php echo e(__('public.currency')); ?></p>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="dash-empty"><p class="text-sm">لا توجد طلبات حديثة</p></div>
                <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if(!empty($hrSection)): ?>
        <div class="section-card animate-fade-in">
            <div class="section-card-header">
                <h3 class="text-base font-heading font-bold text-slate-800 flex items-center gap-2">
                    <i class="fas fa-users-cog text-indigo-500"></i> قسم الموارد البشرية
                </h3>
                <a href="<?php echo e(route('admin.employees.index')); ?>" class="text-sm font-semibold text-indigo-600 hover:text-indigo-700 flex items-center gap-1.5 transition-colors">
                    عرض الكل <i class="fas fa-arrow-left text-[10px]"></i>
                </a>
            </div>
            <div class="section-card-body section-card-body--padded">
                <div class="dash-kpi-grid">
                    <div class="dash-kpi">
                        <p class="dash-kpi__label">إجمالي الموظفين</p>
                        <p class="dash-kpi__value"><?php echo e($hrSection['employees_total']); ?></p>
                    </div>
                    <div class="dash-kpi">
                        <p class="dash-kpi__label">طلبات إجازة معلقة</p>
                        <p class="dash-kpi__value"><?php echo e($hrSection['leaves_pending']); ?></p>
                    </div>
                    <div class="dash-kpi dash-kpi--wide dash-kpi--indigo">
                        <p class="dash-kpi__label">إجازات معتمدة هذا الشهر</p>
                        <p class="dash-kpi__value"><?php echo e($hrSection['leaves_approved_month']); ?></p>
                    </div>
                </div>
                <p class="dash-subsection-title">آخر طلبات الإجازة</p>
                <div class="dash-list rounded-xl border border-slate-100 overflow-hidden">
                <?php $__empty_1 = true; $__currentLoopData = $hrSection['recent_leaves'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $leave): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="list-row">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-slate-700 truncate"><?php echo e($leave->employee->name ?? '—'); ?></p>
                        <p class="text-xs text-slate-400"><?php echo e($leave->days ?? 0); ?> يوم · <?php echo e(optional($leave->employee->employeeJob)->name ?? '—'); ?></p>
                    </div>
                    <div class="text-left flex-shrink-0">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold
                            <?php if($leave->status === 'approved'): ?> bg-emerald-50 text-emerald-600
                            <?php elseif($leave->status === 'pending'): ?> bg-amber-50 text-amber-600
                            <?php else: ?> bg-rose-50 text-rose-600 <?php endif; ?>">
                            <?php if($leave->status === 'approved'): ?> معتمد
                            <?php elseif($leave->status === 'pending'): ?> معلق
                            <?php else: ?> مرفوض <?php endif; ?>
                        </span>
                        <p class="text-[11px] text-slate-300 mt-0.5"><?php echo e($leave->created_at->diffForHumans()); ?></p>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="dash-empty"><p class="text-sm">لا توجد طلبات إجازة حديثة</p></div>
                <?php endif; ?>
                </div>
                <p class="dash-subsection-title mt-3">آخر الموظفين المضافة</p>
                <div class="dash-list rounded-xl border border-slate-100 overflow-hidden">
                    <?php $__empty_1 = true; $__currentLoopData = $hrSection['recent_employees'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="list-row">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-slate-700 truncate"><?php echo e($emp->name); ?></p>
                            <p class="text-xs text-slate-400"><?php echo e(optional($emp->employeeJob)->name ?? 'موظف'); ?></p>
                        </div>
                        <p class="text-[11px] text-slate-400"><?php echo e($emp->hire_date ? $emp->hire_date->diffForHumans() : $emp->created_at->diffForHumans()); ?></p>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="dash-empty"><p class="text-sm">لا يوجد موظفون</p></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
    </section>
    <?php endif; ?>

    
    <?php if((!empty($ds['invoices_panel']) && isset($pending_invoices) && $pending_invoices->count() > 0) || (!empty($ds['payments_panel']) && isset($recent_payments) && $recent_payments->count() > 0)): ?>
    <div class="dash-grid dash-grid--panels">
        <?php if(!empty($ds['invoices_panel']) && isset($pending_invoices) && $pending_invoices->count() > 0): ?>
        <div class="section-card animate-fade-in">
            <div class="section-card-header">
                <h3 class="text-base font-heading font-bold text-slate-800 flex items-center gap-2">
                    <i class="fas fa-file-invoice text-amber-500"></i> الفواتير المعلقة
                </h3>
                <a href="#" class="text-sm font-semibold text-indigo-600 hover:text-indigo-700 flex items-center gap-1.5 transition-colors">عرض الكل <i class="fas fa-arrow-left text-[10px]"></i></a>
            </div>
            <div class="dash-list">
                <?php $__currentLoopData = $pending_invoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invoice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="list-row">
                    <div class="w-10 h-10 bg-amber-50 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-file-invoice text-amber-500 text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-slate-700 truncate"><?php echo e($invoice->invoice_number ?? 'غير محدد'); ?></p>
                        <p class="text-xs text-slate-400"><?php echo e($invoice->user->name ?? 'غير محدد'); ?></p>
                    </div>
                    <div class="text-left flex-shrink-0">
                        <p class="text-sm font-bold text-slate-700"><?php echo e(number_format($invoice->total_amount ?? 0, 2)); ?> <?php echo e(__('public.currency')); ?></p>
                        <p class="text-[11px] text-slate-300"><?php echo e($invoice->created_at->diffForHumans()); ?></p>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if(!empty($ds['payments_panel']) && isset($recent_payments) && $recent_payments->count() > 0): ?>
        <div class="section-card animate-fade-in">
            <div class="section-card-header">
                <h3 class="text-base font-heading font-bold text-slate-800 flex items-center gap-2">
                    <i class="fas fa-money-bill-wave text-emerald-500"></i> المدفوعات الأخيرة
                </h3>
                <a href="#" class="text-sm font-semibold text-indigo-600 hover:text-indigo-700 flex items-center gap-1.5 transition-colors">عرض الكل <i class="fas fa-arrow-left text-[10px]"></i></a>
            </div>
            <div class="dash-list">
                <?php $__currentLoopData = $recent_payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="list-row">
                    <div class="w-10 h-10 bg-emerald-50 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-check-circle text-emerald-500 text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-slate-700 truncate"><?php echo e($payment->payment_number ?? 'غير محدد'); ?></p>
                        <p class="text-xs text-slate-400"><?php echo e($payment->user->name ?? 'غير محدد'); ?></p>
                    </div>
                    <div class="text-left flex-shrink-0">
                        <p class="text-sm font-bold text-emerald-600"><?php echo e(number_format($payment->amount ?? 0, 2)); ?> <?php echo e(__('public.currency')); ?></p>
                        <p class="text-[11px] text-slate-300"><?php echo e($payment->paid_at ? $payment->paid_at->diffForHumans() : $payment->created_at->diffForHumans()); ?></p>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    
    <?php if(isset($subscriptionPackages) && $subscriptionPackages->isNotEmpty() && ($dashboardShow['subscriptions_section'] ?? false)): ?>
    <div class="section-card animate-fade-in">
        <div class="section-card-header">
            <h3 class="text-base font-heading font-bold text-slate-800 flex items-center gap-2">
                <i class="fas fa-crown text-amber-500"></i> العناصر المدفوعة — الاشتراكات
            </h3>
            <div class="flex items-center gap-3">
                <a href="<?php echo e(route('admin.subscriptions.index')); ?>" class="text-sm font-semibold text-indigo-600 hover:text-indigo-700 flex items-center gap-1.5 transition-colors">
                    عرض كل الاشتراكات <i class="fas fa-arrow-left text-[10px]"></i>
                </a>
                <a href="<?php echo e(route('admin.subscriptions.create')); ?>" class="text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 px-3 py-1.5 rounded-lg transition-colors">
                    إضافة اشتراك
                </a>
            </div>
        </div>
        <div class="p-5">
            <?php if($subscriptionPackages->count() > 0): ?>
            <p class="text-xs text-slate-500 mb-4">الباقات المتاحة وجميع المشتركين في كل باقة. يمكنك الدخول على أي مشترك لإدارة اشتراكه ومراجعة بياناته بالكامل.</p>
            <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
                <?php $__currentLoopData = $subscriptionPackages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $package): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="rounded-2xl border border-slate-200 bg-slate-50/50 overflow-hidden">
                    <div class="px-4 py-3 bg-white border-b border-slate-200 flex items-center justify-between">
                        <h4 class="text-sm font-bold text-slate-800"><?php echo e($package['plan_name']); ?></h4>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-700">
                            <i class="fas fa-users text-[10px]"></i>
                            <?php echo e($package['count']); ?> مشترك
                        </span>
                    </div>
                    <div class="p-3 max-h-64 overflow-y-auto space-y-1.5">
                        <?php $__empty_1 = true; $__currentLoopData = $package['subscriptions']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="flex items-center justify-between gap-2 rounded-xl border border-slate-100 bg-white px-3 py-2 hover:border-indigo-200 transition-colors">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-slate-700 truncate"><?php echo e($sub->user->name ?? '—'); ?></p>
                                <p class="text-[11px] text-slate-400"><?php echo e($sub->user->phone ?? $sub->user->email ?? '—'); ?></p>
                            </div>
                            <div class="flex items-center gap-1 flex-shrink-0">
                                <a href="<?php echo e(route('admin.subscriptions.show', $sub)); ?>" class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-sky-100 text-sky-600 hover:bg-sky-200 transition-colors text-xs" title="تفاصيل الاشتراك والتحكم">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <?php if($sub->user): ?>
                                <a href="<?php echo e(route('admin.users.show', $sub->user->id)); ?>" class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200 transition-colors text-xs" title="بيانات المستخدم">
                                    <i class="fas fa-user"></i>
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="text-xs text-slate-400 py-2 text-center">لا مشتركين في هذه الباقة</p>
                        <?php endif; ?>
                    </div>
                    <?php if($package['count'] > $package['subscriptions']->count()): ?>
                    <div class="px-4 py-2 border-t border-slate-100 bg-slate-50/80 text-center">
                        <a href="<?php echo e(route('admin.subscriptions.index')); ?>" class="text-xs font-semibold text-indigo-600 hover:text-indigo-700">
                            عرض كل الاشتراكات (<?php echo e($package['count']); ?> في هذه الباقة) <i class="fas fa-arrow-left text-[10px]"></i>
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php else: ?>
            <div class="py-10 text-center rounded-xl bg-slate-50 border border-slate-100">
                <i class="fas fa-layer-group text-3xl text-slate-300 mb-2"></i>
                <p class="text-sm text-slate-500">لا توجد اشتراكات أو باقات حالياً.</p>
                <a href="<?php echo e(route('admin.subscriptions.create')); ?>" class="inline-flex items-center gap-2 mt-3 text-sm font-semibold text-indigo-600 hover:text-indigo-700">
                    <i class="fas fa-plus"></i> إضافة اشتراك جديد
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    
    <div class="section-card animate-fade-in">
        <div class="section-card-header">
            <div>
                <h3 class="text-base font-heading font-bold text-slate-800 flex items-center gap-2">
                    <i class="fas fa-bolt text-amber-500"></i> إجراءات سريعة
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">روابط مباشرة للمهام اليومية</p>
            </div>
        </div>
        <div class="section-card-body section-card-body--padded">
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
                <?php $__currentLoopData = ($quickActions ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $action): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e($action['route']); ?>" class="group flex flex-col items-center gap-2.5 p-4 rounded-xl border border-slate-100 bg-gradient-to-b from-white to-slate-50/80 hover:border-[rgba(var(--admin-primary-rgb),0.2)] hover:shadow-md transition-all duration-200">
                        <div class="w-11 h-11 rounded-xl bg-gradient-to-br <?php echo e($action['icon_background']); ?> flex items-center justify-center shadow-md group-hover:scale-105 transition-transform">
                            <i class="<?php echo e($action['icon']); ?> text-white text-base"></i>
                        </div>
                        <p class="text-[11px] font-bold text-slate-600 text-center leading-snug"><?php echo e($action['title']); ?></p>
                        <?php $actionCount = $action['count'] ?? 0; ?>
                        <p class="text-xl font-heading font-bold text-slate-800 leading-none"><?php echo e(number_format($actionCount)); ?></p>
                        <?php if(!empty($action['meta'])): ?>
                            <p class="text-[10px] text-slate-400 text-center"><?php echo e($action['meta']); ?></p>
                        <?php endif; ?>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php if(empty($quickActions)): ?>
                    <div class="col-span-full dash-empty">
                        <i class="fas fa-check-circle"></i>
                        <p class="text-sm">لا توجد مهام عاجلة حالياً</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sana\resources\views\admin\dashboard.blade.php ENDPATH**/ ?>