<?php

require '../config/config.php';
require '../classes/Database.php';

$headers = getallheaders();

$token = str_replace(
    'Bearer ',
    '',
    $headers['Authorization']
);

$db = Database::getConnection();

$stmt = $db->prepare(
    "DELETE FROM auth_tokens
    WHERE token=?"
);

$stmt->bind_param(
    "s",
    $token
);

if(!$stmt->execute()){
    throw new Exception($stmt->error);
};

echo json_encode([
    "success"=>true
]);