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

$item =
    new Item();

switch(
    $_SERVER['REQUEST_METHOD']
)
{
    case 'GET':

        $id =
            (int)(
                $_GET['id']
                ?? 0
            );

        $data =
            $item->getById(
                $id
            );

        Response::json([
            'success' => true,
            'data' => $data
        ]);

        break;

    case 'POST':

        $payload =
            json_decode(
                file_get_contents(
                    'php://input'
                ),
                true
            );

        $id =
            $item->create(
                $payload
            );

        $item->updateOfferTotal(
            $payload['oferta']
        );

        Response::json([
            'success' => true,
            'id' => $id
        ]);

        break;

    case 'PUT':

        $id =
            (int)(
                $_GET['id']
                ?? 0
            );

        $payload =
            json_decode(
                file_get_contents(
                    'php://input'
                ),
                true
            );

        $existing =
            $item->getById(
                $id
            );

        if (!$existing)
        {
            Response::json([
                'success' => false,
                'message' =>
                    'Item not found'
            ],404);
        }

        $item->update(
            $id,
            $payload
        );

        $item->updateOfferTotal(
            $existing['oferta']
        );

        Response::json([
            'success' => true
        ]);

        break;

    case 'DELETE':

        $id =
            (int)(
                $_GET['id']
                ?? 0
            );

        $existing =
            $item->getById(
                $id
            );

        if (!$existing)
        {
            Response::json([
                'success' => false
            ],404);
        }

        $item->delete(
            $id
        );

        $item->updateOfferTotal(
            $existing['oferta']
        );

        Response::json([
            'success' => true
        ]);

        break;
}