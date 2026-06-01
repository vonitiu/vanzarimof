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

$sql = "

SELECT

    DATE_FORMAT(
        data,
        '%Y-%m'
    ) month,

    COUNT(*) offers,

    SUM(
        offer_total
    ) total

FROM vw_offer_summary

GROUP BY month

ORDER BY month

";

$result =
    $db->query($sql);

Response::json([
    'success'=>true,
    'data'=>
        $result->fetch_all(
            MYSQLI_ASSOC
        )
]);