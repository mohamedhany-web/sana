<?php $__env->startSection('title', 'تذكرة دعم — ' . $ticket->subject); ?>
<?php $__env->startSection('header', 'تذكرة دعم طالب'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $student = $ticket->user;
    $awaiting = $ticket->isAwaitingAdminResponse();
?>

<div class="space-y-6">
    <?php if(session('success')): ?>
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm font-medium flex items-center gap-2">
            <i class="fas fa-check-circle"></i>
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <div class="flex flex-wrap items-center gap-3">
        <a href="<?php echo e(route('admin.support-tickets.index', ['view' => 'needs_reply'])); ?>"
           class="inline-flex items-center gap-2 text-sm font-semibold text-sky-700 hover:text-sky-900">
            <i class="fas fa-arrow-right"></i>
            العودة لقائمة الدعم
        </a>
        <?php if($awaiting): ?>
            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800 border border-rose-200">
                <i class="fas fa-bell"></i> الطالب بانتظار ردّكم
            </span>
        <?php endif; ?>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        
        <aside class="space-y-4">
            <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
                <div class="px-5 py-4 bg-gradient-to-l from-violet-50 to-white border-b border-slate-200">
                    <h3 class="text-sm font-black text-slate-900 flex items-center gap-2">
                        <i class="fas fa-user-graduate text-violet-600"></i>
                        بيانات الطالب
                    </h3>
                </div>
                <div class="p-5 space-y-3 text-sm">
                    <div>
                        <p class="text-xs text-slate-500">الاسم</p>
                        <p class="font-bold text-slate-900"><?php echo e($student->name ?? '—'); ?></p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500">الجوال</p>
                        <p class="font-semibold text-slate-800" dir="ltr"><?php echo e($student->phone ?? '—'); ?></p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500">البريد</p>
                        <p class="font-semibold text-slate-800 text-xs break-all"><?php echo e($student->email ?? '—'); ?></p>
                    </div>
                    <?php if(Route::has('admin.users.edit') && $student?->id): ?>
                        <a href="<?php echo e(route('admin.users.edit', $student->id)); ?>"
                           class="inline-flex w-full justify-center items-center gap-2 mt-2 px-4 py-2.5 rounded-xl border border-violet-200 text-violet-800 text-xs font-bold hover:bg-violet-50">
                            <i class="fas fa-user-cog"></i>
                            فتح حساب الطالب
                        </a>
                    <?php endif; ?>
                </div>
            </section>

            <section class="rounded-2xl bg-white border border-slate-200 shadow-lg p-5 space-y-4">
                <h3 class="text-sm font-black text-slate-900">إدارة التذكرة</h3>
                <form method="POST" action="<?php echo e(route('admin.support-tickets.status', $ticket)); ?>" class="space-y-3">
                    <?php echo csrf_field(); ?>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">الحالة</label>
                        <select name="status" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm">
                            <?php $__currentLoopData = \App\Models\SupportTicket::statusLabels(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $lbl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($val); ?>" <?php if($ticket->status === $val): echo 'selected'; endif; ?>><?php echo e($lbl); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">الأولوية</label>
                        <select name="priority" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm">
                            <?php $__currentLoopData = \App\Models\SupportTicket::priorityLabels(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $lbl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($val); ?>" <?php if($ticket->priority === $val): echo 'selected'; endif; ?>><?php echo e($lbl); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <button type="submit" class="w-full px-4 py-2.5 rounded-xl bg-slate-800 text-white text-sm font-bold hover:bg-slate-900">
                        حفظ التحديث
                    </button>
                </form>
                <dl class="text-xs text-slate-500 space-y-1 pt-2 border-t border-slate-100">
                    <div class="flex justify-between"><dt>رقم التذكرة</dt><dd class="font-mono font-bold text-slate-700">#<?php echo e($ticket->id); ?></dd></div>
                    <div class="flex justify-between"><dt>تاريخ الفتح</dt><dd><?php echo e($ticket->created_at->format('Y-m-d H:i')); ?></dd></div>
                    <?php if($ticket->resolved_at): ?>
                        <div class="flex justify-between"><dt>تاريخ الحل</dt><dd><?php echo e($ticket->resolved_at->format('Y-m-d H:i')); ?></dd></div>
                    <?php endif; ?>
                    <?php if($ticket->assignedAdmin): ?>
                        <div class="flex justify-between"><dt>المسؤول</dt><dd><?php echo e($ticket->assignedAdmin->name); ?></dd></div>
                    <?php endif; ?>
                </dl>
            </section>
        </aside>

        
        <div class="xl:col-span-2 space-y-4">
            <section class="rounded-2xl bg-white border border-slate-200 shadow-lg p-6">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold text-indigo-700"><?php echo e($ticket->inquiryCategory->name ?? 'استفسار عام'); ?></p>
                        <h1 class="text-xl font-black text-slate-900 mt-1"><?php echo e($ticket->subject); ?></h1>
                        <div class="flex flex-wrap gap-2 mt-2">
                            <?php echo $__env->make('admin.support-tickets._badges', ['status' => $ticket->status, 'priority' => $ticket->priority], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        </div>
                    </div>
                </div>
            </section>

            <section class="rounded-2xl bg-white border border-slate-200 shadow-lg p-5 space-y-4 max-h-[32rem] overflow-y-auto">
                <?php $__currentLoopData = $ticket->replies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reply): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="rounded-xl p-4 <?php echo e($reply->sender_type === 'admin' ? 'bg-emerald-50 border border-emerald-200 ml-0 mr-8' : 'bg-slate-50 border border-slate-200 mr-0 ml-8'); ?>">
                        <div class="flex items-center justify-between gap-2 mb-2">
                            <p class="text-xs font-bold <?php echo e($reply->sender_type === 'admin' ? 'text-emerald-800' : 'text-slate-800'); ?>">
                                <?php if($reply->sender_type === 'admin'): ?>
                                    <i class="fas fa-headset ml-1"></i> فريق الدعم
                                    <?php if($reply->user): ?>
                                        <span class="font-normal text-emerald-700">(<?php echo e($reply->user->name); ?>)</span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <i class="fas fa-user-graduate ml-1"></i> <?php echo e($reply->user->name ?? 'الطالب'); ?>

                                <?php endif; ?>
                            </p>
                            <p class="text-[11px] text-slate-500 shrink-0"><?php echo e($reply->created_at->format('Y-m-d H:i')); ?></p>
                        </div>
                        <p class="text-sm text-slate-800 whitespace-pre-line leading-relaxed"><?php echo e($reply->message); ?></p>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </section>

            <?php if(!in_array($ticket->status, ['closed'], true)): ?>
                <form method="POST" action="<?php echo e(route('admin.support-tickets.reply', $ticket)); ?>" class="rounded-2xl bg-white border border-slate-200 shadow-lg p-5 space-y-4" data-turbo="false">
                    <?php echo csrf_field(); ?>
                    <div>
                        <label class="block text-sm font-bold text-slate-900">رد للطالب</label>
                        <p class="text-xs text-slate-500 mt-0.5">سيصل الطالب إشعاراً داخل المنصة عند الإرسال.</p>
                    </div>
                    <textarea name="message" rows="5" required placeholder="اكتب ردّاً واضحاً يشرح الخطوات أو الحل…"
                              class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500"><?php echo e(old('message')); ?></textarea>
                    <?php $__errorArgs = ['message'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <label class="inline-flex items-center gap-2 text-sm text-slate-700 cursor-pointer">
                            <input type="checkbox" name="mark_resolved" value="1" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                            <span>إغلاق التذكرة بعد الرد (تم الحل)</span>
                        </label>
                        <button type="submit" class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-sky-600 text-white text-sm font-bold hover:bg-sky-700 shadow-sm">
                            <i class="fas fa-paper-plane"></i>
                            إرسال الرد للطالب
                        </button>
                    </div>
                </form>
            <?php else: ?>
                <div class="rounded-xl bg-slate-50 border border-slate-200 px-4 py-3 text-sm text-slate-600">
                    هذه التذكرة مغلقة. غيّر الحالة من اللوحة الجانبية إن احتجت إعادة فتحها.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sana\resources\views\admin\support-tickets\show.blade.php ENDPATH**/ ?>