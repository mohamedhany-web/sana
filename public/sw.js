/**
 * PWA — يخزّن الأيقونات والـ manifest فقط.
 * صفحات HTML دائماً من الشبكة (لا كاش قديم للواجهات).
 */
const CACHE_VERSION = 'sana-static-v5';
const STATIC_PATHS = [
  '/manifest.webmanifest',
  '/icons/icon-192.png',
  '/icons/icon-512.png',
];

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_VERSION).then((cache) =>
      cache.addAll(STATIC_PATHS).catch(() => {})
    )
  );
  self.skipWaiting();
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keys) =>
      Promise.all(keys.filter((k) => k !== CACHE_VERSION).map((k) => caches.delete(k)))
    ).then(() => self.clients.claim())
  );
});

function isHtmlRequest(request) {
  if (request.mode === 'navigate') return true;
  const accept = request.headers.get('accept') || '';
  return accept.includes('text/html');
}

function isStaticAsset(pathname) {
  return STATIC_PATHS.includes(pathname) ||
    pathname.startsWith('/icons/') ||
    pathname.endsWith('.webmanifest');
}

self.addEventListener('fetch', (event) => {
  if (event.request.method !== 'GET') return;

  const url = new URL(event.request.url);
  if (url.origin !== self.location.origin) return;

  // صفحات Laravel — شبكة فقط
  if (isHtmlRequest(event.request)) {
    event.respondWith(
      fetch(event.request).catch(() => caches.match('/'))
    );
    return;
  }

  // أصول ثابتة محدودة — كاش مع تحديث بالخلفية
  if (isStaticAsset(url.pathname)) {
    event.respondWith(
      caches.open(CACHE_VERSION).then(async (cache) => {
        const cached = await cache.match(event.request);
        const network = fetch(event.request)
          .then((response) => {
            if (response && response.status === 200) {
              cache.put(event.request, response.clone());
            }
            return response;
          })
          .catch(() => null);
        return cached || network || new Response('', { status: 504 });
      })
    );
    return;
  }

  // باقي الطلبات — شبكة بدون تخزين (CSS/JS من CDN أو ملفات محلية)
  event.respondWith(fetch(event.request));
});
