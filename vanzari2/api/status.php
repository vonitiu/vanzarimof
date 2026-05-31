<?php

require '../config/config.php';

require '../classes/Database.php';
require '../classes/Auth.php';
require '../classes/Response.php';

$user =
    Auth::validate();

if(
    !$user ||
    $user['role'] !== 'admin'
)
{
    Response::json([
        'success'=>false
    ],403);
}

$status = [];

try
{
    $db =
        Database::getConnection();

    $db->query(
        'SELECT 1'
    );

    $status['database'] =
        'OK';
}
catch(Exception $e)
{
    $status['database'] =
        'ERROR';
}

$pdfFolder =
    __DIR__ .
    '/../storage/pdfs';

$htmlFolder =
    __DIR__ .
    '/../storage/html';

$exportFolder =
    __DIR__ .
    '/../storage/exports';

$status['pdf_folder'] =
    is_dir(
        $pdfFolder
    )
        ? 'OK'
        : 'MISSING';

$status['html_folder'] =
    is_dir(
        $htmlFolder
    )
        ? 'OK'
        : 'MISSING';

$status['export_folder'] =
    is_dir(
        $exportFolder
    )
        ? 'OK'
        : 'MISSING';

$status['php_version'] =
    PHP_VERSION;

$status['disk_free_gb'] =
    round(
        disk_free_space('/') /
        1024 /
        1024 /
        1024,
        2
    );

$status['disk_total_gb'] =
    round(
        disk_total_space('/') /
        1024 /
        1024 /
        1024,
        2
    );

Response::json([
    'success'=>true,
    'data'=>$status
]);