<?php

require '../config/config.php';
require '../classes/Database.php';
require '../classes/Auth.php';
require '../classes/Response.php';

$user = Auth::validate();

if (!$user)
{
    Response::json([
        "success"=>false
    ],401);
}

Response::json([
    "success"=>true,
    "user"=>$user
]);