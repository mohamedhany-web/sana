<?php if(!empty($curriculum)): ?>
<div class="sana-cd-curriculum" x-data="{ openModules: <?php echo \Illuminate\Support\Js::from(collect($curriculum)->pluck('id')->map(fn ($id) => (string) $id)->values()->all())->toHtml() ?> }">
    <?php $__currentLoopData = $curriculum; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $module): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php echo $__env->make('landing.sana.partials.course-curriculum-module', ['module' => $module, 'depth' => 0], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php else: ?>
<p class="sana-cd-section__sub">سيتم إضافة محتوى المنهج قريباً.</p>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\sana\resources\views\landing\sana\partials\course-curriculum.blade.php ENDPATH**/ ?>