<?php $__env->startSection('title', 'تقارير المستخدمين'); ?>
<?php $__env->startSection('header', 'تقارير المستخدمين'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $monthNames = [
        1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل', 5 => 'مايو', 6 => 'يونيو',
        7 => 'يوليو', 8 => 'أغسطس', 9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر',
    ];
    $roleLabels = [
        'student' => 'طالب',
        'instructor' => 'مدرب',
        'teacher' => 'مدرب',
        'admin' => 'إدارة',
        'super_admin' => 'مدير عام',
        'parent' => 'ولي أمر',
        'employee' => 'موظف',
    ];
    $periodStatCards = [
        ['label' => 'تسجيلات الفترة', 'value' => $statsPeriod['total'] ?? 0, 'icon' => 'fa-user-plus', 'bg' => 'bg-blue-100', 'text' => 'text-blue-600', 'desc' => $periodLabel ?? ''],
        ['label' => 'طلاب جدد', 'value' => $statsPeriod['students'] ?? 0, 'icon' => 'fa-user-graduate', 'bg' => 'bg-emerald-100', 'text' => 'text-emerald-600', 'desc' => 'ضمن الفترة المحددة'],
        ['label' => 'مدربون جدد', 'value' => $statsPeriod['instructors'] ?? 0, 'icon' => 'fa-chalkboard-teacher', 'bg' => 'bg-amber-100', 'text' => 'text-amber-700', 'desc' => 'ضمن الفترة'],
        ['label' => 'نشطون (فترة)', 'value' => $statsPeriod['active'] ?? 0, 'icon' => 'fa-user-check', 'bg' => 'bg-cyan-100', 'text' => 'text-cyan-600', 'desc' => 'حسابات مفعّلة'],
    ];
    $quickRoles = [
        '' => ['label' => 'الكل', 'icon' => 'fa-users'],
        'student' => ['label' => 'طلاب', 'icon' => 'fa-user-graduate'],
        'instructor' => ['label' => 'مدربون', 'icon' => 'fa-chalkboard-teacher'],
        'admin' => ['label' => 'إدارة', 'icon' => 'fa-user-shield'],
    ];
?>

<div class="space-y-6">
    
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-5 bg-gradient-to-l from-blue-50 to-white border-b border-slate-200 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-600 to-indigo-600 flex items-center justify-center text-white shadow-md shadow-blue-500/20">
                    <i class="fas fa-users text-lg"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-black text-slate-900">تقارير المستخدمين</h2>
                    <p class="text-sm text-slate-600 mt-0.5">
                        <span class="font-semibold text-blue-700"><?php echo e($periodLabel); ?></span>
                        <span class="text-slate-400 mx-1">·</span>
                        <span class="text-xs" dir="ltr"><?php echo e($periodRangeText); ?></span>
                    </p>
                </div>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="<?php echo e(route('admin.reports.index')); ?>"
                   class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-slate-700 bg-white border border-slate-200 rounded-xl hover:bg-slate-50">
                    <i class="fas fa-arrow-right text-blue-500"></i>
                    التقارير
                </a>
                <?php if(Route::has('admin.reports.export.users')): ?>
                    <a href="<?php echo e(route('admin.reports.export.users', request()->query())); ?>"
                       class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-bold text-white bg-gradient-to-r from-emerald-600 to-emerald-500 rounded-xl shadow-sm hover:from-emerald-700 hover:to-emerald-600">
                        <i class="fas fa-file-excel"></i>
                        Excel
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 p-6 border-b border-slate-100">
            <?php $__currentLoopData = $periodStatCards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-xs font-semibold text-slate-600"><?php echo e($card['label']); ?></p>
                            <p class="text-2xl font-black text-slate-900 mt-1"><?php echo e(number_format($card['value'])); ?></p>
                            <p class="text-[11px] text-slate-500 mt-1"><?php echo e($card['desc']); ?></p>
                        </div>
                        <div class="w-10 h-10 rounded-lg <?php echo e($card['bg']); ?> <?php echo e($card['text']); ?> flex items-center justify-center shrink-0">
                            <i class="fas <?php echo e($card['icon']); ?>"></i>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <div class="px-6 py-4 bg-slate-50/80 flex flex-wrap gap-4 text-xs text-slate-600">
            <span><strong class="text-slate-800">المنصة كاملة:</strong> <?php echo e(number_format($statsPlatform['total'] ?? 0)); ?> مستخدم</span>
            <span><?php echo e(number_format($statsPlatform['students'] ?? 0)); ?> طالب</span>
            <span><?php echo e(number_format($statsPlatform['instructors'] ?? 0)); ?> مدرب</span>
            <span><?php echo e(number_format($statsPlatform['active'] ?? 0)); ?> نشط</span>
            <span class="text-blue-700 font-semibold">+<?php echo e(number_format($statsPlatform['new_this_month'] ?? 0)); ?> هذا الشهر</span>
        </div>
    </section>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        
        <section class="xl:col-span-1 rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden xl:sticky xl:top-4 self-start">
            <div class="px-5 py-4 border-b border-slate-200 bg-slate-50">
                <h3 class="text-sm font-black text-slate-900 flex items-center gap-2">
                    <i class="fas fa-filter text-blue-600"></i>
                    تصفية التقرير
                </h3>
            </div>
            <form method="GET" id="filterForm" class="p-5 space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">بحث</label>
                    <input type="search" name="search" value="<?php echo e($search ?? ''); ?>" placeholder="اسم، بريد، جوال…"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">الفترة</label>
                    <select name="period" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm bg-white focus:ring-2 focus:ring-blue-500">
                        <option value="today" <?php if($period == 'today'): echo 'selected'; endif; ?>>اليوم</option>
                        <option value="week" <?php if($period == 'week'): echo 'selected'; endif; ?>>هذا الأسبوع</option>
                        <option value="month" <?php if($period == 'month'): echo 'selected'; endif; ?>>هذا الشهر</option>
                        <option value="year" <?php if($period == 'year'): echo 'selected'; endif; ?>>هذا العام</option>
                        <option value="all" <?php if($period == 'all'): echo 'selected'; endif; ?>>الكل</option>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">من</label>
                        <input type="date" name="start_date" value="<?php echo e($startDate ? $startDate->format('Y-m-d') : ''); ?>"
                               class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">إلى</label>
                        <input type="date" name="end_date" value="<?php echo e($endDate ? $endDate->format('Y-m-d') : ''); ?>"
                               class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">الدور</label>
                    <select name="role" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm bg-white">
                        <option value="">جميع الأدوار</option>
                        <option value="student" <?php if($role == 'student'): echo 'selected'; endif; ?>>طلاب</option>
                        <option value="instructor" <?php if($role == 'instructor'): echo 'selected'; endif; ?>>مدربون</option>
                        <option value="admin" <?php if($role == 'admin'): echo 'selected'; endif; ?>>إدارة</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">الحالة</label>
                    <select name="status" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm bg-white">
                        <option value="">الكل</option>
                        <option value="active" <?php if(($status ?? '') == 'active'): echo 'selected'; endif; ?>>نشط</option>
                        <option value="inactive" <?php if(($status ?? '') == 'inactive'): echo 'selected'; endif; ?>>غير نشط</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">الاشتراك (طلاب)</label>
                    <select name="subscription" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm bg-white">
                        <option value="">الكل</option>
                        <option value="subscribed" <?php if(($subscription ?? '') == 'subscribed'): echo 'selected'; endif; ?>>مشترك</option>
                        <option value="not_subscribed" <?php if(($subscription ?? '') == 'not_subscribed'): echo 'selected'; endif; ?>>غير مشترك</option>
                    </select>
                </div>
                <div class="flex flex-col gap-2 pt-2">
                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-blue-600 text-white text-sm font-bold hover:bg-blue-700">
                        <i class="fas fa-search"></i>
                        تطبيق
                    </button>
                    <a href="<?php echo e(route('admin.reports.users')); ?>" class="w-full text-center py-2 text-sm font-semibold text-slate-600 hover:text-slate-900">مسح الفلاتر</a>
                </div>
            </form>
        </section>

        <div class="xl:col-span-2 space-y-6">
            
            <?php if($usersByRole->isNotEmpty()): ?>
                <section class="rounded-2xl bg-white border border-slate-200 shadow-lg p-5">
                    <h3 class="text-sm font-black text-slate-900 mb-4">توزيع التسجيلات حسب الدور (الفترة)</h3>
                    <div class="flex flex-wrap gap-3">
                        <?php $__currentLoopData = $usersByRole; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $roleKey = $row->role ?? 'unknown';
                                $roleName = $roleLabels[$roleKey] ?? $roleKey;
                                $pct = ($statsPeriod['total'] ?? 0) > 0 ? round(100 * $row->count / $statsPeriod['total']) : 0;
                            ?>
                            <div class="flex-1 min-w-[120px] rounded-xl border border-slate-200 bg-slate-50/50 p-3">
                                <p class="text-xs font-semibold text-slate-600"><?php echo e($roleName); ?></p>
                                <p class="text-xl font-black text-slate-900"><?php echo e(number_format($row->count)); ?></p>
                                <p class="text-[10px] text-slate-500"><?php echo e($pct); ?>%</p>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </section>
            <?php endif; ?>

            
            <?php if(isset($usersPerMonthChart) && $usersPerMonthChart->isNotEmpty()): ?>
                <section class="rounded-2xl bg-white border border-slate-200 shadow-lg p-5">
                    <h3 class="text-sm font-black text-slate-900 mb-4">التسجيلات الشهرية</h3>
                    <div class="space-y-2">
                        <?php $__currentLoopData = $usersPerMonthChart; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $barPct = round(100 * (int) $row->count / ($maxPerMonth ?? 1));
                                $mLabel = ($monthNames[(int) $row->month] ?? $row->month).' '.$row->year;
                            ?>
                            <div class="flex items-center gap-3 text-sm">
                                <span class="w-24 shrink-0 text-xs font-semibold text-slate-600"><?php echo e($mLabel); ?></span>
                                <div class="flex-1 h-7 bg-slate-100 rounded-lg overflow-hidden">
                                    <div class="h-full bg-gradient-to-l from-blue-500 to-indigo-500 rounded-lg flex items-center justify-end pl-2"
                                         style="width: <?php echo e(max($barPct, $row->count > 0 ? 8 : 0)); ?>%">
                                        <?php if($row->count > 0): ?>
                                            <span class="text-[10px] font-bold text-white"><?php echo e($row->count); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </section>
            <?php endif; ?>

            
            <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 bg-slate-50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h3 class="text-base font-black text-slate-900">قائمة المستخدمين</h3>
                        <p class="text-xs text-slate-500 mt-0.5"><?php echo e($users->total()); ?> نتيجة في الفترة</p>
                    </div>
                    <div class="flex flex-wrap gap-1.5">
                        <?php $__currentLoopData = $quickRoles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $roleKey => $meta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $params = array_merge(request()->except('page', 'role'), ['role' => $roleKey ?: null]);
                                $params = array_filter($params, fn ($v) => $v !== null && $v !== '');
                            ?>
                            <a href="<?php echo e(route('admin.reports.users', $params)); ?>"
                               class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold transition-colors
                               <?php echo e(($role ?? '') === $roleKey ? 'bg-blue-600 text-white' : 'bg-white border border-slate-200 text-slate-700 hover:border-blue-300'); ?>">
                                <i class="fas <?php echo e($meta['icon']); ?> text-[10px]"></i>
                                <?php echo e($meta['label']); ?>

                            </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>

                <?php if($users->count() > 0): ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200 text-right">
                            <thead class="bg-slate-50">
                                <tr class="text-xs font-bold text-slate-600 uppercase tracking-wide">
                                    <th class="px-4 py-3">المستخدم</th>
                                    <th class="px-4 py-3">التواصل</th>
                                    <th class="px-4 py-3">الدور</th>
                                    <th class="px-4 py-3">الحالة</th>
                                    <th class="px-4 py-3">تاريخ التسجيل</th>
                                    <th class="px-4 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr class="hover:bg-blue-50/30 transition-colors">
                                        <td class="px-4 py-3">
                                            <p class="text-sm font-bold text-slate-900"><?php echo e($user->name); ?></p>
                                            <p class="text-[10px] text-slate-400 font-mono">#<?php echo e($user->id); ?></p>
                                        </td>
                                        <td class="px-4 py-3 text-xs text-slate-600">
                                            <span dir="ltr" class="block"><?php echo e($user->phone ?? '—'); ?></span>
                                            <span class="text-slate-500 break-all"><?php echo e($user->email ?? '—'); ?></span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <?php $rl = $roleLabels[$user->role] ?? $user->role; ?>
                                            <span class="inline-flex px-2 py-0.5 rounded-full text-[11px] font-bold border
                                                <?php if($user->role === 'student'): ?> bg-emerald-50 text-emerald-800 border-emerald-200
                                                <?php elseif(in_array($user->role, ['instructor', 'teacher'], true)): ?> bg-amber-50 text-amber-800 border-amber-200
                                                <?php else: ?> bg-purple-50 text-purple-800 border-purple-200
                                                <?php endif; ?>">
                                                <?php echo e($rl); ?>

                                            </span>
                                            <?php if($user->role === 'student' && ($user->active_subscriptions_count ?? 0) > 0): ?>
                                                <span class="block mt-1 text-[10px] text-sky-700 font-semibold">مشترك</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-bold <?php echo e($user->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800'); ?>">
                                                <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                                <?php echo e($user->is_active ? 'نشط' : 'معطّل'); ?>

                                            </span>
                                            <?php if($user->last_login_at): ?>
                                                <p class="text-[10px] text-slate-400 mt-1"><?php echo e($user->last_login_at->diffForHumans()); ?></p>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-4 py-3 text-xs text-slate-600 whitespace-nowrap">
                                            <?php echo e($user->created_at->format('Y-m-d')); ?>

                                            <span class="text-slate-400 block"><?php echo e($user->created_at->format('H:i')); ?></span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="flex flex-col gap-1">
                                                <?php if(Route::has('admin.users.edit')): ?>
                                                    <a href="<?php echo e(route('admin.users.edit', $user->id)); ?>" class="text-xs font-bold text-blue-600 hover:underline">الحساب</a>
                                                <?php endif; ?>
                                                <?php if($user->role === 'student' && Route::has('admin.quality-control.students.show')): ?>
                                                    <a href="<?php echo e(route('admin.quality-control.students.show', $user)); ?>" class="text-xs font-bold text-violet-600 hover:underline">رقابة</a>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                    <?php if($users->hasPages()): ?>
                        <div class="px-5 py-4 border-t border-slate-200"><?php echo e($users->links()); ?></div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="py-16 text-center">
                        <div class="w-14 h-14 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-users text-xl"></i>
                        </div>
                        <p class="text-sm font-bold text-slate-800">لا توجد نتائج</p>
                        <p class="text-xs text-slate-500 mt-1">جرّب توسيع الفترة أو تغيير الفلاتر.</p>
                    </div>
                <?php endif; ?>
            </section>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sana\resources\views/admin/reports/users.blade.php ENDPATH**/ ?>