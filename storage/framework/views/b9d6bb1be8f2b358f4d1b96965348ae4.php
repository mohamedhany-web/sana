
<script>
(function () {
    if (window.__adminShellNavBound) return;
    window.__adminShellNavBound = true;

    var loaderRoot = null;
    var loaderBar = null;
    var visible = false;
    var navGen = 0;
    var creepTimer = null;

    function els() {
        if (!loaderRoot) loaderRoot = document.getElementById('admin-nav-loader');
        if (!loaderBar) loaderBar = document.getElementById('admin-nav-loader-bar');
        return loaderRoot && loaderBar;
    }

    function setPending(active) {
        document.documentElement.classList.toggle('admin-nav-pending', active);
    }

    function clearCreep() {
        if (creepTimer) {
            window.clearTimeout(creepTimer);
            creepTimer = null;
        }
    }

    function showLoader() {
        if (!els()) return;
        if (visible) return;

        visible = true;
        navGen += 1;
        var gen = navGen;
        clearCreep();
        setPending(true);

        loaderBar.style.transition = 'none';
        loaderBar.style.transform = 'scaleX(0.1)';
        loaderRoot.classList.add('is-visible');

        requestAnimationFrame(function () {
            if (gen !== navGen || !visible) return;
            loaderBar.style.transition = 'transform 0.35s ease-out';
            loaderBar.style.transform = 'scaleX(0.55)';
        });

        creepTimer = window.setTimeout(function () {
            if (gen !== navGen || !visible) return;
            loaderBar.style.transition = 'transform 12s ease-out';
            loaderBar.style.transform = 'scaleX(0.88)';
        }, 380);
    }

    function hideLoader() {
        if (!els()) return;

        navGen += 1;
        var gen = navGen;
        clearCreep();

        if (!visible) {
            setPending(false);
            loaderRoot.classList.remove('is-visible');
            loaderBar.style.transition = 'none';
            loaderBar.style.transform = 'scaleX(0)';
            return;
        }

        loaderBar.style.transition = 'transform 0.22s ease-in';
        loaderBar.style.transform = 'scaleX(1)';

        window.setTimeout(function () {
            if (gen !== navGen) return;
            loaderRoot.classList.remove('is-visible');
            loaderBar.style.transition = 'none';
            loaderBar.style.transform = 'scaleX(0)';
            visible = false;
            setPending(false);
        }, 220);
    }

    function shouldNavigateLink(anchor) {
        if (!anchor || anchor.tagName !== 'A') return false;
        if (anchor.hasAttribute('download')) return false;
        if (anchor.dataset && (anchor.dataset.turbo === 'false' || anchor.dataset.noNavLoader === 'true')) return false;
        if (anchor.target && anchor.target !== '_self') return false;
        var href = anchor.getAttribute('href');
        if (!href || href === '#' || href.indexOf('javascript:') === 0) return false;
        try {
            var url = new URL(anchor.href, window.location.origin);
            if (url.origin !== window.location.origin) return false;
            if (url.pathname === window.location.pathname && url.search === window.location.search) return false;
        } catch (e) {
            return false;
        }
        return true;
    }

    function shouldNavigateForm(form) {
        if (!form || form.tagName !== 'FORM') return false;
        if (form.dataset && (form.dataset.turbo === 'false' || form.dataset.noNavLoader === 'true')) return false;
        if (form.target && form.target !== '' && form.target !== '_self') return false;
        var method = (form.getAttribute('method') || 'get').toLowerCase();
        return method === 'get' || method === 'post';
    }

    document.addEventListener('click', function (event) {
        var anchor = event.target.closest('a');
        if (!shouldNavigateLink(anchor)) return;
        if (event.defaultPrevented) return;
        if (event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;
        showLoader();
    }, true);

    document.addEventListener('submit', function (event) {
        var form = event.target.closest('form');
        if (!shouldNavigateForm(form)) return;
        if (event.defaultPrevented) return;
        showLoader();
    });

    window.addEventListener('pageshow', function () {
        hideLoader();
    });
})();
</script>
<?php /**PATH C:\xampp\htdocs\sana\resources\views\partials\admin-shell-nav.blade.php ENDPATH**/ ?>