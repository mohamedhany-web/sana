<?php
    $brand = config('app.name');
    $eyebrow = $eyebrow ?? 'تعلّم أونلاين باحتراف';
    $titleMain = $titleMain ?? 'انضم إلى آلاف المتعلمين';
    $titleEm = $titleEm ?? 'وابدأ رحلتك اليوم';
    $captionText = $captionText ?? 'تعلّم تفاعلي مع أفضل المدربين';
    $footnote = $footnote ?? ('صور حقيقية من بيئة تعليمية — استكشف الدورات بعد التسجيل على ' . $brand . '.');
    $compact = !empty($compact);
    $logoUrl = $adminPanelLogoUrl ?? \App\Services\AdminPanelBranding::logoPublicUrl();
    $letter = mb_substr($brand, 0, 1);
    $photos = [
        'main' => 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?w=800&auto=format&fit=crop&q=80',
        'instructor' => 'https://images.unsplash.com/photo-1560250097-0b93528c311a?w=500&auto=format&fit=crop&q=80',
        'classroom' => 'https://images.unsplash.com/photo-1524178232363-7f38b04a8a6e?w=500&auto=format&fit=crop&q=80',
    ];
?>
<aside class="auth-visual auth-visual--showcase<?php echo e($compact ? ' auth-visual--compact' : ''); ?>" aria-label="نظرة على المنصة">
    <div class="auth-showcase-glow auth-showcase-glow--1"></div>
    <div class="auth-showcase-glow auth-showcase-glow--2"></div>

    <div class="auth-showcase-inner">
        <a href="<?php echo e(route('home')); ?>" class="auth-brand auth-brand--light">
            <span class="auth-brand-logo auth-brand-logo--glass">
                <?php if($logoUrl): ?>
                    <img src="<?php echo e($logoUrl); ?>" alt="<?php echo e($brand); ?>" width="48" height="48" loading="eager">
                <?php else: ?>
                    <?php echo e($letter); ?>

                <?php endif; ?>
            </span>
            <span class="auth-brand-name"><?php echo e($brand); ?></span>
        </a>

        <p class="auth-showcase-eyebrow"><?php echo e($eyebrow); ?></p>
        <h1 class="auth-showcase-title">
            <?php echo e($titleMain); ?>

            <em><?php echo e($titleEm); ?></em>
        </h1>

        <div class="auth-photo-collage">
            <figure class="auth-photo auth-photo--main">
                <img src="<?php echo e($photos['main']); ?>" alt="طلاب يتعلمون معاً" width="600" height="720" loading="eager">
                <figcaption class="auth-photo-caption">
                    <i class="fas fa-graduation-cap"></i>
                    <span><?php echo e($captionText); ?></span>
                </figcaption>
            </figure>

            <?php if (! ($compact)): ?>
            <figure class="auth-photo auth-photo--sub auth-photo--sub-a auth-float">
                <img src="<?php echo e($photos['instructor']); ?>" alt="مدرب" width="280" height="340" loading="lazy">
                <figcaption class="auth-photo-tag">مدربون خبراء</figcaption>
            </figure>

            <figure class="auth-photo auth-photo--sub auth-photo--sub-b auth-float-delay">
                <img src="<?php echo e($photos['classroom']); ?>" alt="قاعة تعليمية" width="280" height="340" loading="lazy">
                <figcaption class="auth-photo-tag">جلسات مباشرة</figcaption>
            </figure>
            <?php endif; ?>
        </div>

        <?php if (! ($compact)): ?>
        <p class="auth-showcase-footnote"><?php echo e($footnote); ?></p>
        <?php endif; ?>
    </div>
</aside>
<?php /**PATH C:\xampp\htdocs\sana\resources\views\auth\partials\eduvalt-visual.blade.php ENDPATH**/ ?>