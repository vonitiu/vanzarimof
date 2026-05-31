function queueRequest(
    url,
    method,
    payload
)
{
    let queue =
        JSON.parse(
            localStorage.getItem(
                'offlineQueue'
            ) || '[]'
        );

    queue.push({

        url,
        method,
        payload

    });

    localStorage.setItem(
        'offlineQueue',
        JSON.stringify(queue)
    );
}

function processOfflineQueue()
{
    let queue =
        JSON.parse(
            localStorage.getItem(
                'offlineQueue'
            ) || '[]'
        );

    if(
        queue.length === 0
    )
    {
        return;
    }

    queue.forEach(item => {

        $.ajax({

            url:
                API_BASE +
                item.url,

            method:
                item.method,

            contentType:
                'application/json',

            data:
                JSON.stringify(
                    item.payload
                )

        });

    });

    localStorage.removeItem(
        'offlineQueue'
    );
}