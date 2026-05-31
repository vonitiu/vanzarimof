<?php

require '../vendor/autoload.php';

require '../config/config.php';

require '../classes/Database.php';
require '../classes/Auth.php';
require '../classes/Response.php';
require '../classes/ExcelExporter.php';

$user =
    Auth::validate();

if(!$user)
{
    Response::json([
        'success'=>false
    ],401);
}

$exporter =
    new ExcelExporter();

$file =
    $exporter
    ->exportOffers();

Response::json([
    'success'=>true,
    'file'=>$file
]);