<?php $__env->startSection('title', __('student.referrals_title') . ' - ' . config('app.name', 'Sana')); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('dashboard.partials.sanua-theme', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="sanua-dash">

    <header class="sanua-page-head">
        <div>
            <h1 class="sanua-page-head__title"><?php echo e(__('student.referrals_title')); ?></h1>
            <p class="sanua-page-head__sub"><?php echo e(__('student.referrals_subtitle')); ?></p>
        </div>
    </header>

    <div class="sanua-stats-row">
        <div class="sanua-stat-pill">
            <span class="sanua-stat-pill__icon sanua-stat-pill__icon--purple" aria-hidden="true">
                <i class="fas fa-user-friends"></i>
            </span>
            <div class="sanua-stat-pill__body">
                <strong><?php echo e(number_format($stats['total_referrals'])); ?></strong>
                <span><?php echo e(__('student.total_referrals')); ?></span>
            </div>
        </div>
        <div class="sanua-stat-pill">
            <span class="sanua-stat-pill__icon sanua-stat-pill__icon--green" aria-hidden="true">
                <i class="fas fa-check-circle"></i>
            </span>
            <div class="sanua-stat-pill__body">
                <strong><?php echo e(number_format($stats['completed_referrals'])); ?></strong>
                <span><?php echo e(__('student.completed_referrals')); ?></span>
            </div>
        </div>
        <div class="sanua-stat-pill">
            <span class="sanua-stat-pill__icon sanua-stat-pill__icon--amber" aria-hidden="true">
                <i class="fas fa-hourglass-half"></i>
            </span>
            <div class="sanua-stat-pill__body">
                <strong><?php echo e(number_format($stats['pending_referrals'])); ?></strong>
                <span><?php echo e(__('student.pending_referrals')); ?></span>
            </div>
        </div>
        <div class="sanua-stat-pill">
            <span class="sanua-stat-pill__icon sanua-stat-pill__icon--gold" aria-hidden="true">
                <i class="fas fa-gift"></i>
            </span>
            <div class="sanua-stat-pill__body">
                <strong><?php echo e(number_format($stats['total_rewards'], 0)); ?></strong>
                <span>إجمالي المكافآت</span>
            </div>
        </div>
    </div>

    <?php if($activeProgram): ?>
        <div class="sanua-alert sanua-alert--info">
            <i class="fas fa-gift ml-2"></i>
            <strong>برنامج: <?php echo e($activeProgram->name); ?></strong>
            — خصم للصديق:
            <?php if($activeProgram->discount_type === 'percentage'): ?>
                <?php echo e(rtrim(rtrim(number_format($activeProgram->discount_value, 2), '0'), '.')); ?>%
            <?php else: ?>
                <?php echo e(number_format($activeProgram->discount_value, 2)); ?> <?php echo e(__('public.currency')); ?>

            <?php endif; ?>
            · مكافأتك عند اكتمال الطلب حسب قواعد البرنامج
        </div>
    <?php else: ?>
        <div class="sanua-alert sanua-alert--warning">
            <i class="fas fa-exclamation-triangle ml-2"></i>
            لا يوجد برنامج إحالات نشط حالياً. يمكنك نسخ رابطك، لكن لن تُسجَّل إحالات أو مكافآت حتى يفعّل المشرف برنامجاً.
        </div>
    <?php endif; ?>

    <div class="sanua-referral-banner">
        <h2 class="sanua-page-head__title" style="font-size:1.15rem;margin:0;">كود الإحالة الخاص بك</h2>
        <p class="sanua-page-head__sub" style="margin-top:6px;">شارك الكود أو الرابط واحصل على مكافآت</p>
        <div class="sanua-referral-banner__code"><?php echo e($referralCode); ?></div>
        <div class="sanua-referral-field">
            <button type="button" onclick="copyReferralCode('<?php echo e($referralCode); ?>')" class="sanua-referral-btn">
                <i class="fas fa-copy"></i> نسخ الكود
            </button>
        </div>
        <p class="sanua-page-head__sub" style="margin-top:16px;margin-bottom:6px;">رابط الإحالة</p>
        <div class="sanua-referral-field">
            <input type="text" id="referralLink" value="<?php echo e($referralLink); ?>" readonly>
            <button type="button" onclick="copyReferralLink()" class="sanua-referral-btn sanua-referral-btn--ghost">
                <i class="fas fa-copy"></i> نسخ
            </button>
            <a href="<?php echo e(\App\Support\PublicContactInfo::whatsappShareUrl('سجّل في المنصة عبر رابطي واحصل على خصم: ' . $referralLink)); ?>"
               target="_blank" rel="noopener" class="sanua-referral-btn sanua-referral-btn--wa">
                <i class="fab fa-whatsapp"></i> واتساب
            </a>
        </div>
    </div>

    <section class="sanua-section">
        <div class="sanua-panel">
            <div class="sanua-panel__head">
                <h3>كيف يعمل برنامج الإحالات؟</h3>
            </div>
            <div class="sanua-panel__body">
                <div class="sanua-steps-grid">
                    <div class="sanua-step-card">
                        <h4>1) شارك كود الإحالة</h4>
                        <p>انسخ كود الإحالة أو الرابط وشاركه مع أصدقائك.</p>
                    </div>
                    <div class="sanua-step-card">
                        <h4>2) صديقك يسجّل</h4>
                        <p>يفتح <code>/register?ref=كودك</code> ويكمل التسجيل.</p>
                    </div>
                    <div class="sanua-step-card">
                        <h4>3) اكتمال الإحالة</h4>
                        <p>عند اعتماد أول طلب شراء للمدعو تُسجَّل الإحالة وتظهر مكافأتك.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="sanua-section">
        <div class="sanua-panel">
            <div class="sanua-panel__head">
                <h3><i class="fas fa-list ml-1"></i> قائمة الإحالات</h3>
            </div>

            <?php if($referrals->count() > 0): ?>
                <div class="sanua-table-wrap">
                    <table class="sanua-table">
                        <thead>
                            <tr>
                                <th>المستخدم المحال</th>
                                <th>التاريخ</th>
                                <th>الحالة</th>
                                <th>الخصم</th>
                                <th>المكافأة</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $referrals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $referral): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td>
                                        <strong><?php echo e($referral->referred->name ?? 'غير معروف'); ?></strong>
                                        <div class="text-xs text-slate-400"><?php echo e($referral->referred->phone ?? '—'); ?></div>
                                    </td>
                                    <td><?php echo e($referral->created_at->format('d/m/Y')); ?></td>
                                    <td>
                                        <span class="sanua-badge <?php echo e($referral->status == 'completed' ? 'sanua-badge--approved' : ($referral->status == 'pending' ? 'sanua-badge--pending' : 'sanua-badge--rejected')); ?>">
                                            <?php if($referral->status == 'completed'): ?> مكتملة
                                            <?php elseif($referral->status == 'pending'): ?> قيد الانتظار
                                            <?php else: ?> ملغاة
                                            <?php endif; ?>
                                        </span>
                                    </td>
                                    <td><?php echo e(number_format($referral->discount_amount ?? 0, 2)); ?> <?php echo e(__('public.currency')); ?></td>
                                    <td style="color:#059669;font-weight:900;"><?php echo e(number_format($referral->reward_amount ?? 0, 2)); ?> <?php echo e(__('public.currency')); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
                <?php if($referrals->hasPages()): ?>
                    <div class="sanua-pagination"><?php echo e($referrals->links()); ?></div>
                <?php endif; ?>
            <?php else: ?>
                <div class="sanua-empty" style="box-shadow:none;border:none;">
                    <div class="sanua-empty__icon"><i class="fas fa-user-friends"></i></div>
                    <h3>لا توجد إحالات حتى الآن</h3>
                    <p>ابدأ بمشاركة كود الإحالة مع أصدقائك واحصل على مكافآت.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>
