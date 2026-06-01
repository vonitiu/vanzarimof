const params =
    new URLSearchParams(
        window.location.search
    );

const userId =
    params.get('id');

$(document).ready(function(){

    if(userId)
    {
        $('#passwordContainer')
            .hide();

        loadUser();
    }

});

$('#btnSave').click(function(){

    saveUser();

});

function loadUser()
{
    api(
        '/user.php?id=' +
        userId
    )
    .done(function(r){

        let u =
            r.data;

        $('#username').val(
            u.username
        );

        $('#full_name').val(
            u.full_name
        );

        $('#email').val(
            u.email
        );

        $('#role').val(
            u.role
        );

        $('#active').prop(
            'checked',
            u.active == 1
        );
    });
}

function saveUser()
{
    let payload = {

        username:
            $('#username').val(),

        full_name:
            $('#full_name').val(),

        email:
            $('#email').val(),

        role:
            $('#role').val(),

        active:
            $('#active').is(':checked')
                ? 1
                : 0
    };

    if(
        !$('#username').val()
    )
    {
        alert(
            'Username required'
        );

        return;
    }

    if(
        !userId &&
        !$('#password').val()
    )
    {
        alert(
            'Password required'
        );

        return;
    }
    
    if(userId)
    {
        $.ajax({

            url:
                API_BASE +
                '/user.php?id=' +
                userId,

            method:'PUT',

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
                JSON.stringify(
                    payload
                )

        })
        .done(function(){

            location.href =
                'users.html';

        });

        return;
    }

    payload.password =
        $('#password').val();

    api(
        '/user.php',
        'POST',
        payload
    )
    .done(function(){

        location.href =
            'users.html';

    });
}

$(document).ready(function(){

    if(
        !requireRoles([
            'admin'
        ])
    )
    {
        return;
    }

    loadUsers();

});