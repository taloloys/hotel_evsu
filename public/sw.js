const CACHE_NAME = 'evsu-hotel-cache-v1';

// Calculate base path from service worker location (supports root domain and subfolder hosting)
const basePath = self.location.pathname.substring(0, self.location.pathname.lastIndexOf('/') + 1);
const OFFLINE_URL = basePath + 'offline.html';

const PRECACHE_ASSETS = [
    OFFLINE_URL,
    basePath + 'site.webmanifest',
    basePath + 'images/logo.png',
    basePath + 'images/icons/icon-192x192.png',
    basePath + 'images/icons/icon-512x512.png',
    basePath + 'images/icons/favicon-32x32.png'
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(PRECACHE_ASSETS).catch((err) => {
                console.warn('PWA: Failed to cache some static assets during install:', err);
            });
        }).then(() => self.skipWaiting())
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((name) => {
                    if (name !== CACHE_NAME) {
                        return caches.delete(name);
                    }
                })
            );
        }).then(() => self.clients.claim())
    );
});

self.addEventListener('fetch', (event) => {
    // Only handle GET requests; never intercept POST, PUT, DELETE, etc.
    if (event.request.method !== 'GET') {
        return;
    }

    const url = new URL(event.request.url);

    // Only process same-origin requests
    if (url.origin !== self.location.origin) {
        return;
    }

    // Handle page navigation requests
    if (event.request.mode === 'navigate') {
        event.respondWith(
            fetch(event.request).catch(async () => {
                const cachedResponse = await caches.match(OFFLINE_URL);
                if (cachedResponse) {
                    return cachedResponse;
                }
                return new Response('Network error occurred', {
                    status: 503,
                    statusText: 'Service Unavailable',
                    headers: { 'Content-Type': 'text/plain' }
                });
            })
        );
        return;
    }

    // Only intercept specific static assets (images, manifest, icons, css, js)
    const isStaticAsset =
        url.pathname.includes('/images/') ||
        url.pathname.endsWith('.webmanifest') ||
        url.pathname.endsWith('.png') ||
        url.pathname.endsWith('.jpg') ||
        url.pathname.endsWith('.jpeg') ||
        url.pathname.endsWith('.svg') ||
        url.pathname.endsWith('.ico') ||
        url.pathname.endsWith('.css') ||
        url.pathname.endsWith('.js');

    if (!isStaticAsset) {
        return;
    }

    // For static assets: network first, then cache, with valid fallback response
    event.respondWith(
        fetch(event.request)
            .then((networkResponse) => {
                if (networkResponse && networkResponse.status === 200 && networkResponse.type === 'basic') {
                    const clonedResponse = networkResponse.clone();
                    caches.open(CACHE_NAME).then((cache) => {
                        cache.put(event.request, clonedResponse);
                    });
                }
                return networkResponse;
            })
            .catch(async () => {
                const cachedResponse = await caches.match(event.request);
                if (cachedResponse) {
                    return cachedResponse;
                }
                return new Response('', { status: 408, statusText: 'Request Timeout' });
            })
    );
});