</div>

<div id="referral-toast" class="fixed top-4 left-4 rtl:left-auto rtl:right-4 z-[100] hidden px-4 py-3 rounded-xl bg-emerald-600 text-white text-sm font-semibold shadow-lg max-w-sm" role="status"></div>

<script>
function showReferralToast(msg) {
    var el = document.getElementById('referral-toast');
    if (!el) { alert(msg); return; }
    el.textContent = msg;
    el.classList.remove('hidden');
    clearTimeout(window._refToastT);
    window._refToastT = setTimeout(function() { el.classList.add('hidden'); }, 3200);
}
function copyReferralCode(code) {
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(code).then(function() {
            showReferralToast('تم نسخ كود الإحالة');
        }).catch(function() { showReferralToast('انسخ الكود يدوياً: ' + code); });
    } else {
        showReferralToast('انسخ الكود يدوياً: ' + code);
    }
}
function copyReferralLink() {
    var link = document.getElementById('referralLink').value;
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(link).then(function() {
            showReferralToast('تم نسخ رابط الإحالة');
        }).catch(function() { showReferralToast('انسخ الرابط يدوياً من الحقل'); });
    } else {
        showReferralToast('انسخ الرابط يدوياً من الحقل');
    }
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sana\resources\views\student\referrals\index.blade.php ENDPATH**/ ?>