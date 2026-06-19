<?php
    $cats = [];
    if (isset($homeSubjects) && $homeSubjects->isNotEmpty()) {
        $palette = [
            ['emoji' => '🔢', 'bg' => 'linear-gradient(145deg,#EDE9FE,#C4B5FD)'],
            ['emoji' => '🔬', 'bg' => 'linear-gradient(145deg,#D1FAE5,#6EE7B7)'],
            ['emoji' => '💻', 'bg' => 'linear-gradient(145deg,#DBEAFE,#93C5FD)'],
            ['emoji' => '🌍', 'bg' => 'linear-gradient(145deg,#FFEDD5,#FDBA74)'],
            ['emoji' => '🤖', 'bg' => 'linear-gradient(145deg,#F3E8FF,#D8B4FE)'],
            ['emoji' => '🦾', 'bg' => 'linear-gradient(145deg,#CCFBF1,#5EEAD4)'],
            ['emoji' => '📚', 'bg' => 'linear-gradient(145deg,#FCE7F3,#F9A8D4)'],
        ];
        $cats = $homeSubjects->take(7)->values()->map(function ($sub, $i) use ($palette) {
            $p = $palette[$i % count($palette)];

            return [
                'name' => $sub['name'],
                'emoji' => $p['emoji'],
                'bg' => $p['bg'],
                'url' => $sub['url'],
            ];
        })->all();
    }
?>
<?php if(count($cats) > 0): ?>
<section class="sana-section" id="categories">
    <div class="sana-container">
        <div class="sana-head-row sana-reveal">
            <div class="sana-head">
                <h2 class="sana-head__title">تصفّح <span class="hl">التصنيفات</span></h2>
                <span class="sana-head__line"></span>
            </div>
            <a href="<?php echo e(route('public.courses')); ?>" class="sana-link-more">عرض الكل <i class="fas fa-arrow-left"></i></a>
        </div>
        <div class="sana-cats-row sana-reveal">
            <?php $__currentLoopData = $cats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e($cat['url'] ?? route('public.courses')); ?>" class="sana-cat-m" style="background:<?php echo e($cat['bg']); ?>">
                <span class="sana-cat-m__emoji"><?php echo e($cat['emoji']); ?></span>
                <span class="sana-cat-m__name"><?php echo e($cat['name']); ?></span>
            </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\sana\resources\views/landing/sana/sections/categories.blade.php ENDPATH**/ ?>