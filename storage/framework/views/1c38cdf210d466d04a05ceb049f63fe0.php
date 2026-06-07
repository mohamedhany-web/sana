<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>دخول الفريق — <?php echo e(config('app.name')); ?></title>
    <meta name="theme-color" content="<?php echo e(config('brand.colors.blue')); ?>">
    <?php echo $__env->make('partials.favicon-links', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('auth.partials.geometric-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <?php echo $__env->make('auth.partials.geometric-engine', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</head>
<body class="geo-page" x-data="staffLoginExperience()" x-init="init()">
    <canvas id="geo-canvas" aria-hidden="true"></canvas>

    <div class="geo-layer">
        <nav class="geo-nav">
            <?php echo $__env->make('auth.partials.geo-brand-logo', ['geoBrandSize' => 'nav'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </nav>

        <main class="geo-stage">
            <div class="geo-panel geo-login-panel">
                <?php echo $__env->make('auth.partials.geo-brand-logo', ['geoBrandSize' => 'mark'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

                <h1 class="geo-headline">دخول<br><em>الفريق</em></h1>
                <p class="geo-lead">للإدارة والمدربين وموظفي المنصة</p>

                <?php if(session('status')): ?>
                <div class="geo-alert geo-alert--ok"><?php echo e(session('status')); ?></div>
                <?php endif; ?>
                <?php if(session('error')): ?>
                <div class="geo-alert geo-alert--err"><?php echo e(session('error')); ?></div>
                <?php endif; ?>
                <?php if($errors->any()): ?>
                <div class="geo-alert geo-alert--err">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $err): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php echo e($err); ?><?php if(!$loop->last): ?><br><?php endif; ?> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <?php endif; ?>

                <form action="<?php echo e(route('staff.login')); ?>" method="POST" @submit="onSubmit" x-ref="loginForm">
                    <?php echo csrf_field(); ?>
                    <div style="display:none" aria-hidden="true">
                        <input type="text" name="website" tabindex="-1" autocomplete="off">
                    </div>

                    <div class="geo-field-wrap">
                        <input type="email" name="email" id="email" value="<?php echo e(old('email')); ?>"
                               required autocomplete="username" autofocus
                               class="geo-field <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               placeholder="البريد الإلكتروني" dir="ltr"
                               @focus="onEmailFocus()" @input="onEmailInput()">
                        <span class="geo-field-line"></span>
                    </div>

                    <div class="geo-field-wrap">
                        <input :type="showPassword ? 'text' : 'password'" name="password" id="password" required
                               class="geo-field <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               placeholder="كلمة المرور" autocomplete="current-password"
                               @focus="onPasswordFocus()" @input="onPasswordInput()">
                        <button type="button" class="geo-pw-toggle" @click="showPassword = !showPassword" tabindex="-1">
                            <span x-text="showPassword ? 'إخفاء' : 'إظهار'" style="font-size:.7rem;font-weight:600"></span>
                        </button>
                        <span class="geo-field-line"></span>
                    </div>

                    <div class="geo-row">
                        <label class="geo-check">
                            <input type="checkbox" name="remember">
                            <span>تذكّرني</span>
                        </label>
                        <a href="<?php echo e(route('password.request')); ?>" class="geo-link">نسيت كلمة المرور؟</a>
                    </div>

                    <button type="submit" class="geo-cta magnetic" x-ref="submitBtn" :disabled="submitting">
                        <span x-text="submitting ? 'جاري الدخول...' : 'دخول'"></span>
                        <span x-show="!submitting">→</span>
                    </button>
                </form>

                <p style="margin-top:2rem;font-size:.85rem;color:var(--edu-muted)">
                    طالب أو ولي أمر؟ <a href="<?php echo e(route('login')); ?>" class="geo-link">الدخول من الصفحة العامة</a>
                </p>
                <p style="margin-top:.75rem;font-size:.85rem;color:var(--edu-muted)">
                    معلم حصص جديد؟ <a href="<?php echo e(route('tutor.apply')); ?>" class="geo-link">انضم كمعلم</a>
                </p>
            </div>
        </main>
    </div>

<script>
function staffLoginExperience() {
    return {
        showPassword: false,
        submitting: false,
        geo: null,

        init() {
            this.geo = window.createGeoEngine(document.getElementById('geo-canvas'));
            this.geo.setPhase('idle');
            this.$nextTick(() => window.initMagnetic(this.$refs.submitBtn));
        },

        onEmailFocus() { this.geo?.setPhase('email'); this.geo?.setEmailPulse(0.3); },
        onEmailInput() { this.geo?.setEmailPulse(0.6); },
        onPasswordFocus() { this.geo?.setPhase('password'); this.geo?.triggerWave(); },
        onPasswordInput() { this.geo?.setConnectStrength(Math.min(1, (document.getElementById('password')?.value.length || 0) / 12)); },

        async onSubmit(e) {
            if (this.submitting) { e.preventDefault(); return; }
            const form = this.$refs.loginForm;
            if (!form.checkValidity()) return;
            e.preventDefault();
            this.submitting = true;
            if (this.geo) await this.geo.waitConverge(850);
            form.submit();
        },
    };
}
</script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\sana\resources\views\auth\staff-login.blade.php ENDPATH**/ ?>