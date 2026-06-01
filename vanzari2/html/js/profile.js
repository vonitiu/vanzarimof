$(document).ready(function(){

    loadProfile();

});

function loadProfile()
{
    api('/profile.php')
    .done(function(r){

        $('#full_name').val(
            r.data.full_name
        );

        $('#email').val(
            r.data.email
        );

        $('#username').val(
            r.data.username
        );

        $('#role').val(
            r.data.role
        );
    });
}

$('#btnSaveProfile').click(function(){

    $.ajax({

        url:
            API_BASE +
            '/profile.php',

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
            JSON.stringify({

                full_name:
                    $('#full_name').val(),

                email:
                    $('#email').val()

            })

    })
    .done(function(){

        alert(
            'Profile saved'
        );

    });

});

$('#btnChangePassword').click(function(){

    api(
        '/change_password.php',
        'POST',
        {
            current_password:
                $('#currentPassword').val(),

            new_password:
                $('#newPassword').val()
        }
    )
    .done(function(){

        alert(
            'Password changed'
        );

        $('#currentPassword').val('');
        $('#newPassword').val('');

    });

});