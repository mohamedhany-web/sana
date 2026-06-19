<?php
    $faqs = [
        ['q' => 'هل المنصة مناسبة لجميع الأعمار؟', 'a' => 'نعم، محتوىنا مصمّم للأطفال من 8 إلى 16 سنة مع مسارات حسب العمر والمستوى.'],
        ['q' => 'كيف أبدأ مع ابني على المنصة؟', 'a' => 'احجز تقييم مستوى مجاني — نحدّد معك المعلّم أو الباقة المناسبة قبل أي التزام.'],
        ['q' => 'كيف أتابع تقدّم ابني؟', 'a' => 'لوحة وليّ الأمر تعرض التقدّم والحضور والاختبارات بوضوح.'],
        ['q' => 'ما نوع الشهادات التي تُصدَر؟', 'a' => 'شهادات إتمام رقمية قابلة للتحقق عبر رابط ورمز فريد — تثبت إنجازك على المنصة.'],
        ['q' => 'هل الحصص المباشرة مسجّلة؟', 'a' => 'نعم، معظم الحصص متاحة للمشاهدة لاحقاً من حساب الطالب.'],
    ];
    $faqChar = public_static_exists('img/sanua/landing-hero-boy.png')
        ? public_static_url('img/sanua/landing-hero-boy.png')
        : public_static_url('img/sanua/hero-boy.png');
?>
<section class="sana-section sana-section--white" id="faq">
    <div class="sana-container">
        <div class="sana-head sana-reveal">
            <h2 class="sana-head__title">الأسئلة <span class="hl">الشائعة</span></h2>
            <span class="sana-head__line"></span>
        </div>
        <div class="sana-faq-m sana-reveal">
            <div class="sana-faq-m__visual">
                <img src="<?php echo e($faqChar); ?>" alt="">
                <span class="bubble bubble--1">🤔</span>
                <span class="bubble bubble--2">💡</span>
            </div>
            <div class="sana-faq" id="sana-faq">
                <?php $__currentLoopData = $faqs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $faq): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="sana-faq-item <?php echo e($i === 0 ? 'is-open' : ''); ?>">
                    <button type="button" class="sana-faq-q"><?php echo e($faq['q']); ?> <i class="fas fa-chevron-down"></i></button>
                    <div class="sana-faq-a"><?php echo e($faq['a']); ?></div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>
</section>
<?php /**PATH C:\xampp\htdocs\sana\resources\views/landing/sana/sections/faq.blade.php ENDPATH**/ ?>