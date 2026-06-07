<?php
    $certCount = max((int)($homeStats['certificates'] ?? 0), 50000);
    $learnCount = max((int)($homeStats['learners'] ?? 0), 100000);
?>
<section class="sana-section" id="achievements">
    <div class="sana-container">
        <div class="sana-achieve-box sana-reveal">
            <div class="sana-achieve-box__glow sana-achieve-box__glow--1"></div>
            <div class="sana-achieve-box__glow sana-achieve-box__glow--2"></div>
            <div class="sana-achieve-box__inner">
                <div class="sana-achieve-box__content">
                    <span class="sana-achieve-box__tag"><i class="fas fa-certificate"></i> شهادات معتمدة</span>
                    <h2 class="sana-achieve-box__title">إنجازاتك... <span class="hl">تفخر بها</span></h2>
                    <p class="sana-achieve-box__desc">شهادات رقمية قابلة للتحقق تُصدَر تلقائياً بعد إتمام المسارات — يمكن مشاركتها وطباعتها في أي وقت.</p>
                    <div class="sana-achieve-box__stats">
                        <div class="sana-achieve-box__stat">
                            <div class="sana-achieve-box__stat-icon sana-achieve-box__stat-icon--gold"><i class="fas fa-award"></i></div>
                            <div>
                                <strong data-sana-counter="<?php echo e($certCount); ?>" data-sana-suffix="+">0+</strong>
                                <span>شهادة صادرة</span>
                            </div>
                        </div>
                        <div class="sana-achieve-box__stat">
                            <div class="sana-achieve-box__stat-icon sana-achieve-box__stat-icon--green"><i class="fas fa-user-graduate"></i></div>
                            <div>
                                <strong data-sana-counter="<?php echo e($learnCount); ?>" data-sana-suffix="+">0+</strong>
                                <span>طالب ناجح</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="sana-achieve-box__visual" aria-hidden="true">
                    <div class="sana-cert-mock">
                        <div class="sana-cert-mock__corner sana-cert-mock__corner--tl"></div>
                        <div class="sana-cert-mock__corner sana-cert-mock__corner--tr"></div>
                        <div class="sana-cert-mock__corner sana-cert-mock__corner--bl"></div>
                        <div class="sana-cert-mock__corner sana-cert-mock__corner--br"></div>
                        <div class="sana-cert-mock__seal"><i class="fas fa-star"></i></div>
                        <p class="sana-cert-mock__label">شهادة إتمام</p>
                        <h3 class="sana-cert-mock__brand"><?php echo e(config('app.name')); ?></h3>
                        <div class="sana-cert-mock__line"></div>
                        <p class="sana-cert-mock__to">يُمنَح هذه الشهادة لـ</p>
                        <p class="sana-cert-mock__name">الطالب المتميّز</p>
                        <p class="sana-cert-mock__course">لإتمام المسار التعليمي بنجاح</p>
                        <div class="sana-cert-mock__footer">
                            <span><i class="fas fa-qrcode"></i> قابلة للتحقق</span>
                            <span><i class="fas fa-shield-check"></i> معتمدة</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php /**PATH C:\xampp\htdocs\sana\resources\views\landing\sana\sections\certificates.blade.php ENDPATH**/ ?>