<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>تسجيل الدخول — <?php echo e(config('app.name')); ?></title>
    <meta name="theme-color" content="<?php echo e(config('brand.colors.blue')); ?>">
    <?php echo $__env->make('partials.favicon-links', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('auth.partials.eduvalt-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body x-data="{ showPassword: false }">
    <div class="auth-shell">

        <?php echo $__env->make('auth.partials.eduvalt-visual', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <main class="auth-form-panel">
            <div class="auth-form-inner">
                <div class="auth-mobile-brand">
                    <?php echo $__env->make('auth.partials.eduvalt-brand', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>

                <div class="auth-form-head">
                    <h2 class="auth-form-title">تسجيل الدخول</h2>
                    <p class="auth-form-subtitle">أدخل بياناتك للوصول إلى حسابك</p>
                </div>

                <form action="<?php echo e(route('login')); ?>" method="POST" class="auth-form">
                    <?php echo csrf_field(); ?>

                    <?php if(session('status')): ?>
                    <div class="auth-alert-ok">
                        <i class="fas fa-check-circle"></i>
                        <span><?php echo e(session('status')); ?></span>
                    </div>
                    <?php endif; ?>

                    <?php if(session('warning')): ?>
                    <div class="auth-alert-warn">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span><?php echo e(session('warning')); ?></span>
                    </div>
                    <?php endif; ?>

                    <div style="display:none" aria-hidden="true">
                        <input type="text" name="website" tabindex="-1" autocomplete="off">
                    </div>

                    <div>
                        <label for="email" class="auth-label">البريد الإلكتروني</label>
                        <div class="auth-field">
                            <span class="auth-field-icon"><i class="fas fa-envelope"></i></span>
                            <input type="email" name="email" id="email" value="<?php echo e(old('email')); ?>"
                                   required autocomplete="email" autofocus
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
                                   placeholder="••••••••">
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

                    <div class="auth-row">
                        <label class="auth-remember">
                            <input type="checkbox" name="remember">
                            <span>تذكّرني</span>
                        </label>
                        <a href="<?php echo e(route('password.request')); ?>" class="auth-link">نسيت كلمة المرور؟</a>
                    </div>

                    <button type="submit" class="auth-btn-primary">
                        <span>تسجيل الدخول</span>
                        <i class="fas fa-arrow-left"></i>
                    </button>
                </form>

                <div class="auth-divider">
                    ليس لديك حساب؟
                    <a href="<?php echo e(route('register')); ?>" class="auth-link">أنشئ حسابك مجاناً</a>
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
<?php /**PATH C:\xampp\htdocs\sana\resources\views\auth\login.blade.php ENDPATH**/ ?>