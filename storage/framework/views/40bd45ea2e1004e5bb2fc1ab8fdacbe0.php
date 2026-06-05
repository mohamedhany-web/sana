<?php
    $tr = $tr ?? fn (string $key) => str_replace(':brand', config('app.name'), __('sana_home.'.$key));
    $hs = $homeStats ?? [];
?>
<section class="home-discover" aria-labelledby="home-discover-title">
    <div class="home-geo-bg" aria-hidden="true">
        <span class="home-geo-shape home-geo-shape--1"></span>
        <span class="home-geo-shape home-geo-shape--2"></span>
    </div>
    <div class="edu-container">
        <div class="home-discover__inner reveal">
            <p class="home-discover__eyebrow">
                <i class="fas fa-sparkles" aria-hidden="true"></i>
                <?php echo e($tr('discover.eyebrow')); ?>

            </p>
            <h1 id="home-discover-title" class="home-discover__title">
                <?php echo e($tr('discover.title')); ?> <em><?php echo e($tr('discover.title_em')); ?></em>
            </h1>
            <p class="home-discover__lead"><?php echo e($tr('discover.lead')); ?></p>

            <form class="home-search-panel" action="<?php echo e(route('public.courses')); ?>" method="get" role="search">
                <div class="home-search-row">
                    <div class="home-search-input-wrap">
                        <i class="fas fa-search" aria-hidden="true"></i>
                        <input type="search" name="search" class="home-search-input"
                               placeholder="<?php echo e($tr('discover.search_placeholder')); ?>"
                               aria-label="<?php echo e($tr('discover.search_placeholder')); ?>"
                               autocomplete="off">
                    </div>
                    <label class="sr-only" for="home-search-category"><?php echo e($tr('discover.category_label')); ?></label>
                    <select id="home-search-category" name="category" class="home-search-select" aria-label="<?php echo e($tr('discover.category_label')); ?>">
                        <option value=""><?php echo e($tr('discover.all_categories')); ?></option>
                        <?php $__currentLoopData = ($courseCategories ?? collect()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($cat['id'] ?? ''); ?>"><?php echo e($cat['name']); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <button type="submit" class="home-search-btn">
                        <i class="fas fa-magnifying-glass" aria-hidden="true"></i>
                        <?php echo e($tr('discover.search_btn')); ?>

                    </button>
                </div>
                <?php if(($courseCategories ?? collect())->isNotEmpty()): ?>
                <div class="home-search-chips" role="list">
                    <?php $__currentLoopData = ($courseCategories ?? collect())->take(6); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e($cat['url']); ?>" class="home-search-chip" role="listitem"><?php echo e($cat['name']); ?></a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <?php endif; ?>
            </form>

            <div class="home-discover__stats">
                <span class="home-discover__stat"><strong><?php echo e(number_format((int) ($hs['courses'] ?? 0))); ?></strong> <?php echo e($tr('discover.stat_courses')); ?></span>
                <span class="home-discover__stat"><strong><?php echo e(number_format((int) ($hs['instructors'] ?? 0))); ?></strong> <?php echo e($tr('discover.stat_instructors')); ?></span>
                <?php if(($hs['avg_rating'] ?? 0) > 0): ?>
                <span class="home-discover__stat"><strong><?php echo e($hs['avg_rating']); ?></strong> <?php echo e($tr('discover.stat_rating')); ?></span>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<?php /**PATH C:\xampp\htdocs\sana\resources\views/landing/eduvalt/partials/home-discover.blade.php ENDPATH**/ ?>