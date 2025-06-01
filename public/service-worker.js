const CACHE_NAME = "saku-rame-cache-v2";  // Versi diperbarui
const OFFLINE_URL = '/offline.html';
const ASSETS = [
    '/',
    '/index.php',
    '/css/app.css',
    '/js/app.js',
    '/images/icons/icon-192x192.png',
    '/images/icons/icon-512x512.png',
    OFFLINE_URL
];

// Install Service Worker
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => cache.addAll(ASSETS))
            .then(() => self.skipWaiting())  // Memaksa SW aktif segera
    );
});

// Activate Service Worker
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames
                    .filter(name => name !== CACHE_NAME)
                    .map(name => caches.delete(name))
            );
        }).then(() => self.clients.claim())  // Mengontrol semua klien segera
    );
});

// Fetch Strategy: Cache First with Network Fallback
self.addEventListener('fetch', event => {
    // Skip non-GET requests
    if (event.request.method !== 'GET') return;

    // Handle navigation requests separately
    if (event.request.mode === 'navigate') {
        event.respondWith(
            fetch(event.request)
                .catch(() => caches.match(OFFLINE_URL))
        );
        return;
    }

    event.respondWith(
        caches.match(event.request)
            .then(cachedResponse => {
                // Return cached response if found
                if (cachedResponse) {
                    return cachedResponse;
                }

                // Otherwise fetch from network
                return fetch(event.request)
                    .then(response => {
                        // Cache the new response if valid
                        if (!response || response.status !== 200 || response.type !== 'basic') {
                            return response;
                        }

                        const responseToCache = response.clone();
                        caches.open(CACHE_NAME)
                            .then(cache => cache.put(event.request, responseToCache));

                        return response;
                    });
            })
            .catch(() => {
                // For assets, return offline page if navigation request
                if (event.request.mode === 'navigate') {
                    return caches.match(OFFLINE_URL);
                }
            })
    );
});
