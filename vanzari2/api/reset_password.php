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

$data =
    json_decode(
        file_get_contents(
            'php://input'
        ),
        true
    );

$userId =
    (int)$data['user_id'];

$tempPassword =
    strtoupper(
        bin2hex(
            random_bytes(4)
        )
    );

$passwordHash =
    password_hash(
        $tempPassword,
        PASSWORD_DEFAULT
    );

$stmt =
    $db->prepare(
    "
    UPDATE users
    SET password_hash=?
    WHERE id=?
    "
);

$stmt->bind_param(
    'si',
    $passwordHash,
    $userId
);

if(!$stmt->execute()){
    throw new Exception($stmt->error);
};

Response::json([
    'success'=>true,
    'temporary_password'=>
        $tempPassword
]);