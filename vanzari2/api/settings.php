<?php

require '../config/config.php';

require '../classes/Database.php';
require '../classes/Auth.php';
require '../classes/Response.php';
require '../classes/Settings.php';

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

switch(
    $_SERVER['REQUEST_METHOD']
)
{
    case 'GET':

        Response::json([
            'success'=>true,
            'data'=>
                Settings::getAll()
        ]);

        break;

    case 'POST':

        $data =
            json_decode(
                file_get_contents(
                    'php://input'
                ),
                true
            );

        foreach(
            $data as $key=>$value
        )
        {
            Settings::set(
                $key,
                $value
            );
        }

        Response::json([
            'success'=>true
        ]);

        break;
}