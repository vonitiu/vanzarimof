<?php

require '../vendor/autoload.php';

require '../config/config.php';
require '../config/mail.php';

require '../classes/Database.php';
require '../classes/Auth.php';
require '../classes/Response.php';
require '../classes/Mailer.php';

$user =
    Auth::validate();

if (!$user)
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

try
{
    $mailer =
        new Mailer();

    $mailer->sendOffer(
        (int)$data['offer_id'],
        $data['recipient'],
        $data['subject'],
        $data['body'],
        $user['username']
    );

    Response::json([
        'success'=>true
    ]);
}
catch(Exception $e)
{
    Response::json([
        'success'=>false,
        'message'=>$e->getMessage()
    ],500);
}