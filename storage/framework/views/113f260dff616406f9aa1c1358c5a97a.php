

<?php $__env->startSection('title', 'Sana Whiteboard'); ?>
<?php $__env->startSection('header', 'Sana Whiteboard'); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .mx-standalone-excalidraw-wrap {
        position: relative;
        width: 100%;
        min-height: min(85vh, 880px);
        height: min(85vh, 880px);
        border-radius: 1rem;
        overflow: hidden;
        border: 1px solid rgb(226 232 240);
    }
    .dark .mx-standalone-excalidraw-wrap {
        border-color: rgb(51 65 85);
    }
    #mx-standalone-excalidraw-root {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
    }
    #mx-standalone-excalidraw-root .excalidraw {
        --color-surface-lowest: #0f172a;
        height: 100% !important;
    }
    /* Sana Whiteboard: مكتبة + روابط وخدمات خارجية */
    .mx-Sana-whiteboard .excalidraw .layer-ui__library,
    .mx-Sana-whiteboard .excalidraw .layer-ui__library-message,
    .mx-Sana-whiteboard .excalidraw .library-menu,
    .mx-Sana-whiteboard .excalidraw .library-menu-dropdown-container,
    .mx-Sana-whiteboard .excalidraw .library-menu-dropdown-container--in-heading,
    .mx-Sana-whiteboard .excalidraw .library-menu-items-container,
    .mx-Sana-whiteboard .excalidraw .library-menu-control-buttons,
    .mx-Sana-whiteboard .excalidraw .library-menu-control-buttons--at-bottom,
    .mx-Sana-whiteboard .excalidraw .library-menu-browse-button,
    .mx-Sana-whiteboard .excalidraw .library-menu-items-private-library-container,
    .mx-Sana-whiteboard .excalidraw .library-actions-counter,
    .mx-Sana-whiteboard .excalidraw .single-library-item,
    .mx-Sana-whiteboard .excalidraw .single-library-item-wrapper,
    .mx-Sana-whiteboard .excalidraw .library-unit,
    .mx-Sana-whiteboard .excalidraw .selected-library-items,
    .mx-Sana-whiteboard .excalidraw [class*="publish-library"] {
        display: none !important;
        visibility: hidden !important;
        pointer-events: none !important;
    }
    .mx-Sana-whiteboard .excalidraw .dropdown-menu a.dropdown-menu-item[href^="http://"],
    .mx-Sana-whiteboard .excalidraw .dropdown-menu a.dropdown-menu-item[href^="https://"] {
        display: none !important;
        visibility: hidden !important;
        pointer-events: none !important;
    }
    .mx-Sana-whiteboard .excalidraw .dropdown-menu .dropdown-menu-group:has(a.dropdown-menu-item[href^="http"]),
    .mx-Sana-whiteboard .excalidraw .dropdown-menu .dropdown-menu-group:has(a.dropdown-menu-item[href^="https"]) {
        display: none !important;
    }
    .mx-Sana-whiteboard .excalidraw .HelpDialog__header {
        display: none !important;
    }
    .mx-Sana-whiteboard .excalidraw [data-testid="collab-button"] {
        display: none !important;
        pointer-events: none !important;
    }
    .mx-Sana-whiteboard .excalidraw .ExcalidrawLogo,
    .mx-Sana-whiteboard .excalidraw .welcome-screen-center__logo {
        display: none !important;
        pointer-events: none !important;
    }
    .mx-Sana-whiteboard .excalidraw a.welcome-screen-menu-item[href^="http://"],
    .mx-Sana-whiteboard .excalidraw a.welcome-screen-menu-item[href^="https://"] {
        display: none !important;
        pointer-events: none !important;
    }
    .mx-Sana-whiteboard .excalidraw .ExportDialog a[href^="http://"],
    .mx-Sana-whiteboard .excalidraw .ExportDialog a[href^="https://"],
    .mx-Sana-whiteboard .excalidraw .ImageExportModal a[href^="http://"],
    .mx-Sana-whiteboard .excalidraw .ImageExportModal a[href^="https://"],
    .mx-Sana-whiteboard .excalidraw .OverwriteConfirm a[href^="http://"],
    .mx-Sana-whiteboard .excalidraw .OverwriteConfirm a[href^="https://"],
    .mx-Sana-whiteboard .excalidraw [class*="publish-library"] a[href^="http://"],
    .mx-Sana-whiteboard .excalidraw [class*="publish-library"] a[href^="https://"],
    .mx-Sana-whiteboard .excalidraw .HelpDialog a[href^="http://"],
    .mx-Sana-whiteboard .excalidraw .HelpDialog a[href^="https://"] {
        display: none !important;
        pointer-events: none !important;
        visibility: hidden !important;
    }
    #mx-standalone-loading {
        position: absolute;
        inset: 0;
        z-index: 5;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(15, 23, 42, 0.65);
        color: #94a3b8;
        font-size: 14px;
        text-align: center;
        padding: 1rem;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full px-4 sm:px-6 lg:px-8 py-6 space-y-4">
    <p class="text-sm text-slate-600 dark:text-slate-400">لوحة مستقلة خارج الاجتماع — يمكنك التصدير من قائمة Sana Whiteboard (PNG/SVG).</p>
    <div class="mx-standalone-excalidraw-wrap bg-slate-900">
        <div id="mx-standalone-excalidraw-root" class="mx-Sana-whiteboard" data-lang="ar"></div>
        <div id="mx-standalone-loading">جاري تحميل Sana Whiteboard…</div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php
    $mxBp = rtrim((string) request()->getBasePath(), '/');
    $mxP = $mxBp !== '' ? $mxBp : '';
    $mxExBases = array_values(array_unique(array_filter([
        $mxP . '/mx-vendor/excalidraw/',
        '/mx-vendor/excalidraw/',
        $mxP . '/vendor/excalidraw/',
        '/vendor/excalidraw/',
    ])));
