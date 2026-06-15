<script src="https://cdn.jsdelivr.net/npm/@hotwired/turbo@8.0.12/dist/turbo.es2017-umd.min.js" defer></script>
<script>
(function () {
    if (window.__adminTurboNavBound) return;
    window.__adminTurboNavBound = true;

    var mainSel = '#admin-main-scroll';

    function scrollMainTop() {
        var main = document.querySelector(mainSel);
        if (main) main.scrollTop = 0;
    }

    function shouldSkip(el) {
        if (!el || el.tagName !== 'A') return true;
        if (el.hasAttribute('download')) return true;
        if (el.target && el.target !== '_self') return true;
        if (el.dataset.turbo === 'false') return true;
        var href = el.getAttribute('href');
        if (!href || href === '#' || href.indexOf('javascript:') === 0) return true;
        try {
            var url = new URL(el.href, window.location.origin);
            if (url.origin !== window.location.origin) return true;
        } catch (e) { return true; }
        return false;
    }

    function currentPath() {
        var p = document.body.getAttribute('data-admin-path') || '';
        if (!p) return window.location.pathname;
        return '/' + p.replace(/^\//, '');
    }

    function syncSidebarNav() {
        var sidebar = document.getElementById('admin-sidebar-desktop');
        if (!sidebar) return;
        var path = currentPath();
        var links = sidebar.querySelectorAll('a.sidebar-link, a.sidebar-sub-link');
        links.forEach(function (a) {
            a.classList.remove('active');
            try {
                var u = new URL(a.href, window.location.origin);
                if (u.pathname === path) {
                    a.classList.add('active');
                } else if (u.pathname.length > 8 && path.indexOf(u.pathname) === 0) {
                    a.classList.add('active');
                }
            } catch (e) {}
        });
        links.forEach(function (a) {
            if (!a.classList.contains('active')) return;
            var li = a.closest('li');
            while (li && li !== sidebar) {
                if (li.__x && li.__x.$data && 'open' in li.__x.$data) {
                    li.__x.$data.open = true;
                }
                li = li.parentElement ? li.parentElement.closest('li') : null;
            }
        });
    }

    document.addEventListener('turbo:before-visit', function () {
        document.documentElement.classList.add('admin-turbo-busy');
    });

    function syncSidebarWidthClass() {
        try {
            var collapsed = localStorage.getItem('sidebar_collapsed') === 'true';
            document.documentElement.classList.toggle('admin-sidebar-collapsed', collapsed);
        } catch (e) {}
    }

    document.addEventListener('turbo:load', function () {
        document.documentElement.classList.remove('admin-turbo-busy');
        syncSidebarWidthClass();
        syncSidebarNav();
        scrollMainTop();
        if (window.Alpine) {
            var shell = document.querySelector('.content-wrapper');
            if (shell) {
                try { Alpine.initTree(shell); } catch (e) {}
            }
        }
    });

    document.addEventListener('turbo:render', scrollMainTop);

    document.addEventListener('click', function (e) {
        var a = e.target.closest('a');
        if (shouldSkip(a)) return;
        if (a && a.closest('#admin-sidebar-desktop') && window.innerWidth < 1024) {
            window.dispatchEvent(new CustomEvent('close-sidebar'));
        }
    }, true);

    document.addEventListener('submit', function (e) {
        var form = e.target;
        if (!form || form.tagName !== 'FORM') return;
        if (form.method && form.method.toLowerCase() !== 'get' && !form.hasAttribute('data-turbo')) {
            form.setAttribute('data-turbo', 'false');
        }
    }, true);
})();
</script>
<?php /**PATH C:\xampp\htdocs\sana\resources\views/partials/admin-turbo-nav.blade.php ENDPATH**/ ?>