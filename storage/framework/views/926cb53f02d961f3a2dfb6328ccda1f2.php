<?php
    $isMobile = !empty($mobile);
?>
<div class="edu-filter-panel space-y-5">
    <?php if (! ($isMobile)): ?>
    <div>
        <label class="text-xs font-bold text-slate-500 mb-2 block"><?php echo e(__('public.search_course_placeholder')); ?></label>
        <div class="relative">
            <input type="search" x-model="searchQuery" class="edu-filter-search" placeholder="<?php echo e(__('public.search_course_placeholder')); ?>">
            <i class="fas fa-search absolute top-1/2 -translate-y-1/2 start-3 text-slate-400 text-sm pointer-events-none"></i>
        </div>
    </div>
    <?php endif; ?>

    <div>
        <p class="text-xs font-bold text-slate-500 mb-2"><?php echo e($tr('categories.badge')); ?></p>
        <div class="edu-cat-filter-scroll space-y-1">
            <button type="button" @click="selectedCategoryId = ''"
                class="edu-cat-filter" :class="selectedCategoryId === '' && 'is-active'">
                <span><?php echo e($tr('courses.tab_all')); ?></span>
                <span class="count" x-text="countForCategory('')"></span>
            </button>
            <template x-for="cat in categories" :key="cat.id">
                <button type="button" @click="selectedCategoryId = String(cat.id)"
                    class="edu-cat-filter" :class="selectedCategoryId === String(cat.id) && 'is-active'">
                    <span x-text="cat.name"></span>
                    <span class="count" x-text="countForCategory(cat.id)"></span>
                </button>
            </template>
        </div>
    </div>

    <div class="pt-3 border-t border-slate-100 space-y-1">
        <label class="edu-check-row">
            <input type="checkbox" x-model="showFreeOnly">
            <span><?php echo e(__('public.free_price')); ?> فقط</span>
        </label>
        <label class="edu-check-row">
            <input type="checkbox" x-model="showFeaturedOnly">
            <span><?php echo e(__('public.featured_badge')); ?> فقط</span>
        </label>
    </div>

    <button type="button" @click="resetFilters(); filtersOpen = false" class="edu-btn-outline w-full justify-center text-sm py-2.5">
        <i class="fas fa-rotate-left text-xs"></i> إعادة تعيين
    </button>
</div>
<?php /**PATH C:\xampp\htdocs\sana\resources\views/landing/eduvalt/partials/courses-filter-panel.blade.php ENDPATH**/ ?>