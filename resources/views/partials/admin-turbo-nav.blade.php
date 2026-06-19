<script src="https://cdn.jsdelivr.net/npm/@hotwired/turbo@8.0.12/dist/turbo.es2017-umd.min.js" defer></script>
<script>
(function () {
    if (window.__adminTurboNavBound) return;
    window.__adminTurboNavBound = true;

    var mainSel = '#admin-main-scroll';
    var loaderRoot = null;
    var loaderBar = null;
    var visitToken = 0;
    var trickleTimer = null;
    var hideTimer = null;
    var progress = 0;
    var isBusy = false;

    function loaderElements() {
        if (!loaderRoot) loaderRoot = document.getElementById('admin-nav-loader');
        if (!loaderBar) loaderBar = document.getElementById('admin-nav-loader-bar');
        return loaderRoot && loaderBar;
    }

    function applyProgress(value) {
        if (!loaderElements()) return;
        progress = Math.max(0, Math.min(1, value));
        loaderBar.style.transform = 'scaleX(' + progress + ')';
    }

    function setBusy(state) {
        isBusy = state;
        document.documentElement.classList.toggle('admin-turbo-busy', state);
    }

    function clearTimers() {
        if (trickleTimer) {
            clearInterval(trickleTimer);
            trickleTimer = null;
        }
        if (hideTimer) {
            clearTimeout(hideTimer);
            hideTimer = null;
        }
    }

    function startLoader() {
        if (!loaderElements()) return;
        visitToken += 1;
        clearTimers();
        setBusy(true);
        loaderRoot.classList.add('is-visible');
        progress = 0.06;
        applyProgress(progress);

        trickleTimer = setInterval(function () {
            if (!isBusy) return;
            if (progress < 0.88) {
                var delta = (0.88 - progress) * 0.1;
                applyProgress(progress + Math.max(delta, 0.004));
            }
        }, 140);
    }

    function bumpLoader(target) {
        if (!isBusy) return;
        applyProgress(Math.max(progress, target));
    }

    function finishLoader() {
        if (!loaderElements()) return;
        var token = visitToken;
        clearTimers();
        applyProgress(1);

        hideTimer = setTimeout(function () {
            if (token !== visitToken) return;
            loaderRoot.classList.remove('is-visible');
            setBusy(false);
            applyProgress(0);
        }, 220);
    }

    function cancelLoader() {
        visitToken += 1;
        clearTimers();
        if (loaderElements()) {
            loaderRoot.classList.remove('is-visible');
        }
        setBusy(false);
        applyProgress(0);
    }

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
        if (syncSidebarNav._lastPath === path) return;
        syncSidebarNav._lastPath = path;
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

    function syncSidebarWidthClass() {
        try {
            var collapsed = localStorage.getItem('sidebar_collapsed') === 'true';
            document.documentElement.classList.toggle('admin-sidebar-collapsed', collapsed);
        } catch (e) {}
    }

    function initMainAlpine() {
        if (!window.Alpine) return;
        var main = document.querySelector(mainSel);
        if (!main) return;
        try { Alpine.initTree(main); } catch (e) {}
    }

    function destroyMainAlpine() {
        if (!window.Alpine) return;
        var main = document.querySelector(mainSel);
        if (!main) return;
        try { Alpine.destroyTree(main); } catch (e) {}
    }

    document.addEventListener('turbo:click', function (event) {
        var link = event.target && event.target.closest ? event.target.closest('a') : null;
        if (!link || shouldSkip(link)) return;
        try {
            var next = new URL(link.href, window.location.origin);
            if (next.pathname === window.location.pathname && next.search === window.location.search) {
                event.preventDefault();
                return;
            }
        } catch (e) {}
        startLoader();
    });

    document.addEventListener('turbo:before-visit', function () {
        if (!isBusy) startLoader();
    });

    document.addEventListener('turbo:before-fetch-response', function () {
        bumpLoader(0.72);
    });

    document.addEventListener('turbo:before-render', function (event) {
        bumpLoader(0.94);
        destroyMainAlpine();
        var newBody = event.detail.newBody;
        if (newBody) {
            var path = newBody.getAttribute('data-admin-path');
            var route = newBody.getAttribute('data-admin-route');
            if (path) document.body.setAttribute('data-admin-path', path);
            if (route) document.body.setAttribute('data-admin-route', route);
        }
    });

    document.addEventListener('turbo:load', function () {
        finishLoader();
        syncSidebarWidthClass();
        syncSidebarNav();
        scrollMainTop();
        initMainAlpine();
    });

    document.addEventListener('turbo:render', scrollMainTop);

    document.addEventListener('turbo:fetch-request-error', cancelLoader);
    document.addEventListener('turbo:frame-missing', cancelLoader);
    document.addEventListener('turbo:visit', function (event) {
        if (event.detail && event.detail.action === 'restore' && !isBusy) {
            startLoader();
            bumpLoader(0.55);
        }
    });

    window.addEventListener('pageshow', function (event) {
        if (event.persisted) cancelLoader();
    });

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
