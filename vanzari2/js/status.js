$(document).ready(function(){

    api('/status.php')
    .done(function(r){

        let html = '';

        Object.keys(
            r.data
        ).forEach(function(key){

            html += `
            <tr>

                <td>
                    ${key}
                </td>

                <td>
                    ${r.data[key]}
                </td>

            </tr>
            `;
        });

        $('#statusTable').html(
            html
        );

    });

});