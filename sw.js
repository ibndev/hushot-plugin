/**
 * Hushot Service Worker v1.8.9
 * Provides offline caching for PWA functionality
 */

const CACHE_NAME = 'hushot-v1.8.9';
const OFFLINE_URL = '/offline.html';

// Assets to cache immediately (icons are critical for PWA)
const PRECACHE_ASSETS = [
    '/hushot-dashboard/',
    '/hushot-login/'
];

// Install event
self.addEventListener('install', (event) => {
    console.log('[SW] Installing Hushot Service Worker v1.8.0');
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(PRECACHE_ASSETS).catch((err) => {
                console.log('[SW] Precache error (non-fatal):', err);
            });
        })
    );
    // Force activation
    self.skipWaiting();
});

// Activate event - clean old caches
self.addEventListener('activate', (event) => {
    console.log('[SW] Activating Hushot Service Worker v1.8.0');
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames
                    .filter((name) => name.startsWith('hushot-') && name !== CACHE_NAME)
                    .map((name) => {
                        console.log('[SW] Deleting old cache:', name);
                        return caches.delete(name);
                    })
            );
        })
    );
    // Take control of all pages immediately
    self.clients.claim();
});

// Fetch event - network first, cache fallback
self.addEventListener('fetch', (event) => {
    // Only handle GET requests
    if (event.request.method !== 'GET') return;
    
    const url = new URL(event.request.url);
    
    // Skip admin and ajax requests
    if (url.pathname.includes('/wp-admin/') || 
        url.pathname.includes('/wp-json/') ||
        url.pathname.includes('admin-ajax.php')) {
        return;
    }
    
    // Cache-first for static assets (images, icons)
    if (url.pathname.match(/\.(png|jpg|jpeg|gif|svg|ico|webp)$/i)) {
        event.respondWith(
            caches.match(event.request).then((cachedResponse) => {
                if (cachedResponse) {
                    return cachedResponse;
                }
                return fetch(event.request).then((response) => {
                    if (response.ok) {
                        const responseClone = response.clone();
                        caches.open(CACHE_NAME).then((cache) => {
                            cache.put(event.request, responseClone);
                        });
                    }
                    return response;
                });
            })
        );
        return;
    }
    
    // Network-first for everything else
    event.respondWith(
        fetch(event.request)
            .then((response) => {
                if (response.ok) {
                    const responseClone = response.clone();
                    caches.open(CACHE_NAME).then((cache) => {
                        cache.put(event.request, responseClone);
                    });
                }
                return response;
            })
            .catch(() => {
                return caches.match(event.request).then((cachedResponse) => {
                    if (cachedResponse) {
                        return cachedResponse;
                    }
                    if (event.request.mode === 'navigate') {
                        return caches.match(OFFLINE_URL);
                    }
                    return new Response('Offline', { status: 503 });
                });
            })
    );
});
