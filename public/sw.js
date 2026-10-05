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
            fetch(event.request).catch(() => {
                return caches.match(OFFLINE_URL);
            })
        );
        return;
    }

    // For static assets (images, manifest, icons): network first, then cache
    event.respondWith(
        fetch(event.request)
            .then((networkResponse) => {
                if (networkResponse && networkResponse.status === 200 && networkResponse.type === 'basic') {
                    const clonedResponse = networkResponse.clone();
                    caches.open(CACHE_NAME).then((cache) => {
                        if (
                            url.pathname.includes('/images/') ||
                            url.pathname.endsWith('.webmanifest') ||
                            url.pathname.endsWith('.png') ||
                            url.pathname.endsWith('.ico')
                        ) {
                            cache.put(event.request, clonedResponse);
                        }
                    });
                }
                return networkResponse;
            })
            .catch(() => {
                return caches.match(event.request);
            })
    );
});
