const params =
    new URLSearchParams(
        window.location.search
    );

const itemId =
    params.get('id');

const offerId =
    params.get('offer');

$(document).ready(function(){

    if(itemId)
    {
        loadItem();
    }

});

$('#buc').on(
    'keyup change',
    recalculate
);

$('#pret').on(
    'keyup change',
    recalculate
);

$('#discount').on(
    'keyup change',
    recalculate
);

$('#btnCancel').click(function(){

    location.href =
        'offer.html?id=' +
        offerId;

});

$('#btnSave').click(function(){

    saveItem();

});

function loadItem()
{
    api(
        '/item.php?id=' +
        itemId
    )
    .done(function(response){

        let item =
            response.data;

        $('#cod').val(
            item.cod
        );

        $('#catalog_no').val(
            item.catalog_no
        );

        $('#material_no').val(
            item.material_no
        );

        $('#descriere').val(
            item.descriere
        );

        $('#pret').val(
            item.pret
        );

        $('#buc').val(
            item.buc
        );

        $('#discount').val(
            item.discount
        );

        $('#livrare').val(
            item.livrare
        );

        $('#packing_q').val(
            item.packing_q
        );

        $('#cod_client').val(
            item.cod_client
        );

        $('#obs1').val(
            item.obs1
        );

        $('#obs2').val(
            item.obs2
        );

        $('#valoare').val(
            item.valoare
        );

        $('#total').val(
            item.total
        );

    });
}

function recalculate()
{
    let qty =
        parseFloat(
            $('#buc').val()
        ) || 0;

    let price =
        parseFloat(
            $('#pret').val()
        ) || 0;

    let discount =
        parseFloat(
            $('#discount').val()
        ) || 0;

    let value =
        qty * price;

    let total =
        value - discount;

    $('#valoare').val(
        value.toFixed(2)
    );

    $('#total').val(
        total.toFixed(2)
    );
}

function saveItem()
{
    const payload = {

        oferta:
            offerId,

        cod:
            $('#cod').val(),

        catalog_no:
            $('#catalog_no').val(),

        material_no:
            $('#material_no').val(),

        descriere:
            $('#descriere').val(),

        pret:
            $('#pret').val(),

        buc:
            $('#buc').val(),

        discount:
            $('#discount').val(),

        livrare:
            $('#livrare').val(),

        packing_q:
            $('#packing_q').val(),

        cod_client:
            $('#cod_client').val(),

        obs1:
            $('#obs1').val(),

        obs2:
            $('#obs2').val()
    };

    if(itemId)
    {
        $.ajax({

            url:
                API_BASE +
                '/item.php?id=' +
                itemId,

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
                'offer.html?id=' +
                offerId;

        });
    }
    else
    {
        api(
            '/item.php',
            'POST',
            payload
        )
        .done(function(){

            location.href =
                'offer.html?id=' +
                offerId;

        });
    }
}