<section class="sana-section" id="testimonials">
    <div class="sana-container">
        <div class="sana-head sana-reveal">
            <h2 class="sana-head__title">ماذا يقول <span class="hl">طلابنا وأولياء الأمور؟</span></h2>
            <span class="sana-head__line"></span>
        </div>
        <div class="sana-test-m">
            <?php $__empty_1 = true; $__currentLoopData = ($homeTestimonials ?? collect())->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <article class="sana-test-m__card sana-reveal">
                <div class="quote"><i class="fas fa-quote-right"></i></div>
                <p>«<?php echo e(Str::limit(strip_tags($t->body ?? ''), 160)); ?>»</p>
                <div class="stars"><?php for($s=0;$s<5;$s++): ?><i class="fas fa-star"></i><?php endfor; ?></div>
                <div class="author">
                    <?php if($t->isImageType() && $t->publicImageUrl()): ?>
                        <img src="<?php echo e($t->publicImageUrl()); ?>" alt="">
                    <?php else: ?>
                        <span class="av"><?php echo e($t->author_name ? mb_substr($t->author_name, 0, 1) : '؟'); ?></span>
                    <?php endif; ?>
                    <div><strong><?php echo e($t->author_name ?? 'عميل'); ?></strong><?php if($t->role_label): ?><small><?php echo e($t->role_label); ?></small><?php endif; ?></div>
                </div>
            </article>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <?php $__currentLoopData = [
                ['n'=>'أم ليان','r'=>'وليّة أمر','t'=>'ابنتي أصبحت تحب الرياضيات بعد سنا! التصميم الجميل غيّر كل شيء.'],
                ['n'=>'محمد','r'=>'طالب 12 سنة','t'=>'أحب النقاط والمستويات! أتعلّم وألعب في نفس الوقت.'],
                ['n'=>'أ. خالد','r'=>'وليّ أمر','t'=>'متابعة التقدّم واضحة والمعلّمون محترفون. أنصح بها بشدة.'],
            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <article class="sana-test-m__card sana-reveal">
                <div class="quote"><i class="fas fa-quote-right"></i></div>
                <p>«<?php echo e($d['t']); ?>»</p>
                <div class="stars"><?php for($s=0;$s<5;$s++): ?><i class="fas fa-star"></i><?php endfor; ?></div>
                <div class="author"><span class="av"><?php echo e(mb_substr($d['n'],0,1)); ?></span><div><strong><?php echo e($d['n']); ?></strong><small><?php echo e($d['r']); ?></small></div></div>
            </article>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php /**PATH C:\xampp\htdocs\sana\resources\views\landing\sana\sections\testimonials.blade.php ENDPATH**/ ?>