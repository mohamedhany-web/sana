<?php $__env->startSection('title', 'حسابات الطلاب'); ?>
<?php $__env->startSection('header', 'حسابات الطلاب'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $stats = $stats ?? [];
    $trend = $trends['students'] ?? null;
    $monthNames = [
        1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل',
        5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس',
        9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر',
    ];
    $statCards = [
        [
            'label' => 'إجمالي الطلاب',
            'value' => number_format($stats['total'] ?? 0),
            'icon' => 'fa-user-graduate',
            'bg' => 'bg-violet-100',
            'text' => 'text-violet-600',
            'desc' => 'كل الحسابات المسجّلة كطالب',
        ],
        [
            'label' => 'نشطون',
            'value' => number_format($stats['active'] ?? 0),
            'icon' => 'fa-user-check',
            'bg' => 'bg-emerald-100',
            'text' => 'text-emerald-600',
            'desc' => 'يمكنهم الدخول للمنصة',
        ],
        [
            'label' => 'غير نشطين',
            'value' => number_format($stats['inactive'] ?? 0),
            'icon' => 'fa-user-slash',
            'bg' => 'bg-rose-100',
            'text' => 'text-rose-600',
            'desc' => 'حسابات معطّلة',
        ],
        [
            'label' => 'جدد هذا الشهر',
            'value' => number_format($stats['new_this_month'] ?? 0),
            'icon' => 'fa-user-plus',
            'bg' => 'bg-sky-100',
            'text' => 'text-sky-600',
            'desc' => 'تسجيلات منذ بداية الشهر',
            'trend' => $trend,
        ],
    ];
?>

<div class="space-y-6">
    <?php if(session('success') || request('created') == '1' || request('updated') == '1'): ?>
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm font-medium flex items-center gap-2">
            <i class="fas fa-check-circle"></i>
            <?php echo e(session('success', request('created') ? 'تم إنشاء الحساب بنجاح' : 'تم التحديث بنجاح')); ?>

        </div>
    <?php endif; ?>
    <?php if(session('warning') || !empty($warning)): ?>
        <div class="rounded-xl bg-amber-50 border border-amber-200 text-amber-800 px-4 py-3 text-sm font-medium flex items-center gap-2">
            <i class="fas fa-exclamation-triangle"></i>
            <?php echo e(session('warning', $warning ?? '')); ?>

        </div>
    <?php endif; ?>

    
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-5 bg-gradient-to-l from-violet-50 to-white border-b border-slate-200 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-violet-600 to-indigo-600 flex items-center justify-center text-white shadow-md shadow-violet-500/25">
                    <i class="fas fa-user-graduate text-lg"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-black text-slate-900">حسابات الطلاب</h2>
                    <p class="text-sm text-slate-600 mt-0.5">بحث، متابعة الاشتراكات، وساعات الحصص مع المعلمين</p>
                </div>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="<?php echo e(route('admin.users.create', ['role' => 'student'])); ?>"
                   class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-bold text-white bg-gradient-to-r from-violet-600 to-indigo-600 rounded-xl shadow hover:from-violet-700 hover:to-indigo-700 transition-all">
                    <i class="fas fa-user-plus"></i>
                    إضافة طالب
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 p-6">
            <?php $__currentLoopData = $statCards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-xs font-semibold text-slate-600"><?php echo e($card['label']); ?></p>
                            <p class="text-2xl font-black text-slate-900 mt-1"><?php echo e($card['value']); ?></p>
                            <p class="text-[11px] text-slate-500 mt-1"><?php echo e($card['desc']); ?></p>
                            <?php if(!empty($card['trend'])): ?>
                                <?php
                                    $diff = (int) round($card['trend']['difference']);
                                    $positive = $diff >= 0;
                                ?>
                                <p class="text-xs mt-2 <?php echo e($positive ? 'text-emerald-600' : 'text-rose-600'); ?> font-semibold">
                                    <?php echo e($positive ? '+' : ''); ?><?php echo e(number_format($diff)); ?> عن الشهر السابق
                                    (<?php echo e($positive ? '+' : ''); ?><?php echo e(number_format($card['trend']['percent'], 1)); ?>%)
                                </p>
                            <?php endif; ?>
                        </div>
                        <div class="w-11 h-11 rounded-lg <?php echo e($card['bg']); ?> flex items-center justify-center <?php echo e($card['text']); ?> shrink-0">
                            <i class="fas <?php echo e($card['icon']); ?>"></i>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <div class="px-6 pb-4 flex flex-wrap gap-2 text-xs font-semibold">
            <?php if(($stats['with_subscription'] ?? 0) > 0): ?>
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-indigo-100 text-indigo-800 border border-indigo-200">
                <i class="fas fa-id-card"></i>
                اشتراك نشط: <?php echo e(number_format($stats['with_subscription'])); ?>

            </span>
            <?php endif; ?>
            <?php if(($stats['with_learning_profile'] ?? 0) > 0): ?>
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-violet-100 text-violet-800 border border-violet-200">
                <i class="fas fa-clipboard-list"></i>
                ملف تعلّم: <?php echo e(number_format($stats['with_learning_profile'])); ?>

            </span>
            <?php endif; ?>
        </div>
    </section>

    
    <section class="rounded-2xl bg-white border border-slate-200 shadow-sm p-4">
        <p class="text-xs font-bold text-slate-500 mb-3">اختصارات</p>
        <div class="flex flex-wrap gap-2">
            <?php if(Route::has('admin.subscriptions.index')): ?>
            <a href="<?php echo e(route('admin.subscriptions.index')); ?>" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl text-sm font-semibold border border-slate-200 text-slate-700 hover:border-violet-300 hover:bg-violet-50 transition-colors">
                <i class="fas fa-calendar-check text-violet-600"></i> الاشتراكات
            </a>
            <?php endif; ?>
            <?php if(Route::has('admin.tutor-lessons.index')): ?>
            <a href="<?php echo e(route('admin.tutor-lessons.index')); ?>" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl text-sm font-semibold border border-slate-200 text-slate-700 hover:border-violet-300 hover:bg-violet-50 transition-colors">
                <i class="fas fa-user-clock text-violet-600"></i> حصص المعلمين
            </a>
            <?php endif; ?>
            <?php if(Route::has('admin.tutor-lessons.settings')): ?>
            <a href="<?php echo e(route('admin.tutor-lessons.settings')); ?>" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl text-sm font-semibold border border-slate-200 text-slate-700 hover:border-violet-300 hover:bg-violet-50 transition-colors">
                <i class="fas fa-cog text-violet-600"></i> إعدادات الحصص
            </a>
            <?php endif; ?>
            <?php if(Route::has('admin.students-control.consumption')): ?>
            <a href="<?php echo e(route('admin.students-control.consumption')); ?>" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl text-sm font-semibold border border-slate-200 text-slate-700 hover:border-violet-300 hover:bg-violet-50 transition-colors">
                <i class="fas fa-chart-pie text-violet-600"></i> الاستهلاك
            </a>
            <?php endif; ?>
            <?php if(Route::has('admin.quality-control.students')): ?>
            <a href="<?php echo e(route('admin.quality-control.students')); ?>" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl text-sm font-semibold border border-slate-200 text-slate-700 hover:border-violet-300 hover:bg-violet-50 transition-colors">
                <i class="fas fa-shield-alt text-violet-600"></i> مراقبة الطلاب
            </a>
            <?php endif; ?>
        </div>
    </section>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-2 space-y-6">
            
            <section class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/80">
                    <h3 class="text-sm font-bold text-slate-900">بحث وتصفية</h3>
                </div>
                <form method="GET" action="<?php echo e(route('admin.students-accounts.index')); ?>" class="p-5 grid grid-cols-1 md:grid-cols-12 gap-4">
                    <div class="md:col-span-7">
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">بحث</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 right-3 flex items-center text-slate-400 pointer-events-none">
                                <i class="fas fa-search text-sm"></i>
                            </span>
                            <input type="text" name="search" value="<?php echo e(request('search')); ?>"
                                   placeholder="الاسم، البريد، أو الهاتف"
                                   class="w-full rounded-xl border border-slate-300 bg-white pr-10 pl-4 py-2.5 text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500">
                        </div>
                    </div>
                    <div class="md:col-span-3">
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">الحالة</label>
                        <select name="status" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-violet-500">
                            <option value="">الكل</option>
                            <option value="1" <?php if(request('status') === '1'): echo 'selected'; endif; ?>>نشط</option>
                            <option value="0" <?php if(request('status') === '0'): echo 'selected'; endif; ?>>غير نشط</option>
                        </select>
                    </div>
                    <div class="md:col-span-2 flex items-end gap-2">
                        <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 bg-violet-600 hover:bg-violet-700 text-white px-4 py-2.5 rounded-xl text-sm font-bold">
                            <i class="fas fa-filter"></i> تطبيق
                        </button>
                        <?php if(request()->anyFilled(['search', 'status'])): ?>
                        <a href="<?php echo e(route('admin.students-accounts.index')); ?>" class="px-3 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50" title="مسح">
                            <i class="fas fa-times"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                </form>
            </section>

            
            <section class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 flex flex-wrap items-center justify-between gap-2">
                    <h3 class="text-sm font-bold text-slate-900">
                        قائمة الطلاب
                        <span class="text-violet-600 font-black">(<?php echo e($users->total()); ?>)</span>
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-slate-50 text-xs font-bold uppercase tracking-wide text-slate-600">
                            <tr>
                                <th class="px-5 py-3 text-right">الطالب</th>
                                <th class="px-5 py-3 text-right">الاشتراك / الساعات</th>
                                <th class="px-5 py-3 text-right">الحالة</th>
                                <th class="px-5 py-3 text-right">التسجيل</th>
                                <th class="px-5 py-3 text-center">إجراءات</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <?php
                                    $profile = $user->studentLearningProfile;
                                    $sub = $user->relationLoaded('subscriptions')
                                        ? $user->subscriptions->first()
                                        : null;
                                    $quota = $profile?->lesson_hours_quota;
                                    $used = (int) ($profile?->lesson_hours_used ?? 0);
                                ?>
                                <tr class="hover:bg-violet-50/40 transition-colors">
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-violet-500 to-indigo-600 flex items-center justify-center text-white font-bold shadow-sm">
                                                <?php echo e(mb_substr($user->name, 0, 1, 'UTF-8')); ?>

                                            </div>
                                            <div class="min-w-0">
                                                <p class="font-bold text-slate-900 truncate"><?php echo e($user->name); ?></p>
                                                <p class="text-xs text-slate-500 truncate"><?php echo e($user->phone ?: '—'); ?></p>
                                                <?php if($user->email): ?>
                                                    <p class="text-xs text-slate-400 truncate"><?php echo e($user->email); ?></p>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <?php if($sub): ?>
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-xs font-semibold bg-indigo-100 text-indigo-800">
                                                <i class="fas fa-id-card text-[10px]"></i>
                                                <?php echo e(\Illuminate\Support\Str::limit($sub->plan_name, 22)); ?>

                                            </span>
                                        <?php else: ?>
                                            <span class="text-xs text-slate-400">بدون اشتراك نشط</span>
                                        <?php endif; ?>
                                        <?php if($profile && $quota !== null): ?>
                                            <p class="text-xs text-slate-600 mt-1">
                                                ساعات: <span class="font-bold"><?php echo e($used); ?></span>
                                                / <?php echo e($quota < 0 ? '∞' : $quota); ?>

                                            </p>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-5 py-4">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold <?php echo e($user->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700'); ?>">
                                            <?php echo e($user->is_active ? 'نشط' : 'معطّل'); ?>

                                        </span>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap text-slate-600">
                                        <div><?php echo e($user->created_at->format('Y-m-d')); ?></div>
                                        <div class="text-xs text-slate-400"><?php echo e($user->created_at->diffForHumans()); ?></div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="flex items-center justify-center gap-1">
                                            <a href="<?php echo e(route('admin.users.show', $user)); ?>" class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200" title="عرض">
                                                <i class="fas fa-eye text-xs"></i>
                                            </a>
                                            <a href="<?php echo e(route('admin.users.edit', $user)); ?>" class="w-8 h-8 flex items-center justify-center rounded-lg bg-violet-100 text-violet-700 hover:bg-violet-200" title="تعديل">
                                                <i class="fas fa-edit text-xs"></i>
                                            </a>
                                            <?php if($user->id !== auth()->id()): ?>
                                            <button type="button" onclick="deleteStudent(this)"
                                                    data-delete-url="<?php echo e(route('admin.users.delete', $user)); ?>"
                                                    class="w-8 h-8 flex items-center justify-center rounded-lg bg-rose-100 text-rose-700 hover:bg-rose-200" title="حذف">
                                                <i class="fas fa-trash text-xs"></i>
                                            </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="5" class="px-5 py-16 text-center">
                                        <div class="w-14 h-14 mx-auto rounded-2xl bg-violet-100 text-violet-600 flex items-center justify-center mb-3">
                                            <i class="fas fa-user-graduate text-2xl"></i>
                                        </div>
                                        <p class="font-bold text-slate-800">لا يوجد طلاب مطابقون</p>
                                        <p class="text-sm text-slate-500 mt-1">جرّب تغيير البحث أو أضف طالباً جديداً</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <?php if($users->hasPages()): ?>
                    <div class="px-5 py-4 border-t border-slate-100 bg-slate-50/50">
                        <?php echo e($users->links()); ?>

                    </div>
                <?php endif; ?>
            </section>
        </div>

        
        <aside class="space-y-6">
            <?php if(isset($usersByMonth) && $usersByMonth->count() > 0): ?>
            <section class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/80">
                    <h3 class="text-sm font-bold text-slate-900">تسجيلات آخر 6 أشهر</h3>
                </div>
                <div class="p-5 space-y-3">
                    <?php $maxCount = max(1, (int) $usersByMonth->max('count')); ?>
                    <?php $__currentLoopData = $usersByMonth->reverse(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $pct = ((int) $row->count / $maxCount) * 100;
                            $m = $monthNames[(int) $row->month] ?? $row->month;
                        ?>
                        <div>
                            <div class="flex justify-between text-xs mb-1">
                                <span class="font-semibold text-slate-700"><?php echo e($m); ?> <?php echo e($row->year); ?></span>
                                <span class="font-bold text-violet-600"><?php echo e($row->count); ?></span>
                            </div>
                            <div class="h-2 rounded-full bg-slate-100 overflow-hidden">
                                <div class="h-full rounded-full bg-gradient-to-l from-violet-500 to-indigo-500" style="width: <?php echo e($pct); ?>%"></div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </section>
            <?php endif; ?>

            <section class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/80">
                    <h3 class="text-sm font-bold text-slate-900">آخر التسجيلات</h3>
                </div>
                <div class="p-4 space-y-2 max-h-80 overflow-y-auto">
                    <?php $__empty_1 = true; $__currentLoopData = $recentUsers ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $recent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <a href="<?php echo e(route('admin.users.show', $recent)); ?>" class="flex items-center gap-3 p-2 rounded-xl hover:bg-violet-50 transition-colors">
                            <div class="w-9 h-9 rounded-lg bg-violet-100 text-violet-700 flex items-center justify-center font-bold text-sm">
                                <?php echo e(mb_substr($recent->name, 0, 1, 'UTF-8')); ?>

                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-semibold text-slate-900 truncate"><?php echo e($recent->name); ?></p>
                                <p class="text-xs text-slate-500"><?php echo e($recent->created_at->diffForHumans()); ?></p>
                            </div>
                            <span class="w-2 h-2 rounded-full <?php echo e($recent->is_active ? 'bg-emerald-500' : 'bg-slate-300'); ?>"></span>
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="text-sm text-slate-500 text-center py-4">لا يوجد طلاب بعد</p>
                    <?php endif; ?>
                </div>
            </section>
        </aside>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function deleteStudent(btn) {
    const url = btn?.getAttribute?.('data-delete-url');
    if (!url || !confirm('حذف هذا الطالب؟ لا يمكن التراجع عن الحذف.')) return;
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (!token) return alert('انتهت الجلسة. حدّث الصفحة.');
    fetch(url, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        },
    })
    .then(r => r.json().catch(() => ({})).then(d => ({ ok: r.ok, data: d })))
    .then(({ ok, data }) => {
        if (ok && data.success !== false) {
            window.location.reload();
            return;
        }
        alert(data.message || 'تعذّر الحذف');
    })
    .catch(() => alert('تعذّر الاتصال بالخادم'));
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sana\resources\views/admin/students-accounts/index.blade.php ENDPATH**/ ?>