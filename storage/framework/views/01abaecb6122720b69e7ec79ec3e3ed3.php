<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>أهلاً بك — <?php echo e(config('app.name')); ?></title>
    <meta name="theme-color" content="<?php echo e(config('brand.colors.blue')); ?>">
    <?php echo $__env->make('partials.favicon-links', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('auth.partials.geometric-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('auth.partials.geometric-engine', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="geo-page" x-data="{}" x-init="window.createGeoEngine(document.getElementById('geo-canvas'))?.startConverge()">
    <canvas id="geo-canvas" aria-hidden="true"></canvas>

    <div class="geo-layer">
        <main class="geo-stage">
            <div class="geo-panel geo-login-panel">
                <?php echo $__env->make('auth.partials.geo-brand-logo', ['geoBrandSize' => 'mark'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <span class="geo-step-tag">اكتمل التكوين</span>
                <h1 class="geo-headline">أهلاً <em><?php echo e($userName ?? 'بك'); ?></em></h1>
                <p class="geo-lead">
                    <?php if(!empty($personalizeHint)): ?>
                    <?php echo e($personalizeHint); ?>

                    <?php else: ?>
                    حسابك جاهز — الأشكال اكتملت.
                    <?php endif; ?>
                </p>
                <a href="<?php echo e(route($dashboardRoute ?? 'dashboard')); ?>" class="geo-cta magnetic" style="margin-top:1rem">ابدأ →</a>
                <?php if(($dashboardRoute ?? 'dashboard') === 'dashboard'): ?>
                <p style="margin-top:1.5rem;font-size:.78rem;color:var(--edu-muted)">
                    وليّ الأمر؟ يمكنه إنشاء حساب مرتبط من صفحة التسجيل.
                </p>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\sana\resources\views\auth\register-complete.blade.php ENDPATH**/ ?>