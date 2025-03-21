const CACHE_NAME = "saku-rame-cache-v1";
const urlsToCache = [
    "/",
    "/index.php",
    "/offline.html",
    "/css/app.css",
    "/js/app.js",
    "/images/icons/icon-192x192.png",
    "/images/icons/icon-512x512.png"
];

// Install Service Worker
self.addEventListener("install", event => {
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => {
            return cache.addAll(urlsToCache);
        })
    );
});

// Fetch Requests
self.addEventListener("fetch", event => {
    event.respondWith(
        fetch(event.request).catch(() => caches.match(event.request))
    );
});

// Activate Service Worker
self.addEventListener("activate", event => {
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames
                    .filter(cacheName => cacheName !== CACHE_NAME)
                    .map(cacheName => caches.delete(cacheName))
            );
        })
    );
});
