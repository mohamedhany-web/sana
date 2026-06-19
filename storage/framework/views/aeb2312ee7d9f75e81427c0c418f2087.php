<?php
    $features = [
        ['emoji' => '🗺️', 'bg' => '#EDE9FE', 'title' => 'تعلّم تفاعلي', 'desc' => 'دروس gamified تجعل التعلّم مغامرة ممتعة مع نقاط ومكافآت.'],
        ['emoji' => '🎧', 'bg' => '#DBEAFE', 'title' => 'دعم مباشر', 'desc' => 'تواصل مع المعلّمين واحصل على إجابات فورية خلال الحصص.'],
        ['emoji' => '📜', 'bg' => '#FEF3C7', 'title' => 'شهادات إتمام رقمية', 'desc' => 'شهادات قابلة للتحقق تُصدَر تلقائياً بعد إتمام المسارات.'],
        ['emoji' => '📊', 'bg' => '#D1FAE5', 'title' => 'لوحة متابعة', 'desc' => 'تتبّع تقدّمك وإنجازاتك لحظة بلحظة.'],
        ['emoji' => '📈', 'bg' => '#FCE7F3', 'title' => 'تقارير ذكية', 'desc' => 'تحليلات تفصيلية لنقاط القوة والتحسين.'],
        ['emoji' => '🎬', 'bg' => '#E0E7FF', 'title' => 'دروس مسجّلة', 'desc' => 'محتوى عالي الجودة متاح للمشاهدة في أي وقت.'],
    ];
?>
<section class="sana-section sana-section--white" id="features">
    <div class="sana-container">
        <div class="sana-head sana-reveal">
            <h2 class="sana-head__title">لماذا تختار <span class="hl">منصتنا؟</span></h2>
            <span class="sana-head__line"></span>
        </div>
        <div class="sana-features-m">
            <?php $__currentLoopData = $features; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <article class="sana-feature-m sana-reveal">
                <div class="sana-feature-m__icon" style="background:<?php echo e($f['bg']); ?>"><?php echo e($f['emoji']); ?></div>
                <h3><?php echo e($f['title']); ?></h3>
                <p><?php echo e($f['desc']); ?></p>
            </article>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>
<?php /**PATH C:\xampp\htdocs\sana\resources\views/landing/sana/sections/features.blade.php ENDPATH**/ ?>