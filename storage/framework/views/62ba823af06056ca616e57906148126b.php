<?php
    $brand = config('app.name');
    $tr = fn (string $key) => str_replace(':brand', $brand, __('sana_home.'.$key));
?>
<footer class="bg-[#0F172A] text-slate-300 pt-16 pb-8">
    <div class="edu-container">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-10 mb-12">
            <div class="lg:col-span-4">
                <a href="<?php echo e(route('home')); ?>" class="inline-flex items-center gap-2 text-white font-extrabold text-xl mb-4">
                    <span class="w-10 h-10 rounded-xl flex items-center justify-center text-sm" style="background:var(--edu-primary)"><?php echo e(mb_substr($brand, 0, 1)); ?></span>
                    <?php echo e($brand); ?>

                </a>
                <p class="text-sm leading-7 text-slate-400 mb-6 max-w-sm"><?php echo e($tr('footer.desc')); ?></p>
                <div class="flex gap-3">
                    <a href="#" class="w-10 h-10 rounded-lg bg-white/10 flex items-center justify-center hover:bg-[var(--edu-primary)] transition-colors" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="w-10 h-10 rounded-lg bg-white/10 flex items-center justify-center hover:bg-[var(--edu-primary)] transition-colors" aria-label="X"><i class="fab fa-x-twitter"></i></a>
                    <a href="#" class="w-10 h-10 rounded-lg bg-white/10 flex items-center justify-center hover:bg-[var(--edu-primary)] transition-colors" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                    <a href="#" class="w-10 h-10 rounded-lg bg-white/10 flex items-center justify-center hover:bg-[var(--edu-primary)] transition-colors" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
            <div class="lg:col-span-2">
                <h4 class="text-white font-bold mb-4"><?php echo e($tr('footer.quick')); ?></h4>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="<?php echo e(route('home')); ?>" class="hover:text-white transition-colors"><?php echo e($tr('nav.home')); ?></a></li>
                    <li><a href="<?php echo e(route('public.about')); ?>" class="hover:text-white transition-colors"><?php echo e($tr('nav.about')); ?></a></li>
                    <li><a href="<?php echo e(route('public.contact')); ?>" class="hover:text-white transition-colors"><?php echo e($tr('nav.contact')); ?></a></li>
                    <li><a href="<?php echo e(route('public.privacy')); ?>" class="hover:text-white transition-colors"><?php echo e(__('public.privacy_policy')); ?></a></li>
                    <li><a href="<?php echo e(route('public.pricing')); ?>" class="hover:text-white transition-colors"><?php echo e(__('landing.nav.pricing')); ?></a></li>
                </ul>
            </div>
            <div class="lg:col-span-2">
                <h4 class="text-white font-bold mb-4"><?php echo e($tr('footer.courses_col')); ?></h4>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="<?php echo e(route('public.courses')); ?>" class="hover:text-white transition-colors"><?php echo e($tr('nav.courses')); ?></a></li>
                    <li><a href="<?php echo e(route('public.instructors.index')); ?>" class="hover:text-white transition-colors"><?php echo e($tr('nav.instructors')); ?></a></li>
                    <li><a href="<?php echo e(route('public.certificates')); ?>" class="hover:text-white transition-colors"><?php echo e(__('landing.stats.certificates')); ?></a></li>
                </ul>
            </div>
            <div class="lg:col-span-2">
                <h4 class="text-white font-bold mb-4"><?php echo e($tr('footer.hours')); ?></h4>
                <p class="text-sm text-slate-400 leading-7"><?php echo e($tr('footer.hours_val')); ?></p>
            </div>
            <div class="lg:col-span-2">
                <h4 class="text-white font-bold mb-3"><?php echo e($tr('newsletter.title')); ?></h4>
                <p class="text-xs text-slate-500 mb-3"><?php echo e($tr('newsletter.subtitle')); ?></p>
                <form class="flex flex-col gap-2" onsubmit="return false;">
                    <input type="email" class="w-full px-4 py-2.5 rounded-xl bg-white/10 border border-white/10 text-sm text-white placeholder:text-slate-500" placeholder="<?php echo e($tr('newsletter.placeholder')); ?>">
                    <button type="button" class="edu-btn-primary w-full justify-center text-sm py-2.5"><?php echo e($tr('newsletter.btn')); ?></button>
                </form>
            </div>
        </div>
        <div class="pt-8 border-t border-white/10 flex flex-col sm:flex-row items-center justify-between gap-4 text-sm text-slate-500">
            <p>&copy; <?php echo e(date('Y')); ?> <?php echo e($brand); ?>. <?php echo e($tr('footer.copyright')); ?></p>
            <button type="button" onclick="window.scrollTo({top:0,behavior:'smooth'})" class="flex items-center gap-2 hover:text-white transition-colors">
                <i class="fas fa-arrow-up"></i> للأعلى
            </button>
        </div>
    </div>
</footer>
<?php /**PATH C:\xampp\htdocs\sana\resources\views/landing/eduvalt/footer.blade.php ENDPATH**/ ?>