function api(
    endpoint,
    method='GET',
    data=null
)
{
    if(
        !navigator.onLine &&
        method !== 'GET'
    )
    {
        queueRequest(
            endpoint,
            method,
            data
        );

        let deferred =
            $.Deferred();

        deferred.resolve({

            success:true,
            offline:true,
            message:
                'Queued for sync'
        });

        return deferred.promise();
    }

    return $.ajax({

        url:
            API_BASE +
            endpoint,

        method:
            method,

        contentType:
            'application/json',

        headers:{
            Authorization:
                'Bearer ' +
                localStorage.getItem(
                    'token'
                )
        },

        data:
            data
                ? JSON.stringify(data)
                : null
    });
}


$(document).ready(function(){

    const currentPage =
        window.location.pathname
            .split('/')
            .pop()
            .toLowerCase();

    if(
        currentPage != 'login.html'
    )
    {
        $('#menuContainer').load(
            'partials/admin-menu.html'
        );
    }   

});

$('#menuToggle').click(function(){

    $('.sidebar')
        .toggleClass(
            'open'
        );

});