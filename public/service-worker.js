const CACHE_VERSION = 'bootkode-pwa-2026-10-20-private-navigation';
const STATIC_CACHE = `${CACHE_VERSION}-static`;
const OFFLINE_PACK_CACHE = 'bootkode-offline-packs-v1';
const OFFLINE_URL = '/offline.html';
const CORE_ASSETS = [
  OFFLINE_URL,
  '/offline-learning.html',
  '/js/offline-learning.js',
  '/img/logo.png',
  '/icons/icon-192.png',
  '/icons/icon-512.png',
  '/manifest.webmanifest'
];

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(STATIC_CACHE)
      .then((cache) => cache.addAll(CORE_ASSETS))
      .then(() => self.skipWaiting())
  );
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys()
      .then((keys) => Promise.all(
        keys
          .filter((key) => key.startsWith('bootkode-pwa-') && key !== STATIC_CACHE)
          .map((key) => caches.delete(key))
      ))
      .then(async () => {
        const packCache = await caches.open(OFFLINE_PACK_CACHE);
        for (const request of await packCache.keys()) {
          const response = await packCache.match(request);
          if (new URL(request.url).pathname.startsWith('/course/') && response?.headers.get('content-type')?.includes('text/html')) {
            await packCache.delete(request);
          }
        }
      })
      .then(() => self.clients.claim())
  );
});

self.addEventListener('fetch', (event) => {
  const { request } = event;

  if (request.method !== 'GET') {
    return;
  }

  const requestUrl = new URL(request.url);

  if (request.mode === 'navigate') {
    event.respondWith(
      fetch(request)
        .then((response) => {
          if (response.ok && requestUrl.pathname === '/offline-learning.html') {
            const copy = response.clone();
            caches.open(OFFLINE_PACK_CACHE).then((cache) => cache.put(request, copy));
          }

          return response;
        })
        .catch(() => requestUrl.pathname.startsWith('/course/')
          ? caches.match(OFFLINE_URL)
          : caches.match(request).then((cached) => cached || caches.match(OFFLINE_URL)))
    );
    return;
  }

  if (requestUrl.origin !== self.location.origin) {
    return;
  }

  if (
    requestUrl.pathname.startsWith('/build/') ||
    requestUrl.pathname.startsWith('/img/') ||
    requestUrl.pathname.startsWith('/icons/') ||
    requestUrl.pathname.startsWith('/js/') ||
    requestUrl.pathname === '/manifest.webmanifest' ||
    requestUrl.pathname === OFFLINE_URL ||
    requestUrl.pathname === '/offline-learning.html'
  ) {
    event.respondWith(
      caches.open(STATIC_CACHE).then((cache) => (
        cache.match(request).then((cached) => {
          const fresh = fetch(request).then((response) => {
            if (response.ok) {
              cache.put(request, response.clone());
            }

            return response;
          }).catch(() => cached);

          return cached || fresh;
        })
      ))
    );
  }
});
