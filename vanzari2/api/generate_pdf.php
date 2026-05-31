<?php

require '../vendor/autoload.php';

require '../config/config.php';
require '../classes/Database.php';
require '../classes/Auth.php';
require '../classes/Response.php';
require '../classes/PdfGenerator.php';

$user =
    Auth::validate();

if(!$user)
{
    Response::json([
        'success'=>false
    ],401);
}

$offerId =
    (int)(
        $_GET['id']
        ?? 0
    );

$pdf =
    new PdfGenerator();

$result =
    $pdf->generate(
        $offerId
    );

Response::json([
    'success'=>true,
    'pdf'=>$result['pdf']
]);