<?php
    $ad = $ad ?? null;
    $brand = config('app.name', 'Sana');
    $imageUrl = ($ad && $ad->image) ? \Illuminate\Support\Facades\Storage::disk('public')->url($ad->image) : null;
?>
<?php if($ad): ?>
<div id="popup-ad-overlay" class="sana-popup-ad" role="dialog" aria-modal="true" aria-labelledby="popup-ad-title">
    <div class="sana-popup-ad__backdrop" data-popup-close></div>

    <div id="popup-ad-box" class="sana-popup-ad__card">
        <button type="button" id="popup-ad-close" class="sana-popup-ad__close" aria-label="إغلاق الإعلان">
            <i class="fas fa-times"></i>
        </button>

        <?php if($imageUrl): ?>
            <div class="sana-popup-ad__media">
                <img src="<?php echo e($imageUrl); ?>" alt="<?php echo e($ad->title); ?>" loading="lazy">
            </div>
        <?php else: ?>
            <div class="sana-popup-ad__hero" aria-hidden="true">
                <span class="sana-popup-ad__hero-icon"><i class="fas fa-bullhorn"></i></span>
            </div>
        <?php endif; ?>

        <div class="sana-popup-ad__body">
            <span class="sana-popup-ad__badge">
                <i class="fas fa-sparkles"></i>
                عرض من <?php echo e($brand); ?>

            </span>

            <h2 id="popup-ad-title" class="sana-popup-ad__title"><?php echo e($ad->title); ?></h2>

            <?php if(filled($ad->body)): ?>
                <div class="sana-popup-ad__text"><?php echo nl2br(e($ad->body)); ?></div>
            <?php endif; ?>

            <div class="sana-popup-ad__actions">
                <?php if($ad->cta_text && $ad->link_url): ?>
                    <?php
                        $isExternal = str_starts_with($ad->link_url, 'http://') || str_starts_with($ad->link_url, 'https://');
                    ?>
                    <a href="<?php echo e($ad->link_url); ?>"
                       class="sana-btn sana-btn--yellow sana-popup-ad__cta"
                       <?php if($isExternal): ?> target="_blank" rel="noopener" <?php endif; ?>>
                        <?php echo e($ad->cta_text); ?>

                        <i class="fas fa-arrow-left"></i>
                    </a>
                <?php elseif($ad->cta_text): ?>
                    <span class="sana-popup-ad__cta-label"><?php echo e($ad->cta_text); ?></span>
                <?php endif; ?>
                <button type="button" class="sana-popup-ad__dismiss" data-popup-close>إغلاق</button>
            </div>
        </div>
    </div>
</div>

<style>
.sana-popup-ad {
    --popup-purple: #6D28D9;
    --popup-purple-dark: #5B21B6;
    --popup-gold: #FBBF24;
    position: fixed;
    inset: 0;
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: clamp(12px, 4vw, 24px);
    animation: sanaPopupFadeIn 0.35s ease-out;
}
.sana-popup-ad__backdrop {
    position: absolute;
    inset: 0;
    background: rgba(15, 23, 42, 0.62);
    backdrop-filter: blur(6px);
}
.sana-popup-ad__card {
    position: relative;
    z-index: 1;
    width: min(100%, 440px);
    max-height: min(92vh, 720px);
    overflow: hidden auto;
    border-radius: 24px;
    background: #fff;
    border: 1px solid #EDE9FE;
    box-shadow:
        0 0 0 1px rgba(255, 255, 255, 0.6) inset,
        0 28px 64px -24px rgba(91, 33, 182, 0.45);
    animation: sanaPopupSlideUp 0.45s cubic-bezier(0.22, 1, 0.36, 1);
}
.sana-popup-ad__close {
    position: absolute;
    top: 14px;
    inset-inline-end: 14px;
    z-index: 3;
    width: 38px;
    height: 38px;
    border: none;
    border-radius: 12px;
    background: rgba(255, 255, 255, 0.92);
    color: #64748b;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: background 0.2s, color 0.2s, transform 0.2s;
    box-shadow: 0 4px 14px rgba(15, 23, 42, 0.12);
}
.sana-popup-ad__close:hover {
    background: #fff;
    color: var(--popup-purple-dark);
    transform: scale(1.05);
}
.sana-popup-ad__hero {
    position: relative;
    padding: 28px 24px 22px;
    background:
        radial-gradient(circle at 88% 18%, rgba(251, 191, 36, 0.28) 0%, transparent 42%),
        linear-gradient(145deg, #4C1D95 0%, #6D28D9 52%, #7C3AED 100%);
    text-align: center;
}
.sana-popup-ad__hero-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 64px;
    height: 64px;
    border-radius: 20px;
    background: rgba(255, 255, 255, 0.16);
    border: 1px solid rgba(255, 255, 255, 0.28);
    color: #fff;
    font-size: 1.5rem;
}
.sana-popup-ad__media img {
    display: block;
    width: 100%;
    max-height: 220px;
    object-fit: cover;
}
.sana-popup-ad__body {
    padding: 22px 24px 24px;
    text-align: center;
}
.sana-popup-ad__badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 12px;
    padding: 6px 12px;
    border-radius: 999px;
    background: #F5F3FF;
    border: 1px solid #DDD6FE;
    color: var(--popup-purple-dark);
    font-size: 0.72rem;
    font-weight: 800;
}
.sana-popup-ad__title {
    margin: 0 0 12px;
    font-family: 'Cairo', 'Tajawal', sans-serif;
    font-size: clamp(1.25rem, 4vw, 1.55rem);
    font-weight: 900;
    line-height: 1.35;
    color: #1e1b4b;
}
.sana-popup-ad__text {
    margin: 0 0 20px;
    font-size: 0.92rem;
    font-weight: 600;
    line-height: 1.75;
    color: #64748b;
    text-align: center;
}
.sana-popup-ad__actions {
    display: flex;
    flex-direction: column;
    align-items: stretch;
    gap: 10px;
}
.sana-popup-ad__cta {
    width: 100%;
    padding: 14px 20px !important;
    font-size: 0.92rem !important;
    border-radius: 14px !important;
}
.sana-popup-ad__cta-label {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 12px 18px;
    border-radius: 14px;
    background: #F5F3FF;
    border: 1px solid #DDD6FE;
    color: var(--popup-purple-dark);
    font-size: 0.88rem;
    font-weight: 800;
}
.sana-popup-ad__dismiss {
    padding: 10px 16px;
    border: none;
    background: transparent;
    color: #94a3b8;
    font-size: 0.82rem;
    font-weight: 700;
    cursor: pointer;
    transition: color 0.15s;
}
.sana-popup-ad__dismiss:hover { color: #64748b; }

@keyframes sanaPopupFadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}
@keyframes sanaPopupSlideUp {
    from { opacity: 0; transform: translateY(18px) scale(0.97); }
    to { opacity: 1; transform: translateY(0) scale(1); }
}

@media (max-width: 479px) {
    .sana-popup-ad__body { padding: 18px 18px 20px; }
    .sana-popup-ad__close { top: 10px; inset-inline-end: 10px; }
}
</style>

<script>
(function () {
    var overlay = document.getElementById('popup-ad-overlay');
    if (!overlay) return;

    document.documentElement.classList.add('sana-popup-ad-open');

    function hide() {
        overlay.style.opacity = '0';
        overlay.style.pointerEvents = 'none';
        document.documentElement.classList.remove('sana-popup-ad-open');
        setTimeout(function () { overlay.remove(); }, 320);
    }

    overlay.querySelectorAll('[data-popup-close]').forEach(function (el) {
        el.addEventListener('click', hide);
    });

    var closeBtn = document.getElementById('popup-ad-close');
    if (closeBtn) closeBtn.addEventListener('click', hide);

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') hide();
    });
})();
</script>

<style>
html.sana-popup-ad-open { overflow: hidden; }
</style>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\sana\resources\views\partials\popup-ad.blade.php ENDPATH**/ ?>