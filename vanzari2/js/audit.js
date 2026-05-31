$(document).ready(function(){

    loadAudit();

});

$('#btnSearch').click(function(){

    loadAudit();

});

function loadAudit()
{
    let query = '?';

    query +=
        'user=' +
        encodeURIComponent(
            $('#userFilter').val()
        );

    query +=
        '&action=' +
        encodeURIComponent(
            $('#actionFilter').val()
        );

    query +=
        '&date_from=' +
        $('#dateFrom').val();

    query +=
        '&date_to=' +
        $('#dateTo').val();

    api(
        '/audit.php' +
        query
    )
    .done(function(r){

        renderAudit(
            r.data
        );

    });
}

function renderAudit(rows)
{
    let html = '';

    rows.forEach(function(row){

        html += `
        <tr>

            <td>
                ${row.created_at}
            </td>

            <td>
                ${row.user_name}
            </td>

            <td>
                ${row.action}
            </td>

            <td>
                ${row.details}
            </td>

        </tr>
        `;
    });

    $('#auditTable').html(
        html
    );
}