<?php $__env->startSection('title', 'التسجيلات الأونلاين'); ?>
<?php $__env->startSection('header', 'التسجيلات الأونلاين'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $statusBadge = [
        'pending' => 'bg-amber-100 text-amber-800 border-amber-200',
        'active' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
        'completed' => 'bg-sky-100 text-sky-800 border-sky-200',
        'suspended' => 'bg-rose-100 text-rose-800 border-rose-200',
    ];
    $statCards = [
        ['label' => 'إجمالي التسجيلات', 'value' => $stats['total'] ?? 0, 'icon' => 'fa-laptop', 'bg' => 'bg-sky-100', 'text' => 'text-sky-600', 'desc' => 'كل تسجيلات الكورسات'],
        ['label' => 'في الانتظار', 'value' => $stats['pending'] ?? 0, 'icon' => 'fa-clock', 'bg' => 'bg-amber-100', 'text' => 'text-amber-600', 'desc' => 'بانتظار التفعيل'],
        ['label' => 'نشطة', 'value' => $stats['active'] ?? 0, 'icon' => 'fa-play-circle', 'bg' => 'bg-emerald-100', 'text' => 'text-emerald-600', 'desc' => 'يتعلّم حالياً'],
        ['label' => 'مكتملة', 'value' => $stats['completed'] ?? 0, 'icon' => 'fa-graduation-cap', 'bg' => 'bg-indigo-100', 'text' => 'text-indigo-600', 'desc' => 'أنهى المسار'],
    ];
?>

<div class="space-y-6">
    <?php if(session('success')): ?>
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm font-medium flex items-center gap-2">
            <i class="fas fa-check-circle"></i><?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="rounded-xl bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 text-sm font-medium flex items-center gap-2">
            <i class="fas fa-times-circle"></i><?php echo e(session('error')); ?>

        </div>
    <?php endif; ?>
    <?php if($errors->any()): ?>
        <div class="rounded-xl bg-amber-50 border border-amber-200 text-amber-900 px-4 py-3 text-sm">
            <p class="font-bold mb-1 flex items-center gap-2"><i class="fas fa-exclamation-triangle"></i> تحقق من الحقول</p>
            <ul class="list-disc list-inside space-y-0.5 text-xs">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $err): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($err); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-5 bg-gradient-to-l from-sky-50 to-white border-b border-slate-200 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-sky-600 to-cyan-600 flex items-center justify-center text-white shadow-md shadow-sky-500/25">
                    <i class="fas fa-laptop text-lg"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-black text-slate-900">التسجيلات الأونلاين</h2>
                    <p class="text-sm text-slate-600 mt-0.5">تسجيل الطلاب في الكورسات، التفعيل، ومتابعة التقدّم</p>
                </div>
            </div>
            <a href="<?php echo e(route('admin.online-enrollments.create')); ?>"
               class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-bold text-white bg-gradient-to-r from-sky-600 to-cyan-600 rounded-xl shadow hover:from-sky-700 hover:to-cyan-700 transition-all">
                <i class="fas fa-user-plus"></i>
                <?php echo e(__('admin.enroll_student_in_course')); ?>

            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 p-6">
            <?php $__currentLoopData = $statCards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-xs font-semibold text-slate-600"><?php echo e($card['label']); ?></p>
                            <p class="text-2xl font-black text-slate-900 mt-1"><?php echo e(number_format($card['value'])); ?></p>
                            <p class="text-[11px] text-slate-500 mt-1"><?php echo e($card['desc']); ?></p>
                        </div>
                        <div class="w-11 h-11 rounded-lg <?php echo e($card['bg']); ?> flex items-center justify-center <?php echo e($card['text']); ?> shrink-0">
                            <i class="fas <?php echo e($card['icon']); ?>"></i>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <?php if(($stats['suspended'] ?? 0) > 0): ?>
        <div class="px-6 pb-4">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-rose-100 text-rose-800 border border-rose-200 text-xs font-semibold">
                <i class="fas fa-pause-circle"></i>
                معلّقة: <?php echo e(number_format($stats['suspended'])); ?>

            </span>
        </div>
        <?php endif; ?>
    </section>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-2 space-y-6">
            
            <section class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/80">
                    <h3 class="text-sm font-bold text-slate-900">بحث وتصفية</h3>
                </div>
                <form method="GET" action="<?php echo e(route('admin.online-enrollments.index')); ?>" class="p-5 grid grid-cols-1 md:grid-cols-12 gap-4">
                    <div class="md:col-span-4">
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">بحث</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 right-3 flex items-center text-slate-400 pointer-events-none">
                                <i class="fas fa-search text-sm"></i>
                            </span>
                            <input type="text" name="search" value="<?php echo e(request('search')); ?>"
                                   placeholder="الاسم، البريد، أو الهاتف"
                                   class="w-full rounded-xl border border-slate-300 bg-white pr-10 pl-4 py-2.5 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                        </div>
                    </div>
                    <div class="md:col-span-3">
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">الحالة</label>
                        <select name="status" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-sky-500">
                            <option value="">الكل</option>
                            <option value="pending" <?php if(request('status') === 'pending'): echo 'selected'; endif; ?>>في الانتظار</option>
                            <option value="active" <?php if(request('status') === 'active'): echo 'selected'; endif; ?>>نشط</option>
                            <option value="completed" <?php if(request('status') === 'completed'): echo 'selected'; endif; ?>>مكتمل</option>
                            <option value="suspended" <?php if(request('status') === 'suspended'): echo 'selected'; endif; ?>>معلق</option>
                        </select>
                    </div>
                    <div class="md:col-span-3">
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">الكورس</label>
                        <select name="course_id" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-sky-500">
                            <option value="">جميع الكورسات</option>
                            <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($course->id); ?>" <?php if((string) request('course_id') === (string) $course->id): echo 'selected'; endif; ?>>
                                    <?php echo e($course->title); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="md:col-span-2 flex items-end gap-2">
                        <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 bg-sky-600 hover:bg-sky-700 text-white px-4 py-2.5 rounded-xl text-sm font-bold">
                            <i class="fas fa-filter"></i> تطبيق
                        </button>
                        <?php if(request()->anyFilled(['search', 'status', 'course_id'])): ?>
                        <a href="<?php echo e(route('admin.online-enrollments.index')); ?>" class="px-3 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50" title="مسح">
                            <i class="fas fa-times"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                </form>
            </section>

            
            <section class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 flex flex-wrap items-center justify-between gap-2">
                    <h3 class="text-sm font-bold text-slate-900">
                        قائمة التسجيلات
                        <span class="text-sky-600 font-black">(<?php echo e($enrollments->total()); ?>)</span>
                    </h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-slate-50 text-xs font-bold uppercase tracking-wide text-slate-600">
                            <tr>
                                <th class="px-5 py-3 text-right">الطالب</th>
                                <th class="px-5 py-3 text-right">الكورس</th>
                                <th class="px-5 py-3 text-right">الحالة</th>
                                <th class="px-5 py-3 text-right">التقدّم</th>
                                <th class="px-5 py-3 text-right">التسجيل</th>
                                <th class="px-5 py-3 text-center">إجراءات</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php $__empty_1 = true; $__currentLoopData = $enrollments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $enrollment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <?php
                                    $badge = $statusBadge[$enrollment->status] ?? 'bg-slate-100 text-slate-700 border-slate-200';
                                    $progress = min(100, max(0, (int) $enrollment->progress));
                                ?>
                                <tr class="hover:bg-sky-50/40 transition-colors">
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-sky-500 to-cyan-600 flex items-center justify-center text-white font-bold shadow-sm">
                                                <?php echo e(mb_substr($enrollment->student->name ?? '?', 0, 1, 'UTF-8')); ?>

                                            </div>
                                            <div class="min-w-0">
                                                <p class="font-bold text-slate-900 truncate"><?php echo e($enrollment->student->name ?? '—'); ?></p>
                                                <p class="text-xs text-slate-500 truncate"><?php echo e($enrollment->student->phone ?? '—'); ?></p>
                                                <?php if($enrollment->student?->email): ?>
                                                    <p class="text-xs text-slate-400 truncate"><?php echo e($enrollment->student->email); ?></p>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <p class="font-semibold text-slate-900"><?php echo e($enrollment->course->title ?? '—'); ?></p>
                                        <p class="text-xs text-slate-500 mt-0.5">
                                            <?php echo e($enrollment->course?->academicYear?->name ?? '—'); ?>

                                            · <?php echo e($enrollment->course?->academicSubject?->name ?? '—'); ?>

                                        </p>
                                    </td>
                                    <td class="px-5 py-4">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold border <?php echo e($badge); ?>">
                                            <?php echo e($enrollment->status_text); ?>

                                        </span>
                                    </td>
                                    <td class="px-5 py-4 min-w-[120px]">
                                        <div class="flex items-center gap-2">
                                            <div class="flex-1 h-2 rounded-full bg-slate-100 overflow-hidden">
                                                <div class="h-full rounded-full bg-gradient-to-l from-sky-500 to-cyan-500" style="width: <?php echo e($progress); ?>%"></div>
                                            </div>
                                            <span class="text-xs font-bold text-slate-600 w-9 text-left"><?php echo e($progress); ?>%</span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap text-slate-600">
                                        <div><?php echo e($enrollment->enrolled_at?->format('Y-m-d') ?? '—'); ?></div>
                                        <?php if($enrollment->enrolled_at): ?>
                                            <div class="text-xs text-slate-400"><?php echo e($enrollment->enrolled_at->diffForHumans()); ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="flex items-center justify-center gap-1">
                                            <a href="<?php echo e(route('admin.online-enrollments.show', $enrollment)); ?>"
                                               class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200" title="عرض">
                                                <i class="fas fa-eye text-xs"></i>
                                            </a>
                                            <?php if($enrollment->status === 'pending'): ?>
                                                <form method="POST" action="<?php echo e(route('admin.online-enrollments.activate', $enrollment)); ?>" class="inline">
                                                    <?php echo csrf_field(); ?>
                                                    <button type="submit" onclick="return confirm('تفعيل هذا التسجيل؟')"
                                                            class="w-8 h-8 flex items-center justify-center rounded-lg bg-emerald-100 text-emerald-700 hover:bg-emerald-200" title="تفعيل">
                                                        <i class="fas fa-play text-xs"></i>
                                                    </button>
                                                </form>
                                            <?php elseif($enrollment->status === 'active'): ?>
                                                <form method="POST" action="<?php echo e(route('admin.online-enrollments.deactivate', $enrollment)); ?>" class="inline">
                                                    <?php echo csrf_field(); ?>
                                                    <button type="submit" onclick="return confirm('إيقاف هذا التسجيل؟')"
                                                            class="w-8 h-8 flex items-center justify-center rounded-lg bg-amber-100 text-amber-700 hover:bg-amber-200" title="إيقاف">
                                                        <i class="fas fa-pause text-xs"></i>
                                                    </button>
                                                </form>
                                            <?php elseif($enrollment->status === 'suspended'): ?>
                                                <form method="POST" action="<?php echo e(route('admin.online-enrollments.activate', $enrollment)); ?>" class="inline">
                                                    <?php echo csrf_field(); ?>
                                                    <button type="submit" onclick="return confirm('إعادة تفعيل التسجيل؟')"
                                                            class="w-8 h-8 flex items-center justify-center rounded-lg bg-emerald-100 text-emerald-700 hover:bg-emerald-200" title="إعادة تفعيل">
                                                        <i class="fas fa-redo text-xs"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                            <form method="POST" action="<?php echo e(route('admin.online-enrollments.destroy', $enrollment)); ?>" class="inline">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="submit" onclick="return confirm('حذف هذا التسجيل؟')"
                                                        class="w-8 h-8 flex items-center justify-center rounded-lg bg-rose-100 text-rose-700 hover:bg-rose-200" title="حذف">
                                                    <i class="fas fa-trash text-xs"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="6" class="px-5 py-16 text-center">
                                        <div class="w-14 h-14 mx-auto rounded-2xl bg-sky-100 text-sky-600 flex items-center justify-center mb-3">
                                            <i class="fas fa-laptop text-2xl"></i>
                                        </div>
                                        <p class="font-bold text-slate-800">لا توجد تسجيلات</p>
                                        <p class="text-sm text-slate-500 mt-1">غيّر الفلاتر أو أضف تسجيلاً جديداً</p>
                                        <a href="<?php echo e(route('admin.online-enrollments.create')); ?>"
                                           class="inline-flex items-center gap-2 mt-4 px-4 py-2 text-sm font-bold text-white bg-sky-600 rounded-xl hover:bg-sky-700">
                                            <i class="fas fa-user-plus"></i> <?php echo e(__('admin.enroll_student_in_course')); ?>

                                        </a>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if($enrollments->hasPages()): ?>
                    <div class="px-5 py-4 border-t border-slate-100 bg-slate-50/50">
                        <?php echo e($enrollments->appends(request()->query())->links()); ?>

                    </div>
                <?php endif; ?>
            </section>
        </div>

        
        <aside class="space-y-6">
            
            <section class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 bg-gradient-to-l from-amber-50 to-white">
                    <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                        <i class="fas fa-bolt text-amber-500"></i>
                        تفعيل سريع بالبريد
                    </h3>
                    <p class="text-xs text-slate-500 mt-1">إنشاء أو تفعيل التسجيل وإرسال بريد للطالب</p>
                </div>
                <form method="POST" action="<?php echo e(route('admin.online-enrollments.quick-activate')); ?>" class="p-5 space-y-4">
                    <?php echo csrf_field(); ?>
                    <div>
                        <label for="quick_email" class="block text-xs font-semibold text-slate-600 mb-1.5">بريد الطالب</label>
                        <input type="email" name="email" id="quick_email" value="<?php echo e(old('email')); ?>"
                               placeholder="student@example.com"
                               class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:ring-2 focus:ring-sky-500">
                        <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <?php $__errorArgs = ['quick_activate_email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div>
                        <label for="quick_course" class="block text-xs font-semibold text-slate-600 mb-1.5">الكورس</label>
                        <select name="advanced_course_id" id="quick_course"
                                class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:ring-2 focus:ring-sky-500">
                            <option value="">اختر الكورس</option>
                            <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($course->id); ?>" <?php if(old('advanced_course_id') == $course->id): echo 'selected'; endif; ?>><?php echo e($course->title); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['advanced_course_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <button type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-bold text-white bg-gradient-to-r from-emerald-600 to-teal-600 rounded-xl hover:from-emerald-700 hover:to-teal-700 shadow-sm">
                        <i class="fas fa-check-circle"></i>
                        تفعيل الآن
                    </button>
                </form>
            </section>

            
            <section class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/80">
                    <h3 class="text-sm font-bold text-slate-900">بحث سريع بالهاتف</h3>
                </div>
                <div class="p-5 space-y-3">
                    <input type="text" id="quickSearchPhone" placeholder="رقم هاتف الطالب..."
                           class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:ring-2 focus:ring-sky-500">
                    <button type="button" onclick="quickSearchByPhone()"
                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-bold text-white bg-sky-600 rounded-xl hover:bg-sky-700">
                        <i class="fas fa-search"></i>
                        بحث
                    </button>
                    <div id="quickSearchResult" class="hidden"></div>
                </div>
            </section>

            <?php if(Route::has('admin.advanced-courses.index')): ?>
            <section class="rounded-2xl bg-white border border-slate-200 shadow-sm p-4">
                <p class="text-xs font-bold text-slate-500 mb-3">اختصارات</p>
                <a href="<?php echo e(route('admin.advanced-courses.index')); ?>"
                   class="inline-flex items-center gap-2 px-3 py-2 rounded-xl text-sm font-semibold border border-slate-200 text-slate-700 hover:border-sky-300 hover:bg-sky-50 w-full">
                    <i class="fas fa-book text-sky-600"></i>
                    إدارة الكورسات
                </a>
                <?php if(Route::has('admin.students-accounts.index')): ?>
                <a href="<?php echo e(route('admin.students-accounts.index')); ?>"
                   class="inline-flex items-center gap-2 px-3 py-2 rounded-xl text-sm font-semibold border border-slate-200 text-slate-700 hover:border-sky-300 hover:bg-sky-50 w-full mt-2">
                    <i class="fas fa-user-graduate text-sky-600"></i>
                    حسابات الطلاب
                </a>
                <?php endif; ?>
            </section>
            <?php endif; ?>
        </aside>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function quickSearchByPhone() {
    const phone = document.getElementById('quickSearchPhone')?.value?.trim();
    const resultDiv = document.getElementById('quickSearchResult');
    if (!phone) {
        alert('يرجى إدخال رقم الهاتف');
        return;
    }
    resultDiv.innerHTML = '<p class="text-center text-sm text-sky-600 py-3"><i class="fas fa-spinner fa-spin"></i> جاري البحث...</p>';
    resultDiv.classList.remove('hidden');

    fetch(`<?php echo e(route('admin.online-enrollments.search-by-phone')); ?>?phone=${encodeURIComponent(phone)}`, {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        credentials: 'same-origin',
    })
    .then(async (response) => {
        const data = await response.json().catch(() => ({}));
        return { ok: response.ok, data };
    })
    .then(({ ok, data }) => {
        if (ok && data.success && data.student) {
            const s = data.student;
            resultDiv.innerHTML = `
                <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-4 text-sm">
                    <p class="font-bold text-emerald-900 mb-2">تم العثور على الطالب</p>
                    <p class="text-slate-700"><span class="text-slate-500">الاسم:</span> ${s.name}</p>
                    <p class="text-slate-700 mt-1"><span class="text-slate-500">الهاتف:</span> ${s.phone || '—'}</p>
                    <a href="<?php echo e(route('admin.online-enrollments.create')); ?>?student_id=${s.id}"
                       class="mt-3 inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-white bg-emerald-600 rounded-lg hover:bg-emerald-700">
                        <i class="fas fa-user-plus"></i> <?php echo e(__('admin.enroll_student_in_course')); ?>

                    </a>
                </div>`;
            return;
        }
        const msg = data.error || data.message || 'لم يُعثر على طالب بهذا الرقم';
        resultDiv.innerHTML = `<div class="rounded-xl bg-rose-50 border border-rose-200 p-4 text-sm font-medium text-rose-800">${msg}</div>`;
    })
    .catch(() => {
        resultDiv.innerHTML = '<div class="rounded-xl bg-rose-50 border border-rose-200 p-4 text-sm text-rose-800">حدث خطأ في البحث</div>';
    });
}

document.getElementById('quickSearchPhone')?.addEventListener('keypress', function (e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        quickSearchByPhone();
    }
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sana\resources\views/admin/online-enrollments/index.blade.php ENDPATH**/ ?>