<?php

require '../config/config.php';
require '../classes/Database.php';
require '../classes/Auth.php';
require '../classes/Response.php';
require '../classes/Offer.php';

$user = Auth::validate();

if (!$user)
{
    Response::json([
        'success' => false
    ],401);
}

$id = (int)($_GET['id'] ?? 0);

$offer = new Offer();

$newId = $offer->duplicate(
    $id,
    $user['username']
);

Response::json([
    'success' => true,
    'new_offer_id' => $newId
]);