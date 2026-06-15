<?php
    $brand = config('app.name');
    $logoUrl = $navbarLogoUrl ?? \App\Services\AdminPanelBranding::logoPublicUrl();
?>
<footer class="sana-foot-m">
    <div class="sana-container">
        <div class="sana-foot-m__grid">
            <div class="sana-foot-m__brand">
                <a href="<?php echo e(route('home')); ?>" class="sana-foot-m__logo">
                    <?php if($logoUrl): ?><img src="<?php echo e($logoUrl); ?>" alt=""><?php endif; ?>
                    <span><?php echo e(strtoupper($brand)); ?></span>
                </a>
                <p>منصة تعليمية عربية تفاعلية للأطفال والطلاب — تعلّم بمتعة وثقة.</p>
                <div class="sana-foot-m__social">
                    <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" aria-label="X"><i class="fab fa-x-twitter"></i></a>
                    <a href="#" aria-label="TikTok"><i class="fab fa-tiktok"></i></a>
                    <a href="#" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
            <div>
                <h4>تصفّح</h4>
                <ul>
                    <li><a href="<?php echo e(route('public.how_it_works')); ?>">كيف تعمل سنا؟</a></li>
                    <?php if($hasPublishedCourses ?? false): ?>
                        <li><a href="<?php echo e(route('public.courses')); ?>">الكورسات</a></li>
                    <?php endif; ?>
                    <?php if($hasPublicInstructors ?? false): ?>
                        <li><a href="<?php echo e(route('public.instructors.index')); ?>">المعلّمون</a></li>
                    <?php endif; ?>
                    <li><a href="<?php echo e(route('public.pricing')); ?>">الأسعار</a></li>
                    <?php if($hasPublishedCourses ?? false): ?>
                        <li><a href="<?php echo e(route('home')); ?>#categories">التصنيفات</a></li>
                    <?php endif; ?>
                </ul>
            </div>
            <div>
                <h4>روابط مهمة</h4>
                <ul>
                    <li><a href="<?php echo e(route('public.about')); ?>">من نحن</a></li>
                    <li><a href="<?php echo e(route('public.help')); ?>">مركز المساعدة</a></li>
                    <li><a href="<?php echo e(route('public.contact')); ?>">اتصل بنا</a></li>
                    <li><a href="<?php echo e(route('public.faq')); ?>">الأسئلة الشائعة</a></li>
                    <li><a href="<?php echo e(route('public.privacy')); ?>">الخصوصية</a></li>
                    <li><a href="<?php echo e(route('public.terms')); ?>">الشروط والأحكام</a></li>
                </ul>
            </div>
            <div>
                <h4>اشترك في النشرة</h4>
                <p class="sub">آخر الدورات والعروض إلى بريدك.</p>
                <form class="sana-foot-m__form" onsubmit="return false;">
                    <input type="email" placeholder="بريدك الإلكتروني">
                    <button type="button" class="sana-btn sana-btn--yellow">اشترك</button>
                </form>
            </div>
        </div>
        <p class="sana-foot-m__copy">&copy; <?php echo e(date('Y')); ?> <?php echo e($brand); ?>. جميع الحقوق محفوظة.</p>
    </div>
</footer>
<?php /**PATH C:\xampp\htdocs\sana\resources\views/landing/sana/footer.blade.php ENDPATH**/ ?>