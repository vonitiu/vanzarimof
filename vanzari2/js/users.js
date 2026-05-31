$(document).ready(function(){

    loadUsers();

});

$('#btnNewUser').click(function(){

    location.href =
        'user-edit.html';

});

function loadUsers()
{
    api('/users.php')
    .done(function(r){

        renderUsers(
            r.data
        );

    });
}

function renderUsers(rows)
{
    let html = '';

    rows.forEach(function(user){

        html += `
        <tr>

            <td>${user.username}</td>

            <td>${user.full_name || ''}</td>

            <td>${user.email || ''}</td>

            <td>${user.role}</td>

            <td>
                ${user.active ? 'Yes':'No'}
            </td>

            <td>
                ${user.last_login || ''}
            </td>

            <td>

                <button
                    onclick="
                        editUser(
                            ${user.id}
                        )
                    ">
                    Edit
                </button>

                <button
                    onclick="
                        resetPassword(
                            ${user.id}
                        )
                    ">
                    Reset Password
                </button>
                <button
                    onclick="
                        deactivateUser(
                            ${user.id}
                        )
                        ">
                    Deactivate
                </button>
            </td>

        </tr>
        `;
    });

    $('#usersTable').html(html);
}

function editUser(id)
{
    location.href =
        'user-edit.html?id=' +
        id;
}

function resetPassword(id)
{
    if(!confirm(
        'Reset password?'
    ))
    {
        return;
    }

    api(
        '/reset_password.php',
        'POST',
        {
            user_id:id
        }
    )
    .done(function(r){

        alert(
            'Temporary password:\n\n' +
            r.temporary_password
        );

    });
}

function deactivateUser(id)
{
    if(
        !confirm(
            'Deactivate user?'
        )
    )
    {
        return;
    }

    $.ajax({

        url:
            API_BASE +
            '/user.php?id=' +
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

        loadUsers();

    });
}

$(document).ready(function(){

    $('#menuContainer').load(
        'partials/admin-menu.html'
    );

});