<?php $__env->startSection('title', 'الملف الشخصي - ' . ($platformName ?? config('brand.name', config('app.name')))); ?>
<?php $__env->startSection('page_title', 'الملف الشخصي'); ?>

<?php $__env->startSection('content'); ?>
<?php
    use App\Support\PlatformBranding;

    $roleLabels = [
        'admin' => 'إداري',
        'super_admin' => 'مدير عام',
    ];
    $roleLabel = $roleLabels[$user->role] ?? 'إداري';
    $displayName = PlatformBranding::greetingDisplayName($user->name);

    $memberSince = $user->created_at ? $user->created_at->copy()->locale('ar')->translatedFormat('d F Y') : '—';
    $lastLogin = $user->last_login_at ? $user->last_login_at->copy()->locale('ar')->diffForHumans() : '—';
?>

<div class="admin-dashboard admin-profile-page space-y-7">

    <?php if(session('recovery_codes')): ?>
        <div class="admin-alert admin-alert--warning">
            <span class="admin-alert__icon"><i class="fas fa-key"></i></span>
            <div class="flex-1 min-w-0">
                <p class="font-bold mb-1">رموز الاسترداد — احفظها في مكان آمن</p>
                <p class="text-sm opacity-90 mb-3">استخدم أحد هذه الرموز للدخول إذا لم يكن معك جهاز المصادقة. كل رمز يُستخدم مرة واحدة فقط.</p>
                <div class="recovery-codes">
                    <?php $__currentLoopData = session('recovery_codes'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <span><?php echo e($code); ?></span>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
        <?php session()->forget('recovery_codes'); ?>
    <?php endif; ?>

    <?php if(session('success')): ?>
        <div class="admin-alert admin-alert--success">
            <span class="admin-alert__icon"><i class="fas fa-check"></i></span>
            <p class="font-semibold"><?php echo e(session('success')); ?></p>
        </div>
    <?php endif; ?>

    
    <div class="admin-dashboard-hero animate-fade-in">
        <div class="admin-dashboard-hero-inner">
            <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-6">
                <div class="flex flex-col sm:flex-row sm:items-center gap-5">
                    <div class="profile-avatar mx-auto sm:mx-0">
                        <?php if($user->profile_image): ?>
                            <img src="<?php echo e($user->profile_image_url); ?>" alt="" onerror="this.style.display='none'; this.nextElementSibling?.classList.remove('hidden');">
                            <span class="hidden"><?php echo e(mb_substr($displayName, 0, 1)); ?></span>
                        <?php else: ?>
                            <?php echo e(mb_substr($displayName, 0, 1)); ?>

                        <?php endif; ?>
                    </div>
                    <div class="text-center sm:text-start min-w-0">
                        <span class="admin-chip admin-chip--primary mb-2">
                            <i class="fas fa-user-shield"></i>
                            <?php echo e($roleLabel); ?>

                        </span>
                        <h1 class="hero-title text-2xl sm:text-3xl font-heading font-bold mb-1"><?php echo e($displayName); ?></h1>
                        <p class="hero-sub text-sm mb-3">إدارة بياناتك وإعدادات حسابك على <?php echo e($platformName ?? config('brand.name', config('app.name'))); ?></p>
                        <div class="flex flex-wrap justify-center sm:justify-start gap-2">
                            <?php if($user->phone): ?>
                                <span class="profile-contact-pill"><i class="fas fa-phone"></i><?php echo e($user->phone); ?></span>
                            <?php endif; ?>
                            <?php if($user->email): ?>
                                <span class="profile-contact-pill"><i class="fas fa-envelope"></i><?php echo e($user->email); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 xl:max-w-md xl:w-full shrink-0">
                    <div class="profile-hero-stat">
                        <div class="profile-hero-stat__label">تاريخ الانضمام</div>
                        <div class="profile-hero-stat__value"><?php echo e($memberSince); ?></div>
                    </div>
                    <div class="profile-hero-stat">
                        <div class="profile-hero-stat__label">نوع الحساب</div>
                        <div class="profile-hero-stat__value"><?php echo e($roleLabel); ?></div>
                    </div>
                    <div class="profile-hero-stat">
                        <div class="profile-hero-stat__label">آخر تسجيل دخول</div>
                        <div class="profile-hero-stat__value"><?php echo e($lastLogin); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="space-y-5">
            <div class="admin-side-card">
                <div class="admin-side-card__head">
                    <span class="admin-side-card__icon admin-side-card__icon--blue"><i class="fas fa-id-card"></i></span>
                    <h2>معلومات الحساب</h2>
                </div>
                <div class="admin-side-card__body">
                    <div class="admin-info-row">
                        <span class="admin-info-row__label">رقم العضوية</span>
                        <span class="admin-info-row__value">#<?php echo e(str_pad($user->id, 5, '0', STR_PAD_LEFT)); ?></span>
                    </div>
                    <div class="admin-info-row">
                        <span class="admin-info-row__label">نوع الحساب</span>
                        <span class="admin-chip admin-chip--primary"><?php echo e($roleLabel); ?></span>
                    </div>
                    <div class="admin-info-row <?php echo e($user->is_active ? 'admin-info-row--success' : 'admin-info-row--danger'); ?>">
                        <span class="admin-info-row__label">الحالة</span>
                        <span class="admin-info-row__value inline-flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full <?php echo e($user->is_active ? 'bg-emerald-500' : 'bg-rose-500'); ?>"></span>
                            <?php echo e($user->is_active ? 'نشط' : 'غير نشط'); ?>

                        </span>
                    </div>
                </div>
            </div>

            <div class="admin-side-card">
                <div class="admin-side-card__head">
                    <span class="admin-side-card__icon admin-side-card__icon--green"><i class="fas fa-shield-alt"></i></span>
                    <h2>المصادقة الثنائية</h2>
                </div>
                <div class="admin-side-card__body space-y-3">
                    <?php if($user->hasTwoFactorEnabled()): ?>
                        <p class="text-sm text-slate-600 leading-relaxed">مفعّلة — يُطلب رمز التحقق عند كل تسجيل دخول.</p>
                        <form action="<?php echo e(route('two-factor.disable')); ?>" method="POST" class="space-y-3" onsubmit="return confirm('هل تريد تعطيل المصادقة الثنائية؟ ستحتاج إدخال كلمة المرور.');">
                            <?php echo csrf_field(); ?>
                            <div class="admin-field">
                                <input type="password" name="password" required placeholder="كلمة المرور للتأكيد"
                                       class="admin-input admin-input--plain">
                                <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="text-rose-600 text-xs mt-1.5 font-medium"><?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <button type="submit" class="admin-btn admin-btn--danger-outline">
                                تعطيل المصادقة الثنائية
                            </button>
                        </form>
                    <?php else: ?>
                        <p class="text-sm text-slate-600 leading-relaxed">تفعيل المصادقة الثنائية يزيد أمان دخولك للمنصة.</p>
                        <a href="<?php echo e(route('two-factor.setup')); ?>" class="admin-btn admin-btn--success">
                            <i class="fas fa-mobile-alt"></i>
                            تفعيل المصادقة الثنائية
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        
        <div class="lg:col-span-2">
            <div class="admin-form-card">
                <div class="admin-form-card__head">
                    <div>
                        <h3>تحديث البيانات الأساسية</h3>
                        <p>قم بمراجعة معلوماتك وتحديثها في أي وقت</p>
                    </div>
                    <span class="admin-form-badge">
                        <i class="fas fa-lock"></i>
                        بياناتك مشفرة وآمنة
                    </span>
                </div>

                <div class="admin-form-card__body">
                    <form method="POST" action="<?php echo e(route('admin.profile.update')); ?>" class="space-y-6" enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div class="admin-field">
                                <label>الاسم الكامل</label>
                                <div class="admin-field-input-wrap">
                                    <i class="fas fa-user"></i>
                                    <input type="text" name="name" value="<?php echo e(old('name', $user->name)); ?>" required class="admin-input">
                                </div>
                                <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-rose-600 text-xs mt-1.5 font-medium"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <div class="admin-field">
                                <label>رقم الهاتف</label>
                                <div class="admin-field-input-wrap">
                                    <i class="fas fa-phone"></i>
                                    <input type="text" name="phone" value="<?php echo e(old('phone', $user->phone)); ?>" required class="admin-input">
                                </div>
                                <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-rose-600 text-xs mt-1.5 font-medium"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <div class="admin-field md:col-span-2">
                                <label>البريد الإلكتروني (اختياري)</label>
                                <div class="admin-field-input-wrap">
                                    <i class="fas fa-at"></i>
                                    <input type="email" name="email" value="<?php echo e(old('email', $user->email)); ?>" class="admin-input">
                                </div>
                                <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-rose-600 text-xs mt-1.5 font-medium"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>

                        <div class="admin-field">
                            <label>صورة الملف الشخصي</label>
                            <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                                <div class="admin-upload-preview">
                                    <?php if($user->profile_image): ?>
                                        <img src="<?php echo e($user->profile_image_url); ?>" alt="" class="w-full h-full object-cover" onerror="this.style.display='none'; this.nextElementSibling?.classList.remove('hidden');">
                                        <i class="fas fa-camera text-slate-300 text-2xl hidden"></i>
                                    <?php else: ?>
                                        <i class="fas fa-camera text-slate-300 text-2xl"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <label class="admin-upload-zone">
                                        <i class="fas fa-upload"></i>
                                        <span>اختر صورة (PNG أو JPG)</span>
                                        <input type="file" name="profile_image" accept="image/*" class="hidden">
                                    </label>
                                    <?php $__errorArgs = ['profile_image'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-rose-600 text-xs mt-1.5 font-medium"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                        </div>

                        <div class="admin-password-block space-y-4">
                            <div>
                                <h4>تغيير كلمة المرور</h4>
                                <p class="text-xs text-slate-500 mt-0.5">اترك الحقول فارغة إذا لم ترغب في التغيير</p>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="admin-field">
                                    <label>كلمة المرور الحالية</label>
                                    <input type="password" name="current_password" class="admin-input admin-input--plain">
                                    <?php $__errorArgs = ['current_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-rose-600 text-xs mt-1.5 font-medium"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                                <div class="admin-field">
                                    <label>كلمة المرور الجديدة</label>
                                    <input type="password" name="password" class="admin-input admin-input--plain">
                                    <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-rose-600 text-xs mt-1.5 font-medium"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                                <div class="admin-field">
                                    <label>تأكيد كلمة المرور</label>
                                    <input type="password" name="password_confirmation" class="admin-input admin-input--plain">
                                </div>
                            </div>
                        </div>

                        <div class="admin-form-actions">
                            <a href="<?php echo e(route('admin.dashboard')); ?>" class="admin-btn admin-btn--ghost">
                                <i class="fas fa-arrow-right"></i>
                                رجوع للوحة التحكم
                            </a>
                            <button type="submit" class="admin-btn admin-btn--primary">
                                <i class="fas fa-save"></i>
                                حفظ التعديلات
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sana\resources\views\admin\profile\index.blade.php ENDPATH**/ ?>