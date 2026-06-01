const params =
    new URLSearchParams(
        window.location.search
    );

const offerId =
    params.get('id');

$(document).ready(function(){

    loadOffer();
    loadItems();

});

$('#btnGeneratePdf').click(function(){

    api(
        '/generate_pdf.php?id=' +
        offerId,
        'POST'
    )
    .done(function(){

        alert(
            'PDF generated'
        );

    });

});

$('#btnPreviewPdf').click(function(){

    window.open(
        API_BASE +
        '/download_pdf.php?id=' +
        offerId,
        '_blank'
    );

});

$('#btnSendEmail').click(function(){

    $('#emailRecipient').val(
        DEFAULT_PROCESSING_EMAIL
    );

    $('#emailSubject').val(
        'Offer ' +
        $('#offerNumber').text()
    );

    $('#emailBody').val(
        'Please find attached the offer.'
    );

    $('#emailModal').show();

});

$('#btnConfirmEmail').click(function(){

    api(
        '/send_email.php',
        'POST',
        {
            offer_id:
                offerId,

            recipient:
                $('#emailRecipient').val(),

            subject:
                $('#emailSubject').val(),

            body:
                $('#emailBody').val()
        }
    )
    .done(function(){

        alert(
            'Email sent'
        );

        $('#emailModal').hide();

        loadOffer();

    })
    .fail(function(r){

        alert(
            r.responseJSON.message
        );

    });

});

$('#btnBack').click(function(){

    location.href =
        'offers.html';

});

$('#btnEditOffer').click(function(){

    location.href =
        'offer-edit.html?id=' +
        offerId;

});

$('#btnAddItem').click(function(){

    location.href =
        'item-edit.html?offer=' +
        offerId;

});

function loadOffer()
{
    api(
        '/offer.php?id=' +
        offerId
    )
    .done(function(response){

        let offer =
            response.data;

        $('#offerNumber').html(
            offer.numaroferta
        );

        $('#firma').html(
            offer.firma || ''
        );

        $('#data').html(
            offer.data || ''
        );

        $('#responsabil').html(
            offer.responsabil || ''
        );

        $('#departament').html(
            offer.departament || ''
        );

        $('#stare').html(
            offer.stareoferta || ''
        );

        $('#valuta').html(
            offer.valuta || ''
        );

        $('#email').html(
            offer.email_client || ''
        );

    });
}

function loadItems()
{
    api(
        '/items.php?offer=' +
        offerId
    )
    .done(function(response){

        renderItems(
            response.data
        );

    });
}

function renderItems(items)
{
    let html = '';

    let total = 0;

    items.forEach(function(item){

        total += parseFloat(
            item.total || 0
        );

        html += `
        <tr>

            <td>
                ${item.cod || ''}
            </td>

            <td>
                ${item.descriere || ''}
            </td>

            <td>
                ${item.buc || ''}
            </td>

            <td>
                ${item.pret || ''}
            </td>

            <td>
                ${item.discount || ''}
            </td>

            <td>
                ${item.total || ''}
            </td>

            <td>

                <button class="btn" 
                    onclick="
                        editItem(
                            ${item.id}
                        )
                    ">

                    Edit

                </button>

                <button class="btn" 
                    onclick="
                        deleteItem(
                            ${item.id}
                        )
                    ">

                    Delete

                </button>

            </td>

        </tr>
        `;
    });

    $('#itemsTable').html(
        html
    );

    $('#itemCount').html(
        items.length
    );

    $('#offerTotal').html(
        total.toFixed(2)
    );
}

function editItem(id)
{
    location.href =
        'item-edit.html?id=' +
        id +
        '&offer=' +
        offerId;
}

function deleteItem(id)
{
    if(
        !confirm(
            'Delete item?'
        )
    )
    {
        return;
    }

    $.ajax({

        url:
            API_BASE +
            '/item.php?id=' +
            id,

        method:'DELETE',

        headers:{
            Authorization:
            'Bearer ' +
            localStorage.getItem(
                'token'
            )
        }

    })
    .done(function(){

        loadOffer();
        loadItems();

    });
}

