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


$(document).ready(function(){

    if(
        !requireAuth()
    )
    {
        return;
    }
    
});