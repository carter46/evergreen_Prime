/**
 * Evergreen Prime — minimal PWA service worker (static assets only).
 * Never caches dashboard, API, markets, or live data.
 */
const CACHE_NAME = 'epm-static-v4';

const STATIC_ASSETS = [
    '/pwa/icons/icon-180.png',
    '/pwa/icons/icon-192.png',
    '/pwa/icons/icon-512.png',
    '/js/pwa-register.js',
    '/js/pwa-install.js',
    '/js/app.js',
    '/js/crypto-config.js',
    '/js/crypto-prices.js',
    '/manifest.webmanifest',
];

function isStaticAsset(pathname) {
    return STATIC_ASSETS.indexOf(pathname) !== -1;
}

self.addEventListener('install', function (event) {
    self.skipWaiting();
    event.waitUntil(
        caches.open(CACHE_NAME).then(function (cache) {
            return cache.addAll(STATIC_ASSETS).catch(function () {
                /* partial precache ok */
            });
        })
    );
});

self.addEventListener('activate', function (event) {
    event.waitUntil(
        caches.keys().then(function (keys) {
            return Promise.all(
                keys.filter(function (k) { return k !== CACHE_NAME; }).map(function (k) {
                    return caches.delete(k);
                })
            );
        }).then(function () {
            return self.clients.claim();
        })
    );
});

self.addEventListener('fetch', function (event) {
    if (event.request.method !== 'GET') return;

    var url = new URL(event.request.url);
    if (url.origin !== self.location.origin) return;
    if (!isStaticAsset(url.pathname)) return;

    event.respondWith(
        caches.match(event.request).then(function (cached) {
            if (cached) return cached;
            return fetch(event.request);
        })
    );
});
