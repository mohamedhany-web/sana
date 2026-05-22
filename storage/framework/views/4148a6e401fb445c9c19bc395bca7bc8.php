<?php
    $defaultDialCode = is_array($defaultCountry ?? null) ? ($defaultCountry['dial_code'] ?? '+966') : '+966';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>إنشاء حساب — <?php echo e(config('app.name')); ?></title>
    <meta name="theme-color" content="<?php echo e(config('brand.colors.blue')); ?>">
    <?php echo $__env->make('partials.favicon-links', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('auth.partials.eduvalt-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body x-data="{ showPassword: false, showPasswordConfirm: false }">
    <div class="auth-shell auth-shell--register">

        <?php echo $__env->make('auth.partials.eduvalt-visual', [
            'compact' => true,
            'eyebrow' => 'لمن صُمّمت المنصة',
            'titleMain' => 'بيئة واحدة',
            'titleEm' => 'للطلاب وأولياء الأمور والمعلمين',
            'captionText' => 'حصص مباشرة، كورسات، وتقارير متابعة',
        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <main class="auth-form-panel">
            <div class="auth-form-inner auth-form-inner--wide">
                <div class="auth-mobile-brand">
                    <?php echo $__env->make('auth.partials.eduvalt-brand', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>

                <div class="auth-form-head">
                    <h2 class="auth-form-title">إنشاء حساب جديد</h2>
                    <p class="auth-form-subtitle">انضم إلى <?php echo e(config('app.name')); ?> كولي أمر، طالب، أو معلم</p>
                </div>

                <div class="auth-notices">
                    <div class="auth-notice auth-notice--info">
                        <i class="fas fa-info-circle"></i>
                        <span>هذا النموذج لتسجيل أولياء الأمور والطلاب والمعلمين. للتسجيل كمدرب مؤسسي تواصل مع فريق الدعم.</span>
                    </div>

                    <?php if(!empty($pendingReferralCode)): ?>
                    <div class="auth-notice auth-notice--referral">
                        <i class="fas fa-gift"></i>
                        <div>
                            <strong>أنت تسجّل عبر رابط دعوة</strong><br>
                            سيتم ربط حسابك بكود الإحالة
                            <span dir="ltr" style="font-family: ui-monospace, monospace;"><?php echo e($pendingReferralCode); ?></span>
                            بعد إتمام التسجيل (إن كان البرنامج مفعّلاً).
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <form action="<?php echo e(route('register')); ?>" method="POST" class="auth-form">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="referral_code" value="<?php echo e(old('referral_code', $pendingReferralCode ?? '')); ?>">

                    <div class="auth-form-grid">
                        <div>
                            <label for="name" class="auth-label">الاسم الكامل</label>
                            <div class="auth-field">
                                <span class="auth-field-icon"><i class="fas fa-user"></i></span>
                                <input type="text" name="name" id="name" value="<?php echo e(old('name')); ?>" required autofocus
                                       class="auth-input auth-input--icon <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                       placeholder="أدخل اسمك الكامل">
                            </div>
                            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="auth-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="auth-field-full">
                            <label class="auth-label">رقم الهاتف</label>
                            <div class="auth-phone-row <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                <select name="country_code" required dir="ltr" aria-label="رمز الدولة">
                                    <?php $__currentLoopData = $phoneCountries ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($c['dial_code']); ?>" <?php echo e(old('country_code', $defaultDialCode) === $c['dial_code'] ? 'selected' : ''); ?>>
                                        <?php echo e($c['dial_code']); ?> <?php echo e($c['name_ar']); ?>

                                    </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <input type="tel" name="phone" value="<?php echo e(old('phone')); ?>" required placeholder="5xxxxxxxx" dir="ltr" inputmode="tel">
                            </div>
                            <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="auth-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div>
                            <label for="email" class="auth-label">البريد الإلكتروني</label>
                            <div class="auth-field">
                                <span class="auth-field-icon"><i class="fas fa-envelope"></i></span>
                                <input type="email" name="email" id="email" value="<?php echo e(old('email')); ?>" required
                                       class="auth-input auth-input--icon <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                       placeholder="example@email.com" dir="ltr">
                            </div>
                            <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="auth-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div>
                            <label for="password" class="auth-label">كلمة المرور</label>
                            <div class="auth-field">
                                <span class="auth-field-icon"><i class="fas fa-lock"></i></span>
                                <input :type="showPassword ? 'text' : 'password'" name="password" id="password" required
                                       class="auth-input auth-input--icon auth-input--toggle <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                       placeholder="كلمة مرور قوية">
                                <button type="button" class="auth-toggle" @click="showPassword = !showPassword"
                                        aria-label="إظهار كلمة المرور">
                                    <i class="fas" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                                </button>
                            </div>
                            <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="auth-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="auth-span-2">
                            <label for="password_confirmation" class="auth-label">تأكيد كلمة المرور</label>
                            <div class="auth-field">
                                <span class="auth-field-icon"><i class="fas fa-lock"></i></span>
                                <input :type="showPasswordConfirm ? 'text' : 'password'" name="password_confirmation" id="password_confirmation" required
                                       class="auth-input auth-input--icon auth-input--toggle"
                                       placeholder="أعد كتابة كلمة المرور">
                                <button type="button" class="auth-toggle" @click="showPasswordConfirm = !showPasswordConfirm"
                                        aria-label="إظهار تأكيد كلمة المرور">
                                    <i class="fas" :class="showPasswordConfirm ? 'fa-eye-slash' : 'fa-eye'"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <label class="auth-terms">
                        <input type="checkbox" id="terms" required>
                        <span>
                            أوافق على
                            <a href="<?php echo e(route('public.terms')); ?>" class="auth-link" target="_blank" rel="noopener">شروط الاستخدام</a>
                            و
                            <a href="<?php echo e(route('public.privacy')); ?>" class="auth-link" target="_blank" rel="noopener">سياسة الخصوصية</a>
                        </span>
                    </label>

                    <button type="submit" class="auth-btn-primary">
                        <span>إنشاء الحساب</span>
                        <i class="fas fa-user-plus"></i>
                    </button>
                </form>

                <div class="auth-divider">
                    لديك حساب بالفعل؟
                    <a href="<?php echo e(route('login')); ?>" class="auth-link">سجّل دخولك</a>
                </div>

                <div class="auth-back-wrap">
                    <a href="<?php echo e(route('home')); ?>" class="auth-back">
                        <i class="fas fa-arrow-right"></i>
                        العودة للرئيسية
                    </a>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\sana\resources\views\auth\register.blade.php ENDPATH**/ ?>