<?php

require '../config/config.php';
require '../classes/Database.php';
require '../classes/Auth.php';
require '../classes/Response.php';

$user = Auth::validate();

if(!$user)
{
    Response::json([
        'success'=>false
    ],401);
}

$term = trim(
    $_GET['q'] ?? ''
);

if(strlen($term) < 2)
{
    Response::json([
        'success'=>true,
        'data'=>[]
    ]);
}

$db = Database::getConnection();

$stmt = $db->prepare(
    "
    SELECT

        id,
        firma,
        responsabil,
        departament,
        discount

    FROM clienti

    WHERE firma LIKE ?

    ORDER BY firma

    LIMIT 10
    "
);

$search = '%' . $term . '%';

$stmt->bind_param(
    's',
    $search
);

$stmt->execute();

$result =
    $stmt->get_result();

Response::json([
    'success'=>true,
    'data'=>
        $result->fetch_all(
            MYSQLI_ASSOC
        )
]);