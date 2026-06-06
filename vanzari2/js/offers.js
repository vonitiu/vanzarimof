$(document).ready(function(){

    loadOffers();

});

$('#btnLogout').click(function(){

    logout();

});

$('#btnNewOffer').click(function(){

    location.href =
        'offer-edit.html';

});

$('#btnExport').click(function(){

    api(
        '/export_excel.php',
        'POST'
    )
    .done(function(r){

        window.open(

            API_BASE +
            '/download_export.php?file=' +
            r.file

        );

    });

});

$('#btnSearch').click(function(){

    loadOffers();

});

function loadOffers()
{
    let query = '';

    query += '?search=' +
        encodeURIComponent(
            $('#search').val()
        );

    query += '&status=' +
        encodeURIComponent(
            $('#status').val()
        );

    query += '&date_from=' +
        $('#dateFrom').val();

    query += '&date_to=' +
        $('#dateTo').val();

    api(
        '/offers.php' + query
    )
    .done(function(response){

        renderOffers(
            response.data
        );

    })
    .fail(function(){

        alert(
            'Failed loading offers'
        );

    });
}

function renderOffers(rows)
{
    let html = '';

    rows.forEach(function(row){

        html += `
        <tr>

            <td>
                ${row.numaroferta}
            </td>

            <td>
                ${row.data || ''}
            </td>

            <td>
                ${row.firma || ''}
            </td>

            <td>
                ${row.responsabil || ''}
            </td>

            <td>
                ${row.item_count}
            </td>

            <td>
                ${parseFloat(
                    row.calculated_total || 0
                ).toFixed(2)}
            </td>

            <td>

                <span class="
                    status
                    status-${(
                        row.stareoferta || ''
                    ).toLowerCase()}
                ">
                    ${row.stareoferta || ''}
                </span>

            </td>

            <td>
                <div class="button-row">
                <button class="btn" 
                        class="action-btn"
                        onclick="viewOffer(${row.id})">

                        View

                    </button>
                    <!-- button class="btn" 
                        class="action-btn"
                        onclick="editOffer(${row.id})">

                        Edit

                    </button>

                    

                    <button class="btn" 
                        class="action-btn"
                        onclick="duplicateOffer(${row.id})">

                        Duplicate

                    </button>

                    <button class="btn" 
                        class="action-btn"
                        onclick="deleteOffer(${row.id})">

                        Delete

                    </button-->
                </div>
            </td>

        </tr>
        `;
    });

    $('#offersTable').html(html);
}

function editOffer(id)
{
    location.href =
        'offer-edit.html?id=' + id;
}

function viewOffer(id)
{
    location.href =
        'offer.html?id=' + id;
}

function duplicateOffer(id)
{
    if(
        !confirm(
            'Duplicate offer?'
        )
    )
    {
        return;
    }

    api(
        '/duplicate_offer.php?id=' + id,
        'POST'
    )
    .done(function(){

        loadOffers();

    });
}

function deleteOffer(id)
{
    if(
        !confirm(
            'Delete offer?'
        )
    )
    {
        return;
    }

    $.ajax({

        url:
            API_BASE +
            '/offer.php?id=' +
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

        loadOffers();

    });
}

