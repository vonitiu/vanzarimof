<?php

require '../config/config.php';

require '../classes/Database.php';
require '../classes/Auth.php';
require '../classes/Response.php';

$user =
    Auth::validate();

if(
    !$user ||
    $user['role'] !== 'admin'
)
{
    Response::json([
        'success'=>false
    ],403);
}

$db =
    Database::getConnection();

$result =
    $db->query(
    "
    SELECT
        id,
        username,
        full_name,
        email,
        role,
        active,
        last_login
    FROM users2

    ORDER BY username
    "
);

Response::json([
    'success'=>true,
    'data'=>
        $result->fetch_all(
            MYSQLI_ASSOC
        )
]);