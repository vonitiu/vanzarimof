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
    ], 401);
}

$offer = new Offer();

$data = $offer->getAll(
    $_GET['search'] ?? '',
    $_GET['status'] ?? '',
    $_GET['date_from'] ?? '',
    $_GET['date_to'] ?? ''
);

Response::json([
    'success' => true,
    'data' => $data
]);