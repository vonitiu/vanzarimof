<?php

require '../config/config.php';
require '../classes/Database.php';
require '../classes/Auth.php';
require '../classes/Audit.php';
require '../classes/Response.php';
require '../classes/Offer.php';

$user = Auth::validate();

if (!$user)
{
    Response::json([
        'success' => false
    ],401);
}

$offer = new Offer();

switch ($_SERVER['REQUEST_METHOD'])
{
    case 'GET':

        $id = (int)($_GET['id'] ?? 0);

        Response::json([
            'success' => true,
            'data' => $offer->getById($id)
        ]);

        break;

    case 'POST':

        Audit::log(
            $user['id'],
            $user['username'],
            'CREATE_OFFER',
            'Offer #' . $offerNumber
        );

        $payload = json_decode(
            file_get_contents('php://input'),
            true
        );

        $payload['createdby'] =
            $user['username'];

        $id = $offer->create($payload);

        Response::json([
            'success' => true,
            'id' => $id
        ]);

        break;

    case 'PUT':

        $id = (int)($_GET['id'] ?? 0);

        $payload = json_decode(
            file_get_contents('php://input'),
            true
        );

        $payload['updatedBy'] =
            $user['username'];

        $offer->update(
            $id,
            $payload
        );

        Response::json([
            'success' => true
        ]);

        break;

    case 'DELETE':

        $id = (int)($_GET['id'] ?? 0);

        $offer->delete($id);

        Response::json([
            'success' => true
        ]);

        break;
}