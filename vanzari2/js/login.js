$('#btnLogin').click(function(){

    api(
        '/login.php',
        'POST',
        {
            username:
                $('#username').val(),

            password:
                $('#password').val()
        }
    )
    .done(function(r){

        saveToken(
            r.token
        );

        location.href =
            'offers.html';
    })
    .fail(function(){

        $('#message').html(
            'Invalid login'
        );
    });
});