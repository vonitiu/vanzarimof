<?php

require '../config/config.php';

require '../classes/Database.php';
require '../classes/Auth.php';
require '../classes/Response.php';

$headers = getallheaders();

if(
    !isset(
        $headers['Authorization']
    )
)
{
    Response::json([
        'success'=>true
    ]);
}

$token =
    str_replace(
        'Bearer ',
        '',
        $headers['Authorization']
    );

$db =
    Database::getConnection();

$stmt =
    $db->prepare(
    "
    DELETE
    FROM auth_tokens
    WHERE token=?
    "
);

$stmt->bind_param(
    's',
    $token
);

$stmt->execute();

Response::json([
    'success'=>true
]);