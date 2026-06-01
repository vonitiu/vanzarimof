const CACHE_NAME =
    'sales-offers-v1';

const STATIC_ASSETS = [

    '/login.html',
    '/dashboard.html',
    '/offers.html',
    '/offer.html',
    '/offer-edit.html',
    '/item-edit.html',

    '/css/app.css',

    '/js/api.js',
    '/js/auth.js',
    '/js/pwa.js',

    '/offline.html'
];

self.addEventListener(
    'install',
    event =>
    {
        event.waitUntil(

            caches.open(
                CACHE_NAME
            )
            .then(cache => {

                return cache.addAll(
                    STATIC_ASSETS
                );

            })

        );
    }
);

self.addEventListener(
    'activate',
    event =>
    {
        event.waitUntil(

            caches.keys()
            .then(keys => {

                return Promise.all(

                    keys.map(key => {

                        if(
                            key !== CACHE_NAME
                        )
                        {
                            return caches.delete(
                                key
                            );
                        }

                    })

                );

            })

        );
    }
);

self.addEventListener(
    'fetch',
    event =>
    {
        event.respondWith(

            fetch(
                event.request
            )
            .catch(() => {

                return caches.match(
                    event.request
                );

            })

        );
    }
);