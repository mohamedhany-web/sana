<?php $__env->startSection('title', __('student.settings_title')); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('dashboard.partials.sanua-theme', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="sanua-dash">

    <header class="sanua-page-head">
        <div>
            <h1 class="sanua-page-head__title"><?php echo e(__('student.settings_title')); ?></h1>
            <p class="sanua-page-head__sub"><?php echo e(__('student.settings_subtitle')); ?></p>
        </div>
        <div class="sanua-page-head__actions">
            <a href="<?php echo e(route('profile')); ?>" class="sanua-page-head__btn sanua-page-head__btn--ghost">
                <i class="fas fa-user"></i>
                الملف الشخصي
            </a>
        </div>
    </header>

    <?php if(session('success')): ?>
        <div class="sanua-alert sanua-alert--info">
            <i class="fas fa-check-circle ml-2"></i>
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <form method="POST" action="<?php echo e(route('settings.update')); ?>">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

        <section class="sanua-section">
            <div class="sanua-panel">
                <div class="sanua-panel__head">
                    <h3><i class="fas fa-bell ml-1"></i> <?php echo e(__('student.notification_settings')); ?></h3>
                </div>
                <div class="sanua-panel__body">
                    <div class="sanua-settings-list">
                        <div class="sanua-setting-row">
                            <div class="sanua-setting-row__text">
                                <p class="sanua-setting-row__title"><?php echo e(__('student.new_courses_notif')); ?></p>
                                <p class="sanua-setting-row__desc"><?php echo e(__('student.new_courses_notif_desc')); ?></p>
                            </div>
                            <label class="sanua-toggle">
                                <input type="hidden" name="notify_new_courses" value="0">
                                <input type="checkbox" name="notify_new_courses" value="1" <?php echo e(($preferences['notify_new_courses'] ?? true) ? 'checked' : ''); ?>>
                                <span class="sanua-toggle__slider"></span>
                            </label>
                        </div>
                        <div class="sanua-setting-row">
                            <div class="sanua-setting-row__text">
                                <p class="sanua-setting-row__title"><?php echo e(__('student.orders_notif')); ?></p>
                                <p class="sanua-setting-row__desc"><?php echo e(__('student.orders_notif_desc')); ?></p>
                            </div>
                            <label class="sanua-toggle">
                                <input type="hidden" name="notify_orders" value="0">
                                <input type="checkbox" name="notify_orders" value="1" <?php echo e(($preferences['notify_orders'] ?? true) ? 'checked' : ''); ?>>
                                <span class="sanua-toggle__slider"></span>
                            </label>
                        </div>
                        <div class="sanua-setting-row">
                            <div class="sanua-setting-row__text">
                                <p class="sanua-setting-row__title"><?php echo e(__('student.exams_notif')); ?></p>
                                <p class="sanua-setting-row__desc"><?php echo e(__('student.exams_notif_desc')); ?></p>
                            </div>
                            <label class="sanua-toggle">
                                <input type="hidden" name="notify_exams" value="0">
                                <input type="checkbox" name="notify_exams" value="1" <?php echo e(($preferences['notify_exams'] ?? true) ? 'checked' : ''); ?>>
                                <span class="sanua-toggle__slider"></span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="sanua-section">
            <div class="sanua-panel">
                <div class="sanua-panel__head">
                    <h3><i class="fas fa-shield-halved ml-1"></i> إعدادات الخصوصية</h3>
                </div>
                <div class="sanua-panel__body">
                    <div class="sanua-settings-list">
                        <div class="sanua-setting-row">
                            <div class="sanua-setting-row__text">
                                <p class="sanua-setting-row__title">إظهار التقدم للمعلمين</p>
                                <p class="sanua-setting-row__desc">السماح للمعلمين برؤية تقدمك في الكورسات</p>
                            </div>
                            <label class="sanua-toggle">
                                <input type="hidden" name="show_progress_to_teachers" value="0">
                                <input type="checkbox" name="show_progress_to_teachers" value="1" <?php echo e(($preferences['show_progress_to_teachers'] ?? true) ? 'checked' : ''); ?>>
                                <span class="sanua-toggle__slider"></span>
                            </label>
                        </div>
                        <div class="sanua-setting-row">
                            <div class="sanua-setting-row__text">
                                <p class="sanua-setting-row__title">إظهار النشاط</p>
                                <p class="sanua-setting-row__desc">إظهار آخر نشاط لك في المنصة</p>
                            </div>
                            <label class="sanua-toggle">
                                <input type="hidden" name="show_activity" value="0">
                                <input type="checkbox" name="show_activity" value="1" <?php echo e(($preferences['show_activity'] ?? false) ? 'checked' : ''); ?>>
                                <span class="sanua-toggle__slider"></span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="sanua-section">
            <div class="sanua-panel">
                <div class="sanua-panel__head">
                    <h3><i class="fas fa-palette ml-1"></i> إعدادات العرض</h3>
                </div>
                <div class="sanua-panel__body">
                    <div class="sanua-form-grid">
                        <div class="sanua-field">
                            <label for="theme">المظهر</label>
                            <select name="theme" id="theme" class="sanua-filter-form__field select" style="width:100%;padding:10px 12px;border-radius:12px;border:1px solid #EDE9FE;background:#FAFAFF;font-size:0.78rem;font-weight:700;">
                                <option value="light" <?php echo e(($preferences['theme'] ?? 'light') === 'light' ? 'selected' : ''); ?>>فاتح</option>
                                <option value="dark" <?php echo e(($preferences['theme'] ?? 'light') === 'dark' ? 'selected' : ''); ?>>داكن</option>
                                <option value="auto" <?php echo e(($preferences['theme'] ?? 'light') === 'auto' ? 'selected' : ''); ?>>تلقائي</option>
                            </select>
                        </div>
                        <div class="sanua-field">
                            <label for="locale">اللغة</label>
                            <select name="locale" id="locale" class="sanua-filter-form__field select" style="width:100%;padding:10px 12px;border-radius:12px;border:1px solid #EDE9FE;background:#FAFAFF;font-size:0.78rem;font-weight:700;">
                                <option value="ar" <?php echo e(($preferences['locale'] ?? 'ar') === 'ar' ? 'selected' : ''); ?>>العربية</option>
                                <option value="en" <?php echo e(($preferences['locale'] ?? 'ar') === 'en' ? 'selected' : ''); ?>>English</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="sanua-section">
            <div class="sanua-panel">
                <div class="sanua-panel__head">
                    <h3><i class="fas fa-user-cog ml-1"></i> الحساب</h3>
                </div>
                <div class="sanua-panel__body">
                    <div class="sanua-settings-list">
                        <div class="sanua-setting-row">
                            <div class="sanua-setting-row__text">
                                <p class="sanua-setting-row__title">تغيير كلمة المرور</p>
                                <p class="sanua-setting-row__desc">يمكنك تحديث كلمة المرور من الملف الشخصي</p>
                            </div>
                            <a href="<?php echo e(route('profile')); ?>" class="sanua-btn sanua-btn--purple" style="padding:8px 14px;font-size:0.75rem;">
                                <i class="fas fa-key"></i> الملف الشخصي
                            </a>
                        </div>
                        <?php if(Route::has('student.support.index')): ?>
                            <div class="sanua-setting-row">
                                <div class="sanua-setting-row__text">
                                    <p class="sanua-setting-row__title">الدعم الفني</p>
                                    <p class="sanua-setting-row__desc">لطلبات حذف الحساب أو تصدير البيانات تواصل مع الدعم</p>
                                </div>
                                <a href="<?php echo e(route('student.support.index')); ?>" class="sanua-btn sanua-btn--muted" style="padding:8px 14px;font-size:0.75rem;">
                                    <i class="fas fa-headset"></i> فتح تذكرة
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>

        <div class="sanua-form-actions" style="border-top:none;padding-top:0;">
            <p style="margin:0;font-size:0.72rem;font-weight:600;color:#94a3b8;">
                <i class="fas fa-info-circle" style="color:#8B5CF6;"></i>
                تُحفظ الإعدادات على حسابك وتُطبَّق فوراً.
            </p>
            <button type="submit" class="sanua-btn sanua-btn--purple">
                <i class="fas fa-save"></i>
                حفظ جميع الإعدادات
            </button>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sana\resources\views\student\settings\index.blade.php ENDPATH**/ ?>