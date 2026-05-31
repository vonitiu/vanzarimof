<?php

$file =
    basename(
        $_GET['file']
    );

$path =
    __DIR__ .
    '/../storage/exports/' .
    $file;

if(!file_exists($path))
{
    http_response_code(
        404
    );

    exit;
}

header(
    'Content-Type:
    application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
);

header(
    'Content-Disposition:
    attachment;
    filename="'.$file.'"'
);

readfile($path);