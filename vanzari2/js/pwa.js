if(
    'serviceWorker'
    in navigator
)
{
    navigator
        .serviceWorker
        .register(
            'service-worker.js'
        )
        .then(() => {

            console.log(
                'Service Worker Registered'
            );

        });
}

let deferredPrompt;

window.addEventListener(
    'beforeinstallprompt',
    (e) =>
    {
        e.preventDefault();

        deferredPrompt = e;

        $('#installButton')
            .show();
    }
);

$('#installButton').click(
function(){

    deferredPrompt.prompt();

});

window.addEventListener(
'online',
function(){

    $('#networkStatus')
        .html(
            'Online'
        );

    processOfflineQueue();

});

window.addEventListener(
'offline',
function(){

    $('#networkStatus')
        .html(
            'Offline'
        );

});