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

$db =
    Database::getConnection();

$offerId =
    (int)(
        $_GET['offer']
        ?? 0
    );

$stmt =
    $db->prepare(
    "
    SELECT *
    FROM email_history
    WHERE offer_id=?
    ORDER BY sent_at DESC
    "
);

$stmt->bind_param(
    'i',
    $offerId
);

if(!$stmt->execute()){
    throw new Exception($stmt->error);
}

Response::json([
    'success'=>true,
    'data'=>
        $stmt
        ->get_result()
        ->fetch_all(
            MYSQLI_ASSOC
        )
]);