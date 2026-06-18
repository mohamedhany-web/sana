<?php $__env->startSection('title', __('student.profile_title')); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('dashboard.partials.sanua-theme', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<?php
    $roleLabels = [
        'student' => __('student.student_role'),
        'parent' => 'ولي أمر',
        'teacher' => __('student.teacher_role'),
        'instructor' => __('student.instructor_role'),
        'admin' => __('student.admin_role_label'),
        'super_admin' => __('student.super_admin_role'),
    ];
    $roleLabel = $roleLabels[$user->role] ?? __('student.user_role');

    $memberSince = ($user->created_at instanceof \Carbon\CarbonInterface)
        ? $user->created_at->copy()->locale('ar')->translatedFormat('d F Y')
        : null;

    $coursesCount = method_exists($user, 'courseEnrollments') ? $user->courseEnrollments()->count() : 0;
    $notificationsCount = method_exists($user, 'customNotifications') ? $user->customNotifications()->count() : 0;

    $lastLogin = ($user->last_login_at instanceof \Carbon\CarbonInterface)
        ? $user->last_login_at->copy()->locale('ar')->diffForHumans()
        : null;
?>

<div class="sanua-dash">

    <header class="sanua-page-head">
        <div>
            <h1 class="sanua-page-head__title"><?php echo e(__('student.profile_title')); ?></h1>
            <p class="sanua-page-head__sub"><?php echo e(__('student.profile_subtitle')); ?></p>
        </div>
        <div class="sanua-page-head__actions">
            <a href="<?php echo e(route('dashboard')); ?>" class="sanua-page-head__btn sanua-page-head__btn--ghost">
                <i class="fas fa-arrow-right"></i>
                لوحة التحكم
            </a>
        </div>
    </header>

    <?php if(session('success')): ?>
        <div class="sanua-alert sanua-alert--info">
            <i class="fas fa-check-circle ml-2"></i>
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <div class="sanua-profile-hero">
        <div class="sanua-profile-avatar">
            <?php if($user->profile_image): ?>
                <img src="<?php echo e($user->profile_image_url); ?>" alt="<?php echo e(__('student.profile_image_alt')); ?>">
            <?php else: ?>
                <?php echo e(mb_substr($user->name, 0, 1)); ?>

            <?php endif; ?>
        </div>
        <div class="sanua-profile-hero__main">
            <span class="sanua-badge sanua-badge--submitted" style="background:rgba(255,255,255,0.2);color:#fff;border:none;">
                <i class="fas fa-user-graduate"></i>
                <?php echo e($roleLabel); ?>

            </span>
            <h2 class="sanua-profile-hero__name"><?php echo e($user->name); ?></h2>
            <p class="sanua-profile-hero__sub"><?php echo e(__('student.profile_subtitle')); ?></p>
            <div class="sanua-profile-hero__chips">
                <span class="sanua-profile-chip"><i class="fas fa-phone"></i> <?php echo e($user->phone ?? '—'); ?></span>
                <?php if($user->email): ?>
                    <span class="sanua-profile-chip"><i class="fas fa-envelope"></i> <?php echo e($user->email); ?></span>
                <?php endif; ?>
                <span class="sanua-profile-chip"><i class="fas fa-id-badge"></i> #<?php echo e(str_pad($user->id, 5, '0', STR_PAD_LEFT)); ?></span>
            </div>
        </div>
    </div>

    <div class="sanua-stats-row">
        <div class="sanua-stat-pill">
            <span class="sanua-stat-pill__icon sanua-stat-pill__icon--purple" aria-hidden="true">
                <i class="fas fa-calendar-week"></i>
            </span>
            <div class="sanua-stat-pill__body">
                <strong><?php echo e($memberSince ?: '—'); ?></strong>
                <span><?php echo e(__('student.join_date_label')); ?></span>
            </div>
        </div>
        <div class="sanua-stat-pill">
            <span class="sanua-stat-pill__icon sanua-stat-pill__icon--gold" aria-hidden="true">
                <i class="fas fa-layer-group"></i>
            </span>
            <div class="sanua-stat-pill__body">
                <strong><?php echo e($coursesCount); ?></strong>
                <span><?php echo e(__('student.active_courses_count')); ?></span>
            </div>
        </div>
        <div class="sanua-stat-pill">
            <span class="sanua-stat-pill__icon sanua-stat-pill__icon--green" aria-hidden="true">
                <i class="fas fa-bell"></i>
            </span>
            <div class="sanua-stat-pill__body">
                <strong><?php echo e($notificationsCount); ?></strong>
                <span><?php echo e(__('student.notifications')); ?></span>
            </div>
        </div>
        <div class="sanua-stat-pill">
            <span class="sanua-stat-pill__icon sanua-stat-pill__icon--amber" aria-hidden="true">
                <i class="fas fa-clock-rotate-left"></i>
            </span>
            <div class="sanua-stat-pill__body">
                <strong><?php echo e($lastLogin ?: '—'); ?></strong>
                <span><?php echo e(__('student.last_login_label')); ?></span>
            </div>
        </div>
    </div>

    <div class="sanua-profile-layout">
        <aside class="space-y-4">
            <div class="sanua-panel">
                <div class="sanua-panel__head"><h3>معلومات الاتصال</h3></div>
                <div class="sanua-panel__body">
                    <div class="sanua-profile-info-row">
                        <span class="sanua-profile-info-row__label"><i class="fas fa-id-badge"></i> رقم العضوية</span>
                        <span class="sanua-profile-info-row__value">#<?php echo e(str_pad($user->id, 5, '0', STR_PAD_LEFT)); ?></span>
                    </div>
                    <div class="sanua-profile-info-row">
                        <span class="sanua-profile-info-row__label"><i class="fas fa-user-shield"></i> نوع الحساب</span>
                        <span class="sanua-profile-info-row__value">
                            <span class="sanua-badge sanua-badge--submitted"><?php echo e($roleLabel); ?></span>
                        </span>
                    </div>
                    <div class="sanua-profile-info-row">
                        <span class="sanua-profile-info-row__label"><i class="fas fa-signal"></i> الحالة</span>
                        <span class="sanua-profile-info-row__value">
                            <span class="sanua-badge <?php echo e($user->is_active ? 'sanua-badge--approved' : 'sanua-badge--rejected'); ?>">
                                <?php echo e($user->is_active ? 'نشط' : 'غير نشط'); ?>

                            </span>
                        </span>
                    </div>
                    <div class="sanua-alert sanua-alert--info" style="margin:12px 0 0;">
                        <i class="fas fa-shield-halved ml-1"></i>
                        يمكنك تحسين أمان حسابك بتفعيل التحقق بخطوتين (قريباً).
                    </div>
                </div>
            </div>

            <div class="sanua-panel">
                <div class="sanua-panel__head"><h3>نصائح سريعة</h3></div>
                <div class="sanua-panel__body">
                    <ul class="sanua-tip-list">
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <div>
                                <strong>حدّث معلومات التواصل</strong>
                                احرص على أن يكون بريدك ورقم هاتفك محدثين لاستقبال الإشعارات.
                            </div>
                        </li>
                        <li>
                            <i class="fas fa-lock"></i>
                            <div>
                                <strong>أنشئ كلمة مرور قوية</strong>
                                استخدم مزيجاً من الأحرف والأرقام وغيّرها بشكل دوري.
                            </div>
                        </li>
                        <li>
                            <i class="fas fa-bell"></i>
                            <div>
                                <strong>تابع الإشعارات</strong>
                                ابقَ على اطلاع بالكورسات والتنبيهات المهمة.
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </aside>

        <div class="space-y-4">
            <div class="sanua-panel">
                <div class="sanua-panel__head">
                    <h3>تحديث البيانات الأساسية</h3>
                </div>
                <div class="sanua-panel__body">
                    <form method="POST" action="<?php echo e(route('profile.update')); ?>" enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>

                        <div class="sanua-form-grid">
                            <div class="sanua-field">
                                <label for="name">الاسم الكامل</label>
                                <input type="text" name="name" id="name" value="<?php echo e(old('name', $user->name)); ?>" required>
                                <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="sanua-field__error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="sanua-field">
                                <label for="phone">رقم الهاتف</label>
                                <input type="text" name="phone" id="phone" value="<?php echo e(old('phone', $user->phone)); ?>" required>
                                <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="sanua-field__error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="sanua-field sanua-field--full">
                                <label for="email">البريد الإلكتروني (اختياري)</label>
                                <input type="email" name="email" id="email" value="<?php echo e(old('email', $user->email)); ?>">
                                <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="sanua-field__error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>

                        <div class="sanua-field sanua-field--full" style="margin-top:16px;">
                            <label>صورة الملف الشخصي</label>
                            <div class="sanua-upload-box">
                                <div class="sanua-upload-preview">
                                    <?php if($user->profile_image): ?>
                                        <img src="<?php echo e($user->profile_image_url); ?>" alt="<?php echo e(__('student.profile_image_alt')); ?>">
                                    <?php else: ?>
                                        <i class="fas fa-camera"></i>
                                    <?php endif; ?>
                                </div>
                                <div style="flex:1;min-width:180px;">
                                    <input type="file" name="profile_image" accept="image/*">
                                    <p style="margin:6px 0 0;font-size:0.72rem;font-weight:600;color:#94a3b8;">PNG أو JPG — الحد الأقصى حسب إعدادات المنصة.</p>
                                    <?php $__errorArgs = ['profile_image'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="sanua-field__error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                        </div>

                        <div class="sanua-password-box" style="margin-top:16px;">
                            <h4 style="margin:0 0 4px;font-size:0.88rem;font-weight:900;color:#1e1b4b;">تغيير كلمة المرور</h4>
                            <p style="margin:0 0 12px;font-size:0.72rem;font-weight:600;color:#94a3b8;">اترك الحقول فارغة إذا لم ترغب في التغيير.</p>
                            <div class="sanua-form-grid">
                                <div class="sanua-field">
                                    <label for="current_password">كلمة المرور الحالية</label>
                                    <input type="password" name="current_password" id="current_password">
                                    <?php $__errorArgs = ['current_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="sanua-field__error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                                <div class="sanua-field">
                                    <label for="password">كلمة المرور الجديدة</label>
                                    <input type="password" name="password" id="password">
                                    <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="sanua-field__error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                                <div class="sanua-field">
                                    <label for="password_confirmation">تأكيد كلمة المرور</label>
                                    <input type="password" name="password_confirmation" id="password_confirmation">
                                </div>
                            </div>
                        </div>

                        <div class="sanua-form-actions">
                            <p style="margin:0;font-size:0.72rem;font-weight:600;color:#94a3b8;">
                                <i class="fas fa-info-circle" style="color:#8B5CF6;"></i>
                                سيتم إرسال إشعار عند تغيير كلمة المرور.
                            </p>
                            <button type="submit" class="sanua-btn sanua-btn--purple">
                                <i class="fas fa-save"></i>
                                حفظ التعديلات
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="sanua-panel">
                <div class="sanua-panel__head"><h3>نشاط الحساب الأخير</h3></div>
                <div class="sanua-panel__body">
                    <div class="sanua-activity-row">
                        <div class="sanua-activity-row__main">
                            <span class="sanua-activity-row__icon"><i class="fas fa-desktop"></i></span>
                            <div>
                                <p class="sanua-activity-row__title">آخر نشاط للنظام</p>
                                <p class="sanua-activity-row__sub">تم تسجيل الدخول بنجاح</p>
                            </div>
                        </div>
                        <span style="font-size:0.72rem;font-weight:800;color:#64748b;"><?php echo e($lastLogin ?: 'قبل قليل'); ?></span>
                    </div>
                    <div class="sanua-activity-row">
                        <div class="sanua-activity-row__main">
                            <span class="sanua-activity-row__icon" style="background:linear-gradient(135deg,#059669,#10B981);"><i class="fas fa-shield-heart"></i></span>
                            <div>
                                <p class="sanua-activity-row__title">أمان الحساب</p>
                                <p class="sanua-activity-row__sub">ننصح بتحديث كلمة المرور كل 90 يوماً.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sana\resources\views\student\profile\index.blade.php ENDPATH**/ ?>