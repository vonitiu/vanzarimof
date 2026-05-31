<?php

require '../config/config.php';
require '../classes/Database.php';
require '../classes/Response.php';
require '../classes/Auth.php';

$data = json_decode(
    file_get_contents("php://input"),
    true
);

$username = trim(
    $data['username'] ?? ''
);

$password = trim(
    $data['password'] ?? ''
);

$db = Database::getConnection();

$stmt = $db->prepare(
    "SELECT *
     FROM users2
     WHERE username=?
     AND active=1"
);

$stmt->bind_param(
    "s",
    $username
);

if(!$stmt->execute()){
    throw new Exception($stmt->error);
}

$user = $stmt
    ->get_result()
    ->fetch_assoc();

if (!$user)
{
    Response::json([
        "success" => false,
        "message" => "Invalid credentials"
    ], 401);
}

if (
    !password_verify(
        $password,
        $user['password_hash']
    )
)
{
    Response::json([
        "success" => false,
        "message" => "Invalid credentials"
    ], 401);
}

/*
|--------------------------------------------------------------------------
| Update Last Login
|--------------------------------------------------------------------------
*/

$stmt = $db->prepare(
    "
    UPDATE users2
    SET last_login = NOW()
    WHERE id = ?
    "
);

$stmt->bind_param(
    "i",
    $user['id']
);

if(!$stmt->execute()){
    throw new Exception($stmt->error);
}

/*
|--------------------------------------------------------------------------
| Create Token
|--------------------------------------------------------------------------
*/

$token = Auth::createToken(
    $user['id']
);

Response::json([
    "success" => true,
    "token" => $token,
    "user" => [
        "id"        => $user['id'],
        "username"  => $user['username'],
        "name"      => $user['full_name'],
        "role"      => $user['role']
    ]
]);