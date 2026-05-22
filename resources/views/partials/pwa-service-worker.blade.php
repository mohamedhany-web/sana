@if(config('app.env') === 'production' && !config('app.debug'))
<script>
(function () {
    if (!('serviceWorker' in navigator)) return;
    window.addEventListener('load', function () {
        navigator.serviceWorker.register('/sw.js?v=4').catch(function () {});
    });
})();
</script>
@else
<script>
(function () {
    if (!('serviceWorker' in navigator)) return;
    navigator.serviceWorker.getRegistrations().then(function (regs) {
        regs.forEach(function (r) { r.unregister(); });
    });
    if ('caches' in window) {
        caches.keys().then(function (keys) {
            keys.forEach(function (k) { caches.delete(k); });
        });
    }
})();
</script>
@endif
