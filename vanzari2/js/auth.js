function saveToken(token)
{
    localStorage.setItem(
        'token',
        token
    );
}

function logout()
{
    api('/logout.php','POST')
    
    localStorage.removeItem(
        'token'
    );

    location.href =
        'login.html';
}

function requireAuth()
{
    const currentPage =
        window.location.pathname
            .split('/')
            .pop()
            .toLowerCase();

    if(
        currentPage === 'login.html'
    )
    {
        return true;
    }

    const token =
        localStorage.getItem(
            'token'
        );

    if(!token)
    {
        location.href =
            'login.html';

        return false;
    }

    return true;
}

function getCurrentUser()
{
    const user =
        localStorage.getItem(
            'user'
        );

    if(!user)
    {
        return null;
    }

    return JSON.parse(user);
}

function hasRole(roles)
{
    const user =
        getCurrentUser();

    if(!user)
    {
        return false;
    }

    return roles.includes(
        user.role
    );
}

function requireRoles(roles)
{
    const user =
        getCurrentUser();

    if(
        !user ||
        !roles.includes(
            user.role
        )
    )
    {
        location.href =
            'dashboard.html';

        return false;
    }

    return true;
}
$(document).ready(function(){

    if(
        !requireAuth()
    )
    {
        return;
    }
    
});