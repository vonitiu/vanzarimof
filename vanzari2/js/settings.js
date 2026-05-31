$(document).ready(function(){

    loadSettings();

});

function loadSettings()
{
    api('/settings.php')
    .done(function(r){

        let s =
            r.data;

        $('#smtp_host').val(
            s.smtp_host || ''
        );

        $('#smtp_port').val(
            s.smtp_port || ''
        );

        $('#smtp_user').val(
            s.smtp_user || ''
        );

        $('#smtp_password').val(
            s.smtp_password || ''
        );

        $('#default_email_recipient').val(
            s.default_email_recipient || ''
        );

    });
}

$('#btnSave').click(function(){

    api(
        '/settings.php',
        'POST',
        {
            smtp_host:
                $('#smtp_host').val(),

            smtp_port:
                $('#smtp_port').val(),

            smtp_user:
                $('#smtp_user').val(),

            smtp_password:
                $('#smtp_password').val(),

            default_email_recipient:
                $('#default_email_recipient').val()
        }
    )
    .done(function(){

        alert(
            'Settings saved'
        );

    });
})