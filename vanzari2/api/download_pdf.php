<?php

require '../config/config.php';
require '../classes/Database.php';

$id =
    (int)(
        $_GET['id']
        ?? 0
    );

$db =
    Database::getConnection();

$stmt =
    $db->prepare(
    "
    SELECT pdf_file
    FROM oferte
    WHERE deleted=0 and id=?
    "
);

$stmt->bind_param(
    'i',
    $id
);

if(!$stmt->execute()){
    throw new Exception($stmt->error);
}

$row =
    $stmt
    ->get_result()
    ->fetch_assoc();

if(!$row)
{
    http_response_code(
        404
    );
    exit;
}

$file =
    __DIR__ .
    '/../storage/pdfs/' .
    $row['pdf_file'];

if(!file_exists($file))
{
    http_response_code(
        404
    );
    exit;
}

header(
    'Content-Type: application/pdf'
);

header(
    'Content-Disposition:inline'
);

readfile($file);