<?php $photos = ['https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=400&auto=format&fit=crop&q=80']; ?>
<section class="sana-section" id="instructors">
    <div class="sana-container">
        <div class="sana-head sana-reveal">
            <h2 class="sana-head__title">تعرّف على <span class="hl">معلّمينا</span></h2>
            <span class="sana-head__line"></span>
        </div>
        <div class="sana-teachers-m">
            <?php $__empty_1 = true; $__currentLoopData = ($homeInstructors ?? collect())->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php
                $name = $p->user->name ?? 'معلّم';
                $headline = $p->headline ?? 'خبير تعليم';
                $photo = $p->photo_path ? $p->photo_url : $photos[0];
            ?>
            <article class="sana-teacher-m sana-reveal">
                <div class="sana-teacher-m__ring"><img src="<?php echo e($photo); ?>" alt="<?php echo e($name); ?>"></div>
                <h3><?php echo e($name); ?></h3>
                <p class="role"><?php echo e(Str::limit($headline, 28)); ?></p>
                <div class="stars"><?php for($s=0;$s<5;$s++): ?><i class="fas fa-star"></i><?php endfor; ?></div>
                <?php if(is_array($p->social_links ?? null)): ?>
                <div class="social">
                    <?php $__currentLoopData = array_slice($p->social_links, 0, 3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $net => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if(is_string($url) && $url): ?><a href="<?php echo e($url); ?>" target="_blank" rel="noopener"><i class="fab fa-<?php echo e($net === 'linkedin' ? 'linkedin-in' : $net); ?>"></i></a><?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <?php endif; ?>
            </article>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <?php $__currentLoopData = ['سارة محمد','أحمد علي','نور حسن','ليلى كريم','عمر سالم']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $n): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <article class="sana-teacher-m sana-reveal">
                <div class="sana-teacher-m__ring av"><?php echo e(mb_substr($n, 0, 1)); ?></div>
                <h3>أ. <?php echo e($n); ?></h3>
                <p class="role">م educator متخصص</p>
                <div class="stars"><?php for($s=0;$s<5;$s++): ?><i class="fas fa-star"></i><?php endfor; ?></div>
            </article>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php /**PATH C:\xampp\htdocs\sana\resources\views\landing\sana\sections\teachers.blade.php ENDPATH**/ ?>