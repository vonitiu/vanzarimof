<?php

require '../config/config.php';

require '../classes/Database.php';
require '../classes/Auth.php';
require '../classes/Response.php';

$currentUser =
    Auth::validate();

if(
    !$currentUser ||
    $currentUser['role'] !== 'admin'
)
{
    Response::json([
        'success'=>false
    ],403);
}

$db =
    Database::getConnection();

switch(
    $_SERVER['REQUEST_METHOD']
)
{
    case 'GET':

        $id =
            (int)(
                $_GET['id']
                ?? 0
            );

        $stmt =
            $db->prepare(
            "
            SELECT

                id,
                username,
                full_name,
                email,
                role,
                active

            FROM users

            WHERE id=?
            "
        );

        $stmt->bind_param(
            'i',
            $id
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

    case 'POST':

        $data =
            json_decode(
                file_get_contents(
                    'php://input'
                ),
                true
            );

        $password =
            password_hash(
                $data['password'],
                PASSWORD_DEFAULT
            );

        $stmt =
            $db->prepare(
            "
            INSERT INTO users
            (
                username,
                password_hash,
                full_name,
                email,
                role,
                active
            )
            VALUES
            (
                ?,?,?,?,?,?
            )
            "
        );

        $stmt->bind_param(
            'sssssi',

            $data['username'],
            $password,
            $data['full_name'],
            $data['email'],
            $data['role'],
            $data['active']
        );

        if(!$stmt->execute()){
    throw new Exception($stmt->error);
};

        Response::json([
            'success'=>true,
            'id'=>$db->insert_id
        ]);

        break;

    case 'PUT':

        $id =
            (int)(
                $_GET['id']
                ?? 0
            );

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
                email=?,
                role=?,
                active=?
            WHERE id=?
            "
        );

        $stmt->bind_param(
            'sssii',

            $data['full_name'],
            $data['email'],
            $data['role'],
            $data['active'],
            $id
        );

        if(!$stmt->execute()){
    throw new Exception($stmt->error);
};

        Response::json([
            'success'=>true
        ]);

        break;

    case 'DELETE':

        $id =
            (int)(
                $_GET['id']
                ?? 0
            );

        $stmt =
            $db->prepare(
            "
            UPDATE users
            SET active=0
            WHERE id=?
            "
        );

        $stmt->bind_param(
            'i',
            $id
        );

        if(!$stmt->execute()){
    throw new Exception($stmt->error);
};

        Response::json([
            'success'=>true
        ]);

        break;
}