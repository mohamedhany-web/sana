<?php $__env->startSection('title', 'رقابة الطالب — ' . $student->name); ?>
<?php $__env->startSection('header', 'رقابة الطالب'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $profile = $student->studentLearningProfile;
    $matchingLabels = [
        'assisted' => 'بمساعدة الإدارة',
        'self_schedule' => 'حجز ذاتي',
        'pick_teacher' => 'اختيار معلم',
    ];
    $ticketStatusLabels = \App\Models\SupportTicket::statusLabels();
    $subscriptionStatusLabels = [
        'active' => 'نشط',
        'expired' => 'منتهي',
        'cancelled' => 'ملغي',
    ];
?>

<div class="w-full space-y-6">
    <nav class="text-sm text-slate-500 flex flex-wrap items-center gap-1">
        <a href="<?php echo e(route('admin.quality-control.index')); ?>" class="text-violet-600 hover:text-violet-800 font-semibold">الرقابة والجودة</a>
        <span>/</span>
        <a href="<?php echo e(route('admin.quality-control.students')); ?>" class="text-violet-600 hover:text-violet-800 font-semibold">الطلاب</a>
        <span>/</span>
        <span class="text-slate-700 font-medium"><?php echo e($student->name); ?></span>
    </nav>

    
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 border-b border-slate-200 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <?php if($student->profile_image): ?>
                    <img src="<?php echo e($student->profile_image_url); ?>" alt="" class="w-16 h-16 rounded-2xl object-cover border border-slate-200">
                <?php else: ?>
                    <div class="w-16 h-16 rounded-2xl bg-violet-100 flex items-center justify-center">
                        <i class="fas fa-user-graduate text-violet-600 text-2xl"></i>
                    </div>
                <?php endif; ?>
                <div>
                    <h1 class="text-2xl font-black text-slate-900"><?php echo e($student->name); ?></h1>
                    <p class="text-sm text-slate-500" dir="ltr"><?php echo e($student->phone ?? '—'); ?> · <?php echo e($student->email ?? '—'); ?></p>
                    <p class="text-xs text-slate-500 mt-1">
                        آخر دخول: <?php echo e($student->last_login_at ? $student->last_login_at->format('Y-m-d H:i') . ' (' . $student->last_login_at->diffForHumans() . ')' : '—'); ?>

                    </p>
                </div>
            </div>
            <div class="flex flex-wrap gap-2">
                <?php if(Route::has('admin.users.edit')): ?>
                    <a href="<?php echo e(route('admin.users.edit', $student->id)); ?>"
                       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-bold text-slate-700 hover:bg-slate-50">
                        <i class="fas fa-user-cog"></i>
                        تعديل الحساب
                    </a>
                <?php endif; ?>
                <?php if(Route::has('admin.students-accounts.index')): ?>
                    <a href="<?php echo e(route('admin.students-accounts.index', ['search' => $student->phone ?? $student->name])); ?>"
                       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-violet-200 text-sm font-bold text-violet-700 bg-violet-50 hover:bg-violet-100">
                        <i class="fas fa-id-card"></i>
                        حسابات الطلاب
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </section>

    
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200">
            <h2 class="text-lg font-black text-slate-900">ملخص النشاط</h2>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 p-5 sm:p-6">
            <div class="rounded-xl border border-violet-200 bg-violet-50/50 p-3">
                <p class="text-[11px] font-bold text-violet-700">تسجيلات كورس</p>
                <p class="text-xl font-black text-slate-900"><?php echo e($enrollmentStats['total']); ?></p>
                <p class="text-[10px] text-slate-500">نشط <?php echo e($enrollmentStats['active']); ?> · مكتمل <?php echo e($enrollmentStats['completed']); ?></p>
            </div>
            <div class="rounded-xl border border-sky-200 bg-sky-50/50 p-3">
                <p class="text-[11px] font-bold text-sky-700">حصص مع معلم</p>
                <p class="text-xl font-black text-slate-900"><?php echo e($bookingStats['total']); ?></p>
                <p class="text-[10px] text-slate-500">مكتمل <?php echo e($bookingStats['completed']); ?> · قادم <?php echo e($bookingStats['upcoming']); ?></p>
            </div>
            <div class="rounded-xl border border-emerald-200 bg-emerald-50/50 p-3">
                <p class="text-[11px] font-bold text-emerald-700">اشتراكات</p>
                <p class="text-xl font-black text-slate-900"><?php echo e($subscriptions->count()); ?></p>
                <p class="text-[10px] text-slate-500"><?php echo e($activeSubscription ? 'باقة نشطة' : 'لا يوجد نشط'); ?></p>
            </div>
            <div class="rounded-xl border border-amber-200 bg-amber-50/50 p-3">
                <p class="text-[11px] font-bold text-amber-800">تذاكر دعم</p>
                <p class="text-xl font-black text-slate-900"><?php echo e($supportTickets->count()); ?></p>
                <p class="text-[10px] text-slate-500"><?php echo e($openSupportCount); ?> مفتوحة</p>
            </div>
            <div class="rounded-xl border border-indigo-200 bg-indigo-50/50 p-3">
                <p class="text-[11px] font-bold text-indigo-700">اختبارات</p>
                <p class="text-xl font-black text-slate-900"><?php echo e($examAttempts->count()); ?></p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-slate-50/50 p-3">
                <p class="text-[11px] font-bold text-slate-600">شهادات</p>
                <p class="text-xl font-black text-slate-900"><?php echo e($certificates->count()); ?></p>
            </div>
        </div>
    </section>

    
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200">
            <h2 class="text-lg font-black text-slate-900">البيانات الشخصية</h2>
        </div>
        <div class="p-5 sm:p-8">
            <dl class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-4 text-sm">
                <div><dt class="text-slate-500 text-xs font-semibold mb-0.5">الاسم</dt><dd class="font-bold text-slate-900"><?php echo e($student->name); ?></dd></div>
                <div><dt class="text-slate-500 text-xs font-semibold mb-0.5">البريد</dt><dd class="text-slate-900 break-all"><?php echo e($student->email ?? '—'); ?></dd></div>
                <div><dt class="text-slate-500 text-xs font-semibold mb-0.5">الجوال</dt><dd class="text-slate-900" dir="ltr"><?php echo e($student->phone ?? '—'); ?></dd></div>
                <div><dt class="text-slate-500 text-xs font-semibold mb-0.5">السنة الدراسية</dt><dd class="text-slate-900"><?php echo e($student->academicYear->name ?? '—'); ?></dd></div>
                <div><dt class="text-slate-500 text-xs font-semibold mb-0.5">تاريخ الميلاد</dt><dd class="text-slate-900"><?php echo e($student->birth_date ? $student->birth_date->format('Y-m-d') : '—'); ?></dd></div>
                <div><dt class="text-slate-500 text-xs font-semibold mb-0.5">الحالة</dt>
                    <dd><span class="inline-flex px-2 py-0.5 rounded-full text-xs font-bold <?php echo e($student->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800'); ?>"><?php echo e($student->is_active ? 'نشط' : 'معطّل'); ?></span></dd>
                </div>
                <div class="md:col-span-2 lg:col-span-3"><dt class="text-slate-500 text-xs font-semibold mb-0.5">العنوان</dt><dd class="text-slate-900"><?php echo e($student->address ?? '—'); ?></dd></div>
                <div><dt class="text-slate-500 text-xs font-semibold mb-0.5">تاريخ التسجيل</dt><dd class="text-slate-900"><?php echo e($student->created_at->format('Y-m-d H:i')); ?></dd></div>
                <div><dt class="text-slate-500 text-xs font-semibold mb-0.5">آخر تحديث للحساب</dt><dd class="text-slate-900"><?php echo e($student->updated_at->format('Y-m-d H:i')); ?></dd></div>
            </dl>
            <?php if($student->guardians->isNotEmpty()): ?>
                <div class="mt-6 pt-6 border-t border-slate-100">
                    <p class="text-xs font-bold text-slate-600 mb-2">أولياء الأمور</p>
                    <div class="flex flex-wrap gap-2">
                        <?php $__currentLoopData = $student->guardians; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $guardian): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-100 text-sm text-slate-800">
                                <i class="fas fa-user-friends text-slate-400"></i>
                                <?php echo e($guardian->name); ?>

                                <?php if($guardian->pivot->relation): ?><span class="text-xs text-slate-500">(<?php echo e($guardian->pivot->relation); ?>)</span><?php endif; ?>
                            </span>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            <?php endif; ?>
            <?php if($student->academicSupervisors->isNotEmpty()): ?>
                <div class="mt-4">
                    <p class="text-xs font-bold text-slate-600 mb-2">المشرفون الأكاديميون</p>
                    <div class="flex flex-wrap gap-2">
                        <?php $__currentLoopData = $student->academicSupervisors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sup): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <span class="px-3 py-1.5 rounded-xl bg-indigo-50 text-indigo-800 text-sm font-semibold"><?php echo e($sup->name); ?></span>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

    
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between gap-2">
            <h2 class="text-lg font-black text-slate-900">الاشتراكات والباقات</h2>
            <?php if(Route::has('admin.subscriptions.index')): ?>
                <a href="<?php echo e(route('admin.subscriptions.index')); ?>" class="text-xs font-bold text-sky-600 hover:underline">إدارة الاشتراكات</a>
            <?php endif; ?>
        </div>
        <div class="overflow-x-auto">
            <?php if($subscriptions->isNotEmpty()): ?>
                <table class="min-w-full divide-y divide-slate-200 text-right text-sm">
                    <thead class="bg-slate-50 text-xs font-bold text-slate-600">
                        <tr>
                            <th class="px-4 py-3">الخطة</th>
                            <th class="px-4 py-3">الحالة</th>
                            <th class="px-4 py-3">السعر</th>
                            <th class="px-4 py-3">من — إلى</th>
                            <th class="px-4 py-3">ساعات حصص</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php $__currentLoopData = $subscriptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $hours = is_array($sub->feature_limits) ? ($sub->feature_limits['tutor_lesson_hours'] ?? null) : null;
                                $isActive = $sub->id === ($activeSubscription?->id);
                            ?>
                            <tr class="hover:bg-slate-50 <?php echo e($isActive ? 'bg-sky-50/50' : ''); ?>">
                                <td class="px-4 py-3 font-semibold text-slate-900">
                                    <?php echo e($sub->plan_name); ?>

                                    <?php if($isActive): ?><span class="mr-1 text-[10px] font-bold text-sky-700 bg-sky-100 px-1.5 py-0.5 rounded">نشط</span><?php endif; ?>
                                </td>
                                <td class="px-4 py-3 text-xs"><?php echo e($subscriptionStatusLabels[$sub->status] ?? $sub->status); ?></td>
                                <td class="px-4 py-3"><?php echo e(number_format((float) $sub->price, 0)); ?> <?php echo e(__('public.currency')); ?></td>
                                <td class="px-4 py-3 text-xs text-slate-600">
                                    <?php echo e(optional($sub->start_date)->format('Y-m-d') ?? '—'); ?>

                                    → <?php echo e(optional($sub->end_date)->format('Y-m-d') ?? '—'); ?>

                                </td>
                                <td class="px-4 py-3"><?php echo e($hours !== null ? (int) $hours . ' س' : '—'); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="p-8 text-center text-sm text-slate-500">لا توجد اشتراكات مسجّلة.</p>
            <?php endif; ?>
        </div>
    </section>

    
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200">
            <h2 class="text-lg font-black text-slate-900">حصص مع المعلمين</h2>
        </div>
        <?php if($profile): ?>
            <div class="px-5 py-4 bg-violet-50/40 border-b border-violet-100 grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
                <div><p class="text-xs text-slate-500">رصيد الساعات</p><p class="font-black text-slate-900"><?php echo e((int) $profile->lesson_hours_quota); ?> س</p></div>
                <div><p class="text-xs text-slate-500">المستخدم</p><p class="font-black text-slate-900"><?php echo e((int) $profile->lesson_hours_used); ?> س</p></div>
                <div><p class="text-xs text-slate-500">المتبقي</p><p class="font-black text-emerald-700"><?php echo e(max(0, (int) $profile->lesson_hours_quota - (int) $profile->lesson_hours_used)); ?> س</p></div>
                <div><p class="text-xs text-slate-500">نمط المطابقة</p><p class="font-semibold text-slate-800"><?php echo e($matchingLabels[$profile->matching_mode] ?? $profile->matching_mode); ?></p></div>
            </div>
        <?php else: ?>
            <p class="px-5 py-3 text-xs text-amber-800 bg-amber-50 border-b border-amber-100">لم يُنشأ ملف تعلّم بعد (يُنشأ عادةً مع الاشتراك أو أول حصة).</p>
        <?php endif; ?>
        <div class="overflow-x-auto">
            <?php if($lessonBookings->isNotEmpty()): ?>
                <table class="min-w-full divide-y divide-slate-200 text-right text-sm">
                    <thead class="bg-slate-50 text-xs font-bold text-slate-600">
                        <tr>
                            <th class="px-4 py-3">الموعد</th>
                            <th class="px-4 py-3">المعلم</th>
                            <th class="px-4 py-3">الحالة</th>
                            <th class="px-4 py-3">المدة</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php $__currentLoopData = $lessonBookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3 text-xs"><?php echo e(optional($booking->scheduled_at)->format('Y-m-d H:i') ?? '—'); ?></td>
                                <td class="px-4 py-3 font-medium"><?php echo e($booking->instructor->name ?? '—'); ?></td>
                                <td class="px-4 py-3 text-xs"><?php echo e($booking->statusLabel()); ?></td>
                                <td class="px-4 py-3 text-xs"><?php echo e($booking->duration_minutes ?? '—'); ?> د</td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="p-8 text-center text-sm text-slate-500">لا توجد حجوزات حصص.</p>
            <?php endif; ?>
        </div>
    </section>

    
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200">
            <h2 class="text-lg font-black text-slate-900">تسجيلات الكورسات (أونلاين)</h2>
        </div>
        <div class="overflow-x-auto">
            <?php if($enrollments->isNotEmpty()): ?>
                <table class="min-w-full divide-y divide-slate-200 text-right text-sm">
                    <thead class="bg-slate-50 text-xs font-bold text-slate-600">
                        <tr>
                            <th class="px-4 py-3">الكورس</th>
                            <th class="px-4 py-3">المدرب</th>
                            <th class="px-4 py-3">الحالة</th>
                            <th class="px-4 py-3">التقدّم</th>
                            <th class="px-4 py-3">السعر</th>
                            <th class="px-4 py-3">تاريخ التسجيل</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php $__currentLoopData = $enrollments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $enr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3">
                                    <?php if($enr->course && Route::has('admin.advanced-courses.show')): ?>
                                        <a href="<?php echo e(route('admin.advanced-courses.show', $enr->course)); ?>" class="font-semibold text-sky-600 hover:underline"><?php echo e($enr->course->title); ?></a>
                                    <?php else: ?>
                                        <?php echo e($enr->course->title ?? '—'); ?>

                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 text-xs"><?php echo e($enr->course?->instructor?->name ?? '—'); ?></td>
                                <td class="px-4 py-3 text-xs font-semibold"><?php echo e($enr->status); ?></td>
                                <td class="px-4 py-3"><?php echo e($enr->progress !== null ? $enr->progress . '%' : '—'); ?></td>
                                <td class="px-4 py-3 text-xs"><?php echo e($enr->final_price ? number_format($enr->final_price, 0) : '—'); ?></td>
                                <td class="px-4 py-3 text-xs text-slate-500"><?php echo e(optional($enr->enrolled_at ?? $enr->created_at)->format('Y-m-d')); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="p-8 text-center text-sm text-slate-500">لا توجد تسجيلات كورسات.</p>
            <?php endif; ?>
        </div>
    </section>

    <?php if($installmentAgreements->isNotEmpty()): ?>
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200">
            <h2 class="text-lg font-black text-slate-900">اتفاقيات التقسيط</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-right text-sm">
                <thead class="bg-slate-50 text-xs font-bold text-slate-600">
                    <tr>
                        <th class="px-4 py-3">#</th>
                        <th class="px-4 py-3">المبلغ</th>
                        <th class="px-4 py-3">الحالة</th>
                        <th class="px-4 py-3">التاريخ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__currentLoopData = $installmentAgreements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3"><?php echo e($agr->id); ?></td>
                            <td class="px-4 py-3"><?php echo e(number_format((float) ($agr->total_amount ?? 0), 0)); ?></td>
                            <td class="px-4 py-3 text-xs"><?php echo e($agr->status ?? '—'); ?></td>
                            <td class="px-4 py-3 text-xs"><?php echo e($agr->created_at->format('Y-m-d')); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </section>
    <?php endif; ?>

    
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
            <h2 class="text-lg font-black text-slate-900">تذاكر الدعم الفني</h2>
            <?php if(Route::has('admin.support-tickets.index')): ?>
                <a href="<?php echo e(route('admin.support-tickets.index')); ?>" class="text-xs font-bold text-sky-600 hover:underline">كل التذاكر</a>
            <?php endif; ?>
        </div>
        <div class="overflow-x-auto">
            <?php if($supportTickets->isNotEmpty()): ?>
                <table class="min-w-full divide-y divide-slate-200 text-right text-sm">
                    <thead class="bg-slate-50 text-xs font-bold text-slate-600">
                        <tr>
                            <th class="px-4 py-3">الموضوع</th>
                            <th class="px-4 py-3">التصنيف</th>
                            <th class="px-4 py-3">الحالة</th>
                            <th class="px-4 py-3">التاريخ</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php $__currentLoopData = $supportTickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticket): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3 font-medium text-slate-900"><?php echo e($ticket->subject); ?></td>
                                <td class="px-4 py-3 text-xs"><?php echo e($ticket->inquiryCategory->name ?? '—'); ?></td>
                                <td class="px-4 py-3 text-xs"><?php echo e($ticketStatusLabels[$ticket->status] ?? $ticket->status); ?></td>
                                <td class="px-4 py-3 text-xs text-slate-500"><?php echo e($ticket->created_at->format('Y-m-d')); ?></td>
                                <td class="px-4 py-3">
                                    <?php if(Route::has('admin.support-tickets.show')): ?>
                                        <a href="<?php echo e(route('admin.support-tickets.show', $ticket)); ?>" class="text-xs font-bold text-sky-600">فتح</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="p-8 text-center text-sm text-slate-500">لا توجد تذاكر دعم.</p>
            <?php endif; ?>
        </div>
    </section>

    
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200">
            <h2 class="text-lg font-black text-slate-900">محاولات الاختبارات</h2>
        </div>
        <div class="overflow-x-auto">
            <?php if($examAttempts->isNotEmpty()): ?>
                <table class="min-w-full divide-y divide-slate-200 text-right text-sm">
                    <thead class="bg-slate-50 text-xs font-bold text-slate-600">
                        <tr>
                            <th class="px-4 py-3">الاختبار</th>
                            <th class="px-4 py-3">الدرجة</th>
                            <th class="px-4 py-3">النسبة</th>
                            <th class="px-4 py-3">الحالة</th>
                            <th class="px-4 py-3">التاريخ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php $__currentLoopData = $examAttempts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attempt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3 font-medium"><?php echo e($attempt->exam->title ?? '—'); ?></td>
                                <td class="px-4 py-3"><?php echo e($attempt->score ?? '—'); ?></td>
                                <td class="px-4 py-3"><?php echo e($attempt->percentage !== null ? $attempt->percentage . '%' : '—'); ?></td>
                                <td class="px-4 py-3 text-xs"><?php echo e($attempt->status ?? '—'); ?></td>
                                <td class="px-4 py-3 text-xs text-slate-500"><?php echo e(optional($attempt->submitted_at ?? $attempt->created_at)->format('Y-m-d H:i')); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="p-8 text-center text-sm text-slate-500">لا توجد محاولات اختبار.</p>
            <?php endif; ?>
        </div>
    </section>

    
    <?php if($certificates->isNotEmpty()): ?>
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200">
            <h2 class="text-lg font-black text-slate-900">الشهادات</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-right text-sm">
                <thead class="bg-slate-50 text-xs font-bold text-slate-600">
                    <tr>
                        <th class="px-4 py-3">العنوان / الكورس</th>
                        <th class="px-4 py-3">تاريخ الإصدار</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__currentLoopData = $certificates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cert): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3"><?php echo e($cert->title ?? $cert->course_name ?? 'شهادة #' . $cert->id); ?></td>
                            <td class="px-4 py-3 text-xs"><?php echo e(optional($cert->issued_at ?? $cert->created_at)->format('Y-m-d')); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </section>
    <?php endif; ?>

    
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200">
            <h2 class="text-lg font-black text-slate-900">سجل النشاط (آخر 100)</h2>
        </div>
        <div class="overflow-x-auto">
            <?php if($activityLogs->isNotEmpty()): ?>
                <table class="min-w-full divide-y divide-slate-200 text-right text-sm">
                    <thead class="bg-slate-50 text-xs font-bold text-slate-600">
                        <tr>
                            <th class="px-4 py-3">التاريخ</th>
                            <th class="px-4 py-3">الإجراء</th>
                            <th class="px-4 py-3">الوصف</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php $__currentLoopData = $activityLogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-2 text-xs text-slate-500"><?php echo e($log->created_at->format('Y-m-d H:i')); ?></td>
                                <td class="px-4 py-2 text-xs"><?php echo e($log->action ?? '—'); ?></td>
                                <td class="px-4 py-2"><?php echo e(\Illuminate\Support\Str::limit($log->description ?? '—', 80)); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="p-8 text-center text-sm text-slate-500">لا يوجد سجل نشاط.</p>
            <?php endif; ?>
        </div>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sana\resources\views\admin\quality-control\student-show.blade.php ENDPATH**/ ?>