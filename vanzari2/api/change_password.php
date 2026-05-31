<?php

require '../config/config.php';

require '../classes/Database.php';
require '../classes/Auth.php';
require '../classes/Response.php';

$user =
    Auth::validate();

if(!$user)
{
    Response::json([
        'success'=>false
    ],401);
}

$data =
    json_decode(
        file_get_contents(
            'php://input'
        ),
        true
    );

$db =
    Database::getConnection();

$stmt =
    $db->prepare(
    "
    SELECT password_hash
    FROM users
    WHERE id=?
    "
);

$stmt->bind_param(
    'i',
    $user['id']
);

if(!$stmt->execute()){
    throw new Exception($stmt->error);
};

$current =
    $stmt
    ->get_result()
    ->fetch_assoc();

if(
    !password_verify(
        $data['current_password'],
        $current['password_hash']
    )
)
{
    Response::json([
        'success'=>false,
        'message'=>
            'Invalid password'
    ],400);
}

$newHash =
    password_hash(
        $data['new_password'],
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
    $newHash,
    $user['id']
);

if(!$stmt->execute()){
    throw new Exception($stmt->error);
};

Response::json([
    'success'=>true
]);