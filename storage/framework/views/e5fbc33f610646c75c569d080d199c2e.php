<?php ($c = config('brand.colors')); ?>
    :root {
        /* أزرق — اللون الرئيسي (أزرار، روابط، شريط التقدّم) */
        --edu-primary: <?php echo e($c['blue']); ?>;
        --edu-primary-dark: <?php echo e($c['blue_dark']); ?>;
        --edu-primary-light: <?php echo e($c['blue_light']); ?>;
        --edu-primary-rgb: <?php echo e($c['blue_rgb']); ?>;

        /* بنفسجي — تمييز وثانوي */
        --edu-purple: <?php echo e($c['purple']); ?>;
        --edu-purple-dark: <?php echo e($c['purple_dark']); ?>;
        --edu-purple-light: <?php echo e($c['purple_light']); ?>;
        --edu-purple-rgb: <?php echo e($c['purple_rgb']); ?>;

        /* أصفر — تمييز وشارات */
        --edu-accent: <?php echo e($c['yellow']); ?>;
        --edu-accent-dark: <?php echo e($c['yellow_dark']); ?>;
        --edu-accent-light: <?php echo e($c['yellow_light']); ?>;
        --edu-accent-rgb: <?php echo e($c['yellow_rgb']); ?>;

        --edu-navy: #0f172a;
        --edu-text: #1e293b;
        --edu-muted: #64748b;
        --edu-bg: #f8fafc;
        --edu-radius: 24px;
        --edu-radius-sm: 16px;
        --edu-shadow: 0 12px 40px -12px rgba(<?php echo e($c['blue_rgb']); ?>, 0.22);
        --edu-font: 'IBM Plex Sans Arabic', system-ui, sans-serif;
        --edu-gradient-cta: linear-gradient(135deg, <?php echo e($c['blue']); ?> 0%, <?php echo e($c['purple']); ?> 55%, #5b8ef5 100%);
        --edu-gradient-auth: linear-gradient(145deg, #0b1f3a 0%, <?php echo e($c['blue']); ?> 42%, <?php echo e($c['purple']); ?> 100%);
    }
<?php /**PATH C:\xampp\htdocs\sana\resources\views\landing\eduvalt\brand-vars.blade.php ENDPATH**/ ?>