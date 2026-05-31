const params =
    new URLSearchParams(
        window.location.search
    );

const offerId =
    params.get('id');

$(document).ready(function(){

    if(offerId)
    {
        loadOffer();
    }
    else
    {
        $('#data').val(
            new Date()
            .toISOString()
            .substring(0,10)
        );
    }

});

setInterval(function(){

    if(offerId)
    {
        saveOffer(false);
    }

},30000);

$('#btnSave').click(function(){

    saveOffer(false);

});

$('#btnSaveClose').click(function(){

    saveOffer(true);

});

$('#btnCancel').click(function(){

    location.href =
        'offers.html';

});

function loadOffer()
{
    api(
        '/offer.php?id=' +
        offerId
    )
    .done(function(response){

        let offer =
            response.data.offer;

        $('#offerId').val(
            offer.id
        );

        $('#firma').val(
            offer.firma
        );

        $('#data').val(
            offer.data
        );

        $('#responsabil').val(
            offer.responsabil
        );

        $('#departament').val(
            offer.departament
        );

        $('#valuta').val(
            offer.valuta
        );

        $('#stareoferta').val(
            offer.stareoferta
        );

        $('#contact_client').val(
            offer.contact_client
        );

        $('#email_client').val(
            offer.email_client
        );

        $('#observatii').val(
            offer.observatii
        );

    });
}

function saveOffer(closeAfter)
{
    const payload = {

        firma:
            $('#firma').val(),

        data:
            $('#data').val(),

        responsabil:
            $('#responsabil').val(),

        departament:
            $('#departament').val(),

        valuta:
            $('#valuta').val(),

        stareoferta:
            $('#stareoferta').val(),

        contact_client:
            $('#contact_client').val(),

        email_client:
            $('#email_client').val(),

        observatii:
            $('#observatii').val()
    };

    if(offerId)
    {
        $.ajax({

            url:
                API_BASE +
                '/offer.php?id=' +
                offerId,

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

            afterSave(
                closeAfter
            );

        });
    }
    else
    {
        api(
            '/offer.php',
            'POST',
            payload
        )
        .done(function(response){

            if(closeAfter)
            {
                location.href =
                    'offers.html';
            }
            else
            {
                location.href =
                    'offer-edit.html?id=' +
                    response.id;
            }

        });
    }
}

function afterSave(closeAfter)
{
    if(closeAfter)
    {
        location.href =
            'offers.html';
    }
    else
    {
        alert(
            'Offer saved'
        );
    }
}