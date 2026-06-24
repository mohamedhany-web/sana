<section class="sana-section sana-section--white sana-promo-videos" id="promo-videos">
    <div class="sana-container">
        <div class="sana-head sana-reveal">
            <h2 class="sana-head__title">فيديوهات <span class="hl">دعائية</span></h2>
            <span class="sana-head__line"></span>
            <p class="sana-head__sub">شاهد أحدث محتوياتنا التعريفية والترويجية</p>
        </div>

        <div class="sana-promo-videos__shell sana-reveal">
            <?php if($promotionalVideos->count() > 1): ?>
            <button type="button" class="sana-promo-videos__nav sana-promo-videos__nav--prev" aria-label="الفيديو السابق" data-promo-scroll="prev">
                <i class="fas fa-chevron-right"></i>
            </button>
            <?php endif; ?>

            <div class="sana-promo-videos__viewport">
                <div class="sana-promo-videos__track<?php echo e($promotionalVideos->count() === 1 ? ' sana-promo-videos__track--single' : ''); ?>" id="sana-promo-videos-track">
                    <?php $__currentLoopData = $promotionalVideos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $video): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $embed = $video->embedUrl(); ?>
                        <?php if($embed): ?>
                        <article class="sana-promo-videos__card">
                            <div class="sana-promo-videos__frame">
                                <iframe
                                    src="<?php echo e($embed); ?>"
                                    title="<?php echo e($video->title); ?>"
                                    loading="lazy"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                    allowfullscreen
                                ></iframe>
                            </div>
                            <div class="sana-promo-videos__meta">
                                <h3 class="sana-promo-videos__title"><?php echo e($video->title); ?></h3>
                                <?php if(filled($video->description)): ?>
                                    <p class="sana-promo-videos__desc"><?php echo e($video->description); ?></p>
                                <?php endif; ?>
                            </div>
                        </article>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>

            <?php if($promotionalVideos->count() > 1): ?>
            <button type="button" class="sana-promo-videos__nav sana-promo-videos__nav--next" aria-label="الفيديو التالي" data-promo-scroll="next">
                <i class="fas fa-chevron-left"></i>
            </button>
            <?php endif; ?>
        </div>

        <?php if($promotionalVideos->count() > 1): ?>
            <p class="sana-promo-videos__hint sana-reveal">
                <i class="fas fa-arrows-alt-h"></i>
                اسحب جانبياً لاستكشاف المزيد من الفيديوهات
            </p>
        <?php endif; ?>
    </div>
</section>

<script>
(function () {
    var track = document.getElementById('sana-promo-videos-track');
    if (!track) return;

    var step = function () {
        var card = track.querySelector('.sana-promo-videos__card');
        if (!card) return 320;
        var style = window.getComputedStyle(track);
        var gap = parseFloat(style.columnGap || style.gap || '16') || 16;
        return card.offsetWidth + gap;
    };

    document.querySelectorAll('[data-promo-scroll]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var rtl = document.documentElement.dir === 'rtl';
            var dir = btn.getAttribute('data-promo-scroll') === 'next' ? 1 : -1;
            if (rtl) dir *= -1;
            track.scrollBy({ left: dir * step(), behavior: 'smooth' });
        });
    });
})();
</script>
<?php /**PATH C:\xampp\htdocs\sana\resources\views\landing\sana\sections\promotional-videos.blade.php ENDPATH**/ ?>