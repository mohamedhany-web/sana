<?php
    $waUrl = trim((string) (($publicFooter ?? \App\Services\PublicFooterSettings::payload())['whatsapp_url'] ?? ''));
?>
<?php if($waUrl !== ''): ?>
<a href="<?php echo e(e($waUrl)); ?>"
   target="_blank"
   rel="noopener noreferrer"
   class="sana-wa-fab"
   aria-label="تواصل عبر واتساب">
    <i class="fab fa-whatsapp"></i>
</a>
<style>
.sana-wa-fab {
    position: fixed;
    z-index: 9998;
    width: 56px;
    height: 56px;
    inset-inline-start: 18px;
    bottom: 18px;
    border-radius: 50%;
    background: #25D366;
    color: #fff !important;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.6rem;
    text-decoration: none !important;
    box-shadow: 0 10px 28px -8px rgba(0,0,0,0.45);
    transition: transform 0.2s, box-shadow 0.2s;
}
.sana-wa-fab:hover { transform: scale(1.06); box-shadow: 0 14px 32px -8px rgba(0,0,0,0.5); }
@media (max-width: 991px) {
    .sana-wa-fab { bottom: calc(18px + env(safe-area-inset-bottom, 0)); }
    .sana-instructor-show-page .sana-wa-fab { bottom: calc(82px + env(safe-area-inset-bottom, 0)); }
}
</style>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\sana\resources\views/partials/whatsapp-fab.blade.php ENDPATH**/ ?>