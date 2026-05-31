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