<?php
    $moduleId = is_string($module['id'] ?? null) ? $module['id'] : (string) ($module['id'] ?? uniqid());
    $itemCount = count($module['items'] ?? []);
    $padding = ($depth ?? 0) > 0 ? 'margin-inline-start:16px' : '';
?>
<div class="sana-cd-module" style="<?php echo e($padding); ?>"
     :class="openModules.includes('<?php echo e($moduleId); ?>') && 'is-open'">
    <button type="button" class="sana-cd-module__toggle"
            @click="openModules.includes('<?php echo e($moduleId); ?>') ? openModules = openModules.filter(id => id !== '<?php echo e($moduleId); ?>') : openModules.push('<?php echo e($moduleId); ?>')">
        <div>
            <strong><?php echo e($module['title'] ?? 'وحدة'); ?></strong>
            <?php if(!empty($module['description'])): ?>
                <span style="display:block;font-size:0.75rem;color:var(--muted);font-weight:600;margin-top:4px"><?php echo e(Str::limit($module['description'], 80)); ?></span>
            <?php endif; ?>
        </div>
        <span>
            <?php echo e($itemCount); ?> عنصر
            <i class="fas fa-chevron-down chevron"></i>
        </span>
    </button>
    <div class="sana-cd-module__body">
        <?php $__currentLoopData = $module['items'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="sana-cd-lesson">
                <span class="sana-cd-lesson__icon"><i class="fas <?php echo e($item['icon'] ?? 'fa-play-circle'); ?>"></i></span>
                <span><?php echo e($item['title'] ?? ''); ?></span>
                <span class="sana-cd-lesson__meta"><?php echo e($item['type_label'] ?? ''); ?><?php if(!empty($item['duration'])): ?> · <?php echo e($item['duration']); ?> د<?php endif; ?></span>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php $__currentLoopData = $module['children'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php echo $__env->make('landing.sana.partials.course-curriculum-module', ['module' => $child, 'depth' => ($depth ?? 0) + 1], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\sana\resources\views\landing\sana\partials\course-curriculum-module.blade.php ENDPATH**/ ?>