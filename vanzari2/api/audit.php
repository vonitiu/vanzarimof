<?php

require '../config/config.php';

require '../classes/Database.php';
require '../classes/Auth.php';
require '../classes/Response.php';

$user =
    Auth::validate();

if(!$user)
{
    Response::json([
        'success'=>false
    ],401);
}

$db =
    Database::getConnection();

$where = [];
$params = [];
$types = '';

if(!empty($_GET['user']))
{
    $where[] =
        'user_name=?';

    $params[] =
        $_GET['user'];

    $types .= 's';
}

if(!empty($_GET['action']))
{
    $where[] =
        'action=?';

    $params[] =
        $_GET['action'];

    $types .= 's';
}

if(!empty($_GET['date_from']))
{
    $where[] =
        'DATE(created_at)>=?';

    $params[] =
        $_GET['date_from'];

    $types .= 's';
}

if(!empty($_GET['date_to']))
{
    $where[] =
        'DATE(created_at)<=?';

    $params[] =
        $_GET['date_to'];

    $types .= 's';
}

$sql =
"
SELECT *
FROM audit_logs
";

if(count($where))
{
    $sql .=
        ' WHERE ' .
        implode(
            ' AND ',
            $where
        );
}

$sql .=
"
ORDER BY created_at DESC
LIMIT 500
";

$stmt =
    $db->prepare(
        $sql
    );

if(count($params))
{
    $stmt->bind_param(
        $types,
        ...$params
    );
}

if(!$stmt->execute()){
    throw new Exception($stmt->error);
}

Response::json([
    'success'=>true,
    'data'=>
        $stmt
        ->get_result()
        ->fetch_all(
            MYSQLI_ASSOC
        )
]);