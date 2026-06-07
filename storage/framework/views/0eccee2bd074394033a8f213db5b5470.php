<?php if(isset($landingPaths) && $landingPaths->isNotEmpty()): ?>
<section class="py-14 sm:py-20 bg-slate-50" id="paths">
    <div class="max-w-[1280px] mx-auto px-5 sm:px-8 lg:px-10">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-10 reveal">
            <div>
                <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-white text-indigo-700 border border-indigo-100 mb-3"><?php echo e($tr('paths.badge')); ?></span>
                <h2 class="font-display text-2xl sm:text-3xl font-extrabold text-slate-900"><?php echo e($tr('paths.title')); ?></h2>
                <p class="text-slate-500 mt-2 max-w-xl text-sm sm:text-base"><?php echo e($tr('paths.subtitle')); ?></p>
            </div>
            <a href="<?php echo e(route('public.courses')); ?>" class="sana-btn-ghost shrink-0 bg-white"><?php echo e($tr('paths.view_all')); ?></a>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
            <?php $__currentLoopData = $landingPaths->take(6); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $path): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $pathFree = (float)($path->price ?? 0) <= 0;
                $pathUrl = route('public.learning-path.show', $path->slug);
            ?>
            <a href="<?php echo e($pathUrl); ?>" class="sana-card reveal overflow-hidden flex flex-col group !p-0">
                <div class="aspect-[16/9] bg-slate-100 relative overflow-hidden">
                    <?php if($path->image_url ?? null): ?>
                        <img src="<?php echo e($path->image_url); ?>" alt="<?php echo e($path->name); ?>" class="w-full h-full object-cover group-hover:scale-[1.03] transition-transform duration-500" loading="lazy">
                    <?php else: ?>
                        <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-indigo-50 to-cyan-50 text-indigo-300">
                            <i class="fas fa-route text-4xl"></i>
                        </div>
                    <?php endif; ?>
                    <?php if($path->courses_count > 0): ?>
                        <span class="absolute top-3 start-3 px-2.5 py-1 rounded-lg text-[10px] font-bold bg-white/95 text-slate-700 shadow-sm">
                            <?php echo e($path->courses_count); ?> <?php echo e($tr('paths.courses_in_path')); ?>

                        </span>
                    <?php endif; ?>
                </div>
                <div class="p-5 flex flex-col flex-1">
                    <h3 class="font-display font-bold text-slate-900 mb-2 line-clamp-2 group-hover:text-indigo-600 transition-colors"><?php echo e($path->name); ?></h3>
                    <?php if(!empty($path->description)): ?>
                        <p class="text-xs text-slate-500 line-clamp-2 mb-4 flex-1"><?php echo e(Str::limit(strip_tags($path->description), 100)); ?></p>
                    <?php endif; ?>
                    <div class="flex items-center justify-between mt-auto pt-2 border-t border-slate-100">
                        <?php if($pathFree): ?>
                            <span class="font-bold text-emerald-600 text-sm"><?php echo e($tr('paths.free')); ?></span>
                        <?php else: ?>
                            <span class="font-bold text-indigo-600 text-sm tabular-nums"><?php echo e(number_format($path->price, 0)); ?> <?php echo e(__('public.currency')); ?></span>
                        <?php endif; ?>
                        <span class="text-xs font-bold text-indigo-600"><?php echo e($tr('paths.view_path')); ?> <i class="fas fa-arrow-left text-[10px]"></i></span>
                    </div>
                </div>
            </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\sana\resources\views\landing\sana\paths.blade.php ENDPATH**/ ?>