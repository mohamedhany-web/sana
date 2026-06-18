<?php $__env->startSection('title', 'اشتراكي - الباقة والمدة'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('dashboard.partials.sanua-theme', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<?php
    $sub = $subscription;
    $durationLabel = \App\Models\Subscription::getDurationLabel($sub->billing_cycle);
    $currency = __('public.currency');
?>

<div class="sanua-dash">

    <header class="sanua-page-head">
        <div>
            <h1 class="sanua-page-head__title">اشتراكي</h1>
            <p class="sanua-page-head__sub">تفاصيل باقتك الحالية والمزايا المتاحة</p>
        </div>
        <div class="sanua-page-head__actions">
            <a href="<?php echo e(route('public.pricing')); ?>" class="sanua-page-head__btn sanua-page-head__btn--ghost">
                <i class="fas fa-gem"></i>
                الباقات
            </a>
            <?php if(Route::has('student.tutor-lessons.hub')): ?>
                <a href="<?php echo e(route('student.tutor-lessons.hub')); ?>" class="sanua-page-head__btn">
                    <i class="fas fa-calendar-check"></i>
                    حجز حصة
                </a>
            <?php endif; ?>
        </div>
    </header>

    <div class="sanua-stats-row">
        <div class="sanua-stat-pill">
            <span class="sanua-stat-pill__icon sanua-stat-pill__icon--purple" aria-hidden="true">
                <i class="fas fa-gem"></i>
            </span>
            <div class="sanua-stat-pill__body">
                <strong><?php echo e($daysRemaining !== null ? max(0, $daysRemaining) : '—'); ?></strong>
                <span>يوم متبقٍ</span>
            </div>
        </div>
        <div class="sanua-stat-pill">
            <span class="sanua-stat-pill__icon sanua-stat-pill__icon--gold" aria-hidden="true">
                <i class="fas fa-clock"></i>
            </span>
            <div class="sanua-stat-pill__body">
                <strong><?php echo e($tutorHoursRemaining ?? 0); ?></strong>
                <span>ساعات حصص متبقية</span>
            </div>
        </div>
        <div class="sanua-stat-pill">
            <span class="sanua-stat-pill__icon sanua-stat-pill__icon--green" aria-hidden="true">
                <i class="fas fa-check-circle"></i>
            </span>
            <div class="sanua-stat-pill__body">
                <strong>نشط</strong>
                <span>حالة الاشتراك</span>
            </div>
        </div>
        <div class="sanua-stat-pill">
            <span class="sanua-stat-pill__icon sanua-stat-pill__icon--amber" aria-hidden="true">
                <i class="fas fa-tags"></i>
            </span>
            <div class="sanua-stat-pill__body">
                <strong><?php echo e($displayPrice > 0 ? number_format($displayPrice, 0) : '—'); ?></strong>
                <span><?php echo e($currency); ?></span>
            </div>
        </div>
    </div>

    <div class="sanua-subscription-hero">
        <div class="sanua-subscription-hero__top">
            <div class="sanua-live-card__badges" style="margin-bottom:10px;">
                <?php if(!empty($planPackage['card_badge'])): ?>
                    <span class="sanua-badge sanua-badge--pending"><?php echo e($planPackage['card_badge']); ?></span>
                <?php endif; ?>
                <span class="sanua-badge sanua-badge--approved"><span class="sanua-badge__dot"></span> نشط</span>
            </div>
            <h2 class="sanua-subscription-hero__title"><?php echo e($displayPlanName); ?></h2>
            <?php if(!empty($planPackage['card_subtitle'])): ?>
                <p class="sanua-subscription-hero__sub"><?php echo e($planPackage['card_subtitle']); ?></p>
            <?php else: ?>
                <p class="sanua-subscription-hero__sub">مدة الباقة: <?php echo e($durationLabel); ?></p>
            <?php endif; ?>
            <?php if($displayPrice > 0): ?>
                <div class="sanua-subscription-hero__price">
                    <?php echo e(number_format($displayPrice, 0)); ?> <?php echo e($currency); ?>

                    <span style="font-size:0.55em;opacity:0.85;">/ <?php echo e(\App\Models\Subscription::billingCycleLabel($sub->billing_cycle)); ?></span>
                </div>
            <?php endif; ?>
        </div>
        <div class="sanua-subscription-hero__body">
            <div class="sanua-metric-grid">
                <div class="sanua-metric">
                    <span class="sanua-metric__label">تاريخ التفعيل</span>
                    <span class="sanua-metric__value"><?php echo e($sub->start_date?->format('Y-m-d') ?? '—'); ?></span>
                </div>
                <div class="sanua-metric">
                    <span class="sanua-metric__label">مدة الباقة</span>
                    <span class="sanua-metric__value"><?php echo e($durationLabel); ?></span>
                </div>
                <div class="sanua-metric">
                    <span class="sanua-metric__label">ينتهي في</span>
                    <span class="sanua-metric__value"><?php echo e($sub->end_date?->format('Y-m-d') ?? '—'); ?></span>
                </div>
                <div class="sanua-metric">
                    <span class="sanua-metric__label">نوع الاشتراك</span>
                    <span class="sanua-metric__value"><?php echo e(\App\Models\Subscription::typeLabel($sub->subscription_type)); ?></span>
                </div>
            </div>
            <?php if($daysRemaining !== null): ?>
                <p class="text-sm font-bold mt-3 <?php echo e($daysRemaining < 0 ? 'text-rose-600' : ($daysRemaining <= 7 ? 'text-amber-600' : 'text-slate-500')); ?>">
                    <?php if($daysRemaining < 0): ?>
                        انتهى منذ <?php echo e(abs($daysRemaining)); ?> يوم
                    <?php elseif($daysRemaining === 0): ?>
                        ينتهي اليوم
                    <?php else: ?>
                        متبقٍ <?php echo e($daysRemaining); ?> يوم على انتهاء الاشتراك
                    <?php endif; ?>
                </p>
            <?php endif; ?>
        </div>
    </div>

    <?php if($isStudentPlan || $tutorHoursQuota > 0 || $hasSupport): ?>
        <section class="sanua-section">
            <div class="sanua-panel">
                <div class="sanua-panel__head"><h3>محتوى باقتك</h3></div>
                <div class="sanua-panel__body space-y-4">

                    <?php if($tutorHoursQuota > 0 || $tutorHoursFromLimits !== null): ?>
                        <div class="rounded-xl border border-violet-200 bg-violet-50/40 p-4 space-y-3">
                            <p class="text-sm font-bold text-slate-800 flex items-center gap-2 m-0">
                                <i class="fas fa-chalkboard-user text-violet-500"></i>
                                حصص مع المعلمين
                            </p>
                            <div class="sanua-metric-grid" style="grid-template-columns:repeat(3,minmax(0,1fr));">
                                <div class="sanua-metric"><span class="sanua-metric__label">الرصيد</span><span class="sanua-metric__value"><?php echo e($tutorHoursQuota); ?> س</span></div>
                                <div class="sanua-metric"><span class="sanua-metric__label">مستهلكة</span><span class="sanua-metric__value"><?php echo e($tutorHoursUsed); ?> س</span></div>
                                <div class="sanua-metric"><span class="sanua-metric__label">متبقية</span><span class="sanua-metric__value"><?php echo e($tutorHoursRemaining); ?> س</span></div>
                            </div>
                            <?php if($tutorHoursQuota > 0): ?>
                                <div>
                                    <div class="flex justify-between text-[10px] text-slate-500 mb-1">
                                        <span>نسبة الاستهلاك</span><span><?php echo e($tutorHoursPercent); ?>%</span>
                                    </div>
                                    <div class="sanua-course-card__bar">
                                        <div class="sanua-course-card__bar-fill" style="width:<?php echo e($tutorHoursPercent); ?>%;"></div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <?php if($hasSupport): ?>
                        <div class="flex items-center gap-3 rounded-xl border border-sky-200 bg-sky-50/50 px-4 py-3">
                            <span class="w-10 h-10 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center shrink-0">
                                <i class="fas fa-headset"></i>
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-bold text-slate-800 m-0">الدعم الفني</p>
                                <p class="text-xs text-slate-600 m-0">مشمول في باقتك — يمكنك فتح تذكرة دعم.</p>
                            </div>
                            <?php if(Route::has('student.support.index')): ?>
                                <a href="<?php echo e(route('student.support.index')); ?>" class="sanua-btn sanua-btn--purple" style="padding:8px 14px;font-size:0.75rem;">فتح الدعم</a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <?php if(count($displayFeatures) > 0): ?>
        <section class="sanua-section">
            <div class="sanua-panel">
                <div class="sanua-panel__head"><h3>المزايا المتاحة في باقتك</h3></div>
                <div class="sanua-panel__body">
                    <ul class="sanua-feature-list">
                        <?php $__currentLoopData = $displayFeatures; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $featureKey): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><i class="fas fa-check-circle"></i> <?php echo e(__("student.subscription_feature.{$featureKey}")); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <?php if($studentUpgradeOptions->count() > 0): ?>
        <section class="sanua-section">
            <div class="sanua-panel">
                <div class="sanua-panel__head"><h3>ترقية الباقة</h3></div>
                <div class="sanua-panel__body">
                    <p class="text-sm text-slate-600 mb-4">يمكنك الترقية إلى باقة أعلى للحصول على ساعات حصص أكثر.</p>
                    <div class="sanua-courses-grid">
                        <?php $__currentLoopData = $studentUpgradeOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="sanua-recording-card" style="cursor:default;">
                                <span class="sanua-recording-card__icon"><i class="fas fa-arrow-up"></i></span>
                                <h3 class="sanua-recording-card__title"><?php echo e($plan['label'] ?? $plan['key']); ?></h3>
                                <?php if(!empty($plan['card_subtitle'])): ?>
                                    <p class="sanua-recording-card__sub"><?php echo e($plan['card_subtitle']); ?></p>
                                <?php endif; ?>
                                <div class="sanua-recording-card__meta">
                                    <span><?php echo e((int) ($plan['limits']['tutor_lesson_hours'] ?? 0)); ?> ساعة حصص</span>
                                    <?php if(($plan['price'] ?? 0) > 0): ?>
                                        <span><?php echo e(number_format((float) $plan['price'], 0)); ?> <?php echo e($currency); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <?php if(Route::has('student.support.index')): ?>
                        <a href="<?php echo e(route('student.support.index')); ?>" class="sanua-btn sanua-btn--purple mt-4">
                            <i class="fas fa-arrow-up"></i> طلب ترقية عبر الدعم
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <?php if($teacherUpgradeOptions->count() > 0): ?>
        <section class="sanua-section">
            <div class="sanua-panel">
                <div class="sanua-panel__head"><h3>ترقية الباقة</h3></div>
                <div class="sanua-panel__body">
                    <div class="sanua-session-list">
                        <?php $__currentLoopData = $teacherUpgradeOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $planKey): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <a href="<?php echo e(route('public.subscription.checkout', $planKey) . '?upgrade=1&from=' . $sub->id); ?>" class="sanua-session-card">
                                <div class="sanua-session-card__row">
                                    <div>
                                        <h3 class="sanua-session-card__title">
                                            <?php echo e($planKey === 'teacher_pro' ? 'الباقة الشاملة' : 'الباقة الأساسية'); ?>

                                        </h3>
                                        <p class="sanua-session-card__desc" style="margin:0;">ترقية إلى مستوى أعلى</p>
                                    </div>
                                    <span class="sanua-session-card__action"><i class="fas fa-arrow-up"></i></span>
                                </div>
                            </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <p class="text-sm text-slate-500">
        عند انتهاء المدة سيُغلق الاشتراك تلقائياً.
        <a href="<?php echo e(route('public.pricing')); ?>" class="text-violet-600 font-bold hover:underline">عرض الباقات والتجديد</a>
    </p>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sana\resources\views\student\my-subscription.blade.php ENDPATH**/ ?>