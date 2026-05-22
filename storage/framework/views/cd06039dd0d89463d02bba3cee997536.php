<?php
    $brand = config('app.name');
    $tr = fn (string $key) => str_replace(':brand', $brand, __('sana_home.'.$key));
?>
<footer class="sana-footer relative pt-16 pb-8 border-t border-slate-200 bg-white">
    <div class="max-w-[1280px] mx-auto px-5 sm:px-8 lg:px-10">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-10 mb-12">
            <div class="lg:col-span-4">
                <a href="<?php echo e(route('home')); ?>" class="inline-flex items-center gap-2 mb-3 font-extrabold text-lg text-slate-800"><?php echo e($brand); ?></a>
                <p class="text-slate-500 text-sm leading-7 max-w-sm"><?php echo e($tr('footer.desc')); ?></p>
            </div>
            <div class="lg:col-span-2">
                <h4 class="font-bold text-slate-800 mb-3 text-sm"><?php echo e($tr('footer.platform')); ?></h4>
                <ul class="space-y-2 text-sm text-slate-500">
                    <li><a href="<?php echo e(route('public.courses')); ?>" class="hover:text-indigo-600"><?php echo e($tr('nav.courses')); ?></a></li>
                    <li><a href="<?php echo e(route('public.instructors.index')); ?>" class="hover:text-indigo-600"><?php echo e($tr('nav.instructors')); ?></a></li>
                    <li><a href="<?php echo e(route('public.pricing')); ?>" class="hover:text-indigo-600"><?php echo e(__('landing.nav.pricing')); ?></a></li>
                </ul>
            </div>
            <div class="lg:col-span-2">
                <h4 class="font-bold text-slate-800 mb-3 text-sm"><?php echo e($tr('footer.categories')); ?></h4>
                <ul class="space-y-2 text-sm text-slate-500">
                    <?php $__empty_1 = true; $__currentLoopData = ($courseCategories ?? collect())->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <li><a href="<?php echo e($cat['url']); ?>" class="hover:text-indigo-600"><?php echo e($cat['name']); ?></a></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <li><a href="<?php echo e(route('public.courses')); ?>" class="hover:text-indigo-600"><?php echo e($tr('nav.courses')); ?></a></li>
                    <?php endif; ?>
                </ul>
            </div>
            <div class="lg:col-span-2">
                <h4 class="font-bold text-slate-800 mb-3 text-sm"><?php echo e($tr('footer.support')); ?></h4>
                <ul class="space-y-2 text-sm text-slate-500">
                    <li><a href="<?php echo e(route('public.privacy')); ?>" class="hover:text-indigo-600"><?php echo e(__('public.privacy_policy')); ?></a></li>
                    <li><a href="<?php echo e(route('public.faq')); ?>" class="hover:text-indigo-600"><?php echo e(__('public.faq_page_title', [], 'الأسئلة الشائعة')); ?></a></li>
                    <li><a href="<?php echo e(route('public.testimonials')); ?>" class="hover:text-indigo-600"><?php echo e($tr('testimonials.view_all')); ?></a></li>
                </ul>
            </div>
            <div class="lg:col-span-2">
                <h4 class="font-bold text-slate-800 mb-2 text-sm"><?php echo e($tr('footer.newsletter_title')); ?></h4>
                <p class="text-xs text-slate-500 mb-2"><?php echo e($tr('footer.newsletter_desc')); ?></p>
                <form class="flex flex-col gap-2" onsubmit="return false;">
                    <input type="email" class="sana-input text-sm" placeholder="<?php echo e($tr('footer.newsletter_placeholder')); ?>">
                    <button type="button" class="sana-btn-primary w-full justify-center text-sm py-2"><?php echo e($tr('footer.newsletter_btn')); ?></button>
                </form>
            </div>
        </div>
        <p class="text-center text-xs text-slate-400 pt-6 border-t border-slate-100">&copy; <?php echo e(date('Y')); ?> <?php echo e($brand); ?>. <?php echo e($tr('footer.copyright')); ?></p>
    </div>
</footer>
<?php /**PATH C:\xampp\htdocs\sana\resources\views\landing\sana\footer.blade.php ENDPATH**/ ?>