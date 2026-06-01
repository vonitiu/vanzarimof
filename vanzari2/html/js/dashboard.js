$(document).ready(function(){

    loadDashboard();
    loadMonthlyReport();

});

$('#btnLogout').click(function(){

    logout();

});

$('#btnOffers').click(function(){

    location.href =
        'offers.html';

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
function loadDashboard()
{
    api('/dashboard.php')
    .done(function(r){

        $('#totalOffers').html(
            r.summary.total_offers
        );

        $('#draftOffers').html(
            r.summary.drafts
        );

        $('#submittedOffers').html(
            r.summary.submitted
        );

        $('#sentOffers').html(
            r.summary.sent
        );

        $('#totalValue').html(
            parseFloat(
                r.summary.total_value || 0
            ).toFixed(2)
        );

        $('#sentThisWeek').html(
            r.sentThisWeek.cnt
        );

        renderCustomers(
            r.topCustomers
        );

        renderAgents(
            r.topAgents
        );

        renderDepartments(
            r.departments
        );

        renderAwaiting(
            r.awaitingEmail
        );

        renderActivity(
            r.recentActivity
        );

    });
}

function renderCustomers(rows)
{
    let html='';

    rows.forEach(function(r){

        html += `
        <tr>

        <td>${r.firma}</td>

        <td>${r.offers}</td>

        <td>${parseFloat(
            r.total
        ).toFixed(2)}</td>

        </tr>
        `;
    });

    $('#topCustomers').html(html);
}

function renderAgents(rows)
{
    let html='';

    rows.forEach(function(r){

        html += `
        <tr>

        <td>${r.responsabil}</td>

        <td>${r.offers}</td>

        <td>${parseFloat(
            r.total
        ).toFixed(2)}</td>

        </tr>
        `;
    });

    $('#topAgents').html(html);
}

function renderDepartments(rows)
{
    let html='';

    rows.forEach(function(r){

        html += `
        <tr>

        <td>${r.departament}</td>

        <td>${r.offers}</td>

        <td>${parseFloat(
            r.total
        ).toFixed(2)}</td>

        </tr>
        `;
    });

    $('#departments').html(html);
}

function renderAwaiting(rows)
{
    let html='';

    rows.forEach(function(r){

        html += `
        <tr>

        <td>${r.numaroferta}</td>

        <td>${r.firma}</td>

        <td>${parseFloat(
            r.offer_total
        ).toFixed(2)}</td>

        </tr>
        `;
    });

    $('#awaitingEmail').html(html);
}

function renderActivity(rows)
{
    let html='';

    rows.forEach(function(r){

        html += `
        <tr>

        <td>${r.created_at}</td>

        <td>${r.user_name}</td>

        <td>${r.action}</td>

        <td>${r.details}</td>

        </tr>
        `;
    });

    $('#recentActivity').html(html);
}

function loadMonthlyReport()
{
    api('/monthly_report.php')
    .done(function(r){

        let html = '';

        r.data.forEach(function(row){

            html += `
            <tr>

                <td>
                    ${row.month}
                </td>

                <td>
                    ${row.offers}
                </td>

                <td>
                    ${parseFloat(
                        row.total || 0
                    ).toFixed(2)}
                </td>

            </tr>
            `;

        });

        $('#monthlyTable').html(
            html
        );

    });
}