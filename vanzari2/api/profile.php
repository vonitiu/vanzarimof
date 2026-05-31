<?php

require '../config/config.php';
require '../classes/Database.php';
require '../classes/Auth.php';
require '../classes/Response.php';

$user = Auth::validate();

if(!$user)
{
    Response::json([
        'success'=>false
    ],401);
}

$db = Database::getConnection();

switch($_SERVER['REQUEST_METHOD'])
{
    case 'GET':

        $stmt = $db->prepare(
            "
            SELECT
                id,
                username,
                full_name,
                email,
                role
            FROM users
            WHERE id=?
            "
        );

        $stmt->bind_param(
            'i',
            $user['id']
        );

        if(!$stmt->execute()){
    throw new Exception($stmt->error);
};

        Response::json([
            'success'=>true,
            'data'=>
                $stmt
                ->get_result()
                ->fetch_assoc()
        ]);

        break;

    case 'PUT':

        $data =
            json_decode(
                file_get_contents(
                    'php://input'
                ),
                true
            );

        $stmt =
            $db->prepare(
            "
            UPDATE users
            SET
                full_name=?,
                email=?
            WHERE id=?
            "
        );

        $stmt->bind_param(
            'ssi',
            $data['full_name'],
            $data['email'],
            $user['id']
        );

        if(!$stmt->execute()){
    throw new Exception($stmt->error);
};

        Response::json([
            'success'=>true
        ]);

        break;
}