
<?php
    /** @var \App\Models\SiteTestimonial $t */
    $fluid = $fluid ?? false;
    $widthClass = $fluid
        ? 'w-full'
        : 'min-w-[min(88vw,300px)] max-w-[min(88vw,300px)] sm:min-w-[320px] sm:max-w-[320px]';
    $cardClass = 'rounded-2xl bg-white border border-slate-200 shadow-[0_4px_24px_-8px_rgba(15,23,42,.08)] transition-shadow hover:shadow-[0_12px_40px_-12px_rgba(15,23,42,.12)]';
?>
<article class="<?php echo e($widthClass); ?> <?php echo e($fluid ? '' : 'shrink-0'); ?> <?php echo e($cardClass); ?> p-6 sm:p-8 flex flex-col h-full">
    <?php if($t->isImageType() && $t->publicImageUrl()): ?>
        <div class="w-full aspect-[4/3] max-h-[14rem] rounded-xl overflow-hidden bg-slate-50 mb-5 flex items-center justify-center">
            <img src="<?php echo e($t->publicImageUrl()); ?>" alt="" class="max-h-full max-w-full object-contain" loading="lazy" decoding="async">
        </div>
    <?php else: ?>
        <span class="w-11 h-11 rounded-xl bg-blue-50 text-[var(--edu-primary)] flex items-center justify-center text-lg mb-5 shrink-0">
            <i class="fas fa-quote-right"></i>
        </span>
    <?php endif; ?>
    <div class="flex flex-col flex-1">
        <?php if($t->body): ?>
            <p class="text-sm leading-8 flex-1 text-slate-600">
                <?php if($t->isImageType()): ?>
                    <?php echo e(Str::limit(strip_tags($t->body), 160)); ?>

                <?php else: ?>
                    «<?php echo e(Str::limit(strip_tags($t->body), 260)); ?>»
                <?php endif; ?>
            </p>
        <?php endif; ?>
        <div class="mt-5 pt-4 border-t border-slate-100">
            <?php if($t->author_name): ?>
                <p class="font-bold text-sm text-slate-900"><?php echo e($t->author_name); ?></p>
            <?php endif; ?>
            <?php if($t->role_label): ?>
                <p class="text-xs mt-1 text-slate-500"><?php echo e($t->role_label); ?></p>
            <?php endif; ?>
            <div class="flex gap-0.5 text-amber-500 text-xs mt-2">
                <?php for($s = 0; $s < 5; $s++): ?><i class="fas fa-star"></i><?php endfor; ?>
            </div>
        </div>
    </div>
</article>
<?php /**PATH C:\xampp\htdocs\sana\resources\views\partials\home-testimonial-card.blade.php ENDPATH**/ ?>