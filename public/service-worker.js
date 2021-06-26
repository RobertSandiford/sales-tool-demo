const staticCacheName = 'site-cache';

const version = 102;

const standardAssets = [
    '/manifest.json',
    '/js/pwa.js',
    '/js/setup_idb.js',
    '/js/setup_service_worker.js',
    '/js/app.js',
    '/css/app.css',
    'https://fonts.googleapis.com/css?family=Nunito',
    'https://fonts.gstatic.com/s/nunito/v13/XRXV3I6Li01BKofINeaB.woff2',
    '/favicon.ico',
    '/images/logo-cropped-2.png',
    '/images/icons/icon-144.png',
    '/images/icons/icon-288.png',
    '/images/bifold%20web%20bright%202.jpg',
    '/images/SASH.jpg',
    '/images/casement-windows-double-glazing.jpg',
    '/images/left-arrow-arrows-svgrepo-com.svg',
    '/images/right-arrow-arrows-svgrepo-com.svg',
]

const pageAssets = [
    '/',
    '/home',
    '/home/local',
    '/quote',
    '/quote/local/template',
    '/quotes/offline',
    '/pricing',
    '/pricing/local/template',
    '/finance',
    '/finance/local/template',
    '/management',
    '/management/local',
    '/settings',
]


// install
self.addEventListener('install', event => {
    console.log("service worker installed")

    event.waitUntil(
        caches.open(staticCacheName)
            .then( cache => {
                cache.addAll(standardAssets)
                cache.addAll(pageAssets)
            })
    )
})


// activate
self.addEventListener('activate', event => {
    console.log("service worker activated")
})


// fetch
self.addEventListener('fetch', event => {

    // get the request path
    var path = event.request.url.substring(event.request.url.indexOf('/',8))


    // always return cached local page for quote/local? (uses standard QuotePage component via React routing)
    let quoteLocalPattern = new RegExp("/quote/local(/[0-9]+)?$");

    if ( quoteLocalPattern.test(path) ) {
        console.log("quote local page fetch")
        event.respondWith(
            caches.match('/quote/local/template').then(function(response) {
                console.log("returning quote offline template")
                return response
            })
        );
        return
    }


    // always return cached local page for pricing/local? (uses standard PricingPage component via React routing)
    let pricingLocalPattern = new RegExp("/pricing/local/[0-9]+$");
    
    if ( pricingLocalPattern.test(path) ) {
        event.respondWith(
            caches.match('/pricing/local/template').then(function(response) {
                console.log("returning pricing offline template")
                return response
            })
        );
        return
    }


    // always return cached local page for finance/local? (uses standard FinancePage component via React routing)
    let financingLocalPattern = new RegExp("/finance/local/[0-9]+$");
    
    if ( financingLocalPattern.test(path) ) {
        event.respondWith(
            caches.match('/finance/local/template').then(function(response) {
                console.log("returning financing offline template")
                return response
            })
        );
        return
    }

    
    // use cached assets first, else seek internet asset for standard cached assets
    if ( standardAssets.includes(path) || standardAssets.includes(event.request.url) ) {
        event.respondWith(
            caches.match(event.request).then(function(response) {
                return response || fetch(event.request);
            })
        );
        return
    }


    // use cached assets only for certain pages
    if ( pageAssets.includes(path) ) {
        event.respondWith(
            fetch(event.request).catch(function() {
                return caches.match(event.request);
            })
        );
        return
    }


    switch (path) {

        // home pages
        // always used cached home pages - why? fudge to make these pages work offline?
        case "/home":
        case "/management":
            event.respondWith(
                caches.match(event.request).then(function(response) {
                    return response
                })
            );

        /*case "/login":

            //console.log("try to kill this request")
            //event.respondWith(null)

            break;*/

        /*case "/save-pricing":
            console.log("try to kill this save-pricing")
            event.respondWith(null)
            break;*/

        /*case "/sold":
            console.log("try to kill this save-pricing")
            event.respondWith(null)
            break;*/

        /*case "/quotes":
            event.respondWith(
                fetch(event.request).catch(function() {
                    return caches.match("/quotes/offline");
                })
            );*/

        /*default:
            event.respondWith(
                caches.match(event.request).then(function(response) {
                    return response || fetch(event.request);
                })
            );
            break;*/
    }


})