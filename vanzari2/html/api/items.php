<?php

require '../config/config.php';
require '../classes/Database.php';
require '../classes/Auth.php';
require '../classes/Response.php';
require '../classes/Item.php';

$user =
    Auth::validate();

if (!$user)
{
    Response::json([
        'success' => false
    ],401);
}

$offerId =
    (int)(
        $_GET['offer']
        ?? 0
    );

$item =
    new Item();

$data =
    $item->getByOffer(
        $offerId
    );

Response::json([
    'success' => true,
    'data' => $data
]);