{{-- الوضع الفاتح فقط — لا يُفعَّل الوضع الداكن --}}
<script>
(function () {
    try { localStorage.setItem('theme', 'light'); } catch (e) {}
    document.documentElement.classList.remove('dark');
    document.documentElement.classList.add('light');
})();
</script>