?>

<?php $__env->startPush('scripts'); ?>
<script>
(function() {
    var excRoot = document.getElementById('mx-standalone-excalidraw-root');
    var excLoading = document.getElementById('mx-standalone-loading');
    var mxExcalidrawBases = <?php echo json_encode($mxExBases); ?>;
    var excVendorPromise = null;
    var mounted = false;

    function mxAbsAssetUrl(basePath) {
        var b = String(basePath || '').replace(/\/?$/, '/');
        if (b.indexOf('http') === 0) return b;
        if (b.charAt(0) !== '/') b = '/' + b;
        return window.location.origin + b;
    }

    function loadScriptSequential(url) {
        return new Promise(function(resolve, reject) {
            var s = document.createElement('script');
            s.src = url;
            s.async = false;
            s.onerror = function() {
                s.onerror = s.onload = null;
                reject(new Error('فشل تحميل: ' + url));
            };
            s.onload = function() {
                s.onerror = s.onload = null;
                resolve();
            };
            (document.head || document.documentElement).appendChild(s);
        });
    }

    function getExcalidrawLib() {
        if (typeof ExcalidrawLib !== 'undefined') return ExcalidrawLib;
        if (typeof window.ExcalidrawLib !== 'undefined') return window.ExcalidrawLib;
        return null;
    }

    function ensureExcalidrawVendorLoaded() {
        if (window.React && window.ReactDOM && getExcalidrawLib()) {
            return Promise.resolve();
        }
        if (excVendorPromise) return excVendorPromise;
        var bases = Array.isArray(mxExcalidrawBases) ? mxExcalidrawBases : [];
        if (!bases.length) bases = ['/mx-vendor/excalidraw/', '/vendor/excalidraw/'];

        function loadFromBase(basePath) {
            var root = String(basePath || '').replace(/\/?$/, '/');
            window.EXCALIDRAW_ASSET_PATH = root + 'dist/';
            var prefix = mxAbsAssetUrl(root);
            return loadScriptSequential(prefix + 'react.production.min.js')
                .then(function() { return loadScriptSequential(prefix + 'react-dom.production.min.js'); })
                .then(function() { return loadScriptSequential(prefix + 'dist/excalidraw.production.min.js'); })
                .then(function() {
                    if (!window.React || !window.ReactDOM || !getExcalidrawLib()) {
                        throw new Error('تعذّر تعريف المكتبات بعد التحميل');
                    }
                });
        }

        function tryNext(i) {
            if (i >= bases.length) {
                return Promise.reject(new Error('فشل تحميل ملفات اللوحة من كل المسارات'));
            }
            return loadFromBase(bases[i]).catch(function() { return tryNext(i + 1); });
        }

        excVendorPromise = tryNext(0).catch(function(e) {
            excVendorPromise = null;
            throw e;
        });
        return excVendorPromise;
    }

    function nudgeLayout() {
        window.dispatchEvent(new Event('resize'));
        if (window.requestAnimationFrame) {
            requestAnimationFrame(function() { window.dispatchEvent(new Event('resize')); });
        }
    }

    function fail(msg) {
        if (excLoading) {
            excLoading.textContent = msg;
            excLoading.style.display = 'flex';
        }
        console.error('[Whiteboard standalone]', msg);
    }

    function mountWhenSized() {
        var deadline = Date.now() + 5000;
        function tick() {
            if (!excRoot) return;
            var rect = excRoot.getBoundingClientRect();
            if (rect.width < 8 || rect.height < 8) {
                if (Date.now() > deadline) {
                    fail('الحاوية بلا أبعاد كافية.');
                    return;
                }
                requestAnimationFrame(tick);
                return;
            }
            try {
                var Lib = getExcalidrawLib();
                var R = window.React;
                var RD = window.ReactDOM;
                var Excalidraw = Lib.Excalidraw;
                if (Excalidraw == null || (typeof Excalidraw !== 'function' && typeof Excalidraw !== 'object')) {
                    throw new Error('مكوّن Sana Whiteboard غير متاح');
                }
                if (typeof RD.createRoot !== 'function') {
                    throw new Error('ReactDOM.createRoot غير متاح');
                }
                RD.createRoot(excRoot).render(R.createElement(Excalidraw, {
                    langCode: 'ar-SA',
                    excalidrawAPI: function(api) {
                        window.__mxStandaloneExcalidrawAPI = api;
                    }
                }));
                mounted = true;
                if (excLoading) excLoading.style.display = 'none';
                nudgeLayout();
                setTimeout(nudgeLayout, 120);
            } catch (e) {
                fail('تعذّر تهيئة اللوحة: ' + (e && e.message ? e.message : String(e)));
            }
        }
        requestAnimationFrame(tick);
    }

    document.addEventListener('DOMContentLoaded', function() {
        if (!excRoot) return;
        ensureExcalidrawVendorLoaded()
            .then(function() { mountWhenSized(); })
            .catch(function(e) {
                fail('تعذّر تحميل المكتبات: ' + (e && e.message ? e.message : ''));
            });
    });
})();
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sana\resources\views\student\classroom\whiteboard-standalone.blade.php ENDPATH**/ ?>