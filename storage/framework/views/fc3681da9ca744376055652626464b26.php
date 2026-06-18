<?php
    $pf = $publicFooter ?? \App\Services\PublicFooterSettings::payload();
    $contact = $publicContact ?? \App\Support\PublicContactInfo::payload();
    $brand = config('app.name');
    $logoUrl = $navbarLogoUrl ?? \App\Services\AdminPanelBranding::logoPublicUrl();
    $telHref = '';
    if (! empty($pf['phone'])) {
        $digits = preg_replace('/[^\d+]/', '', $pf['phone']);
        $telHref = $digits !== '' ? 'tel:'.$digits : '';
    }
?>
<footer class="sana-foot-m">
    <div class="sana-container">
        <div class="sana-foot-m__grid">
            <div class="sana-foot-m__brand">
                <a href="<?php echo e(route('home')); ?>" class="sana-foot-m__logo">
                    <?php if($logoUrl): ?><img src="<?php echo e($logoUrl); ?>" alt="<?php echo e($brand); ?>"><?php endif; ?>
                    <span><?php echo e(strtoupper($brand)); ?></span>
                </a>
                <p><?php echo e($pf['blurb'] ?: 'منصة تعليمية عربية تفاعلية للأطفال والطلاب — تعلّم بمتعة وثقة.'); ?></p>
                <?php if(! empty($pf['socials'])): ?>
                <div class="sana-foot-m__social">
                    <?php $__currentLoopData = $pf['socials']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $soc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e(e($soc['url'])); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo e(e($soc['label'])); ?>" title="<?php echo e(e($soc['label'])); ?>">
                            <i class="<?php echo e(e($soc['icon'])); ?>"></i>
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <?php endif; ?>
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
                    <li><a href="<?php echo e(route('tutor.apply')); ?>"><?php echo e(__('public.footer_teacher_apply')); ?></a></li>
                </ul>
            </div>
            <div>
                <h4>تواصل معنا</h4>
                <ul>
                    <?php if(! empty($pf['email'])): ?>
                        <li><a href="mailto:<?php echo e(e($pf['email'])); ?>"><?php echo e($pf['email']); ?></a></li>
                    <?php endif; ?>
                    <?php if(! empty($pf['phone'])): ?>
                        <li>
                            <?php if($telHref !== ''): ?>
                                <a href="<?php echo e($telHref); ?>" rel="nofollow"><?php echo e($pf['phone']); ?></a>
                            <?php else: ?>
                                <span><?php echo e($pf['phone']); ?></span>
                            <?php endif; ?>
                        </li>
                    <?php endif; ?>
                    <?php if(! empty($pf['whatsapp_url'])): ?>
                        <li>
                            <a href="<?php echo e(e($pf['whatsapp_url'])); ?>" target="_blank" rel="noopener noreferrer">
                                <i class="fab fa-whatsapp"></i> واتساب
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if(! empty($pf['address'])): ?>
                        <li><span><?php echo e($pf['address']); ?></span></li>
                    <?php endif; ?>
                    <?php if(! empty($pf['support_hours'])): ?>
                        <li><span><?php echo e($pf['support_hours']); ?></span></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
        <p class="sana-foot-m__copy">
            &copy; <?php echo e(date('Y')); ?> <?php echo e($brand); ?>.
            <?php echo e($pf['bottom_tagline'] ?: 'جميع الحقوق محفوظة.'); ?>

        </p>
    </div>
</footer>

<?php echo $__env->make('partials.whatsapp-fab', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php /**PATH C:\xampp\htdocs\sana\resources\views/landing/sana/footer.blade.php ENDPATH**/ ?>