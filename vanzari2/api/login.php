<?php

require '../config/config.php';

require '../classes/Database.php';
require '../classes/Response.php';
require '../classes/Auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST')
{
    Response::json([
        'success' => false,
        'message' => 'Method not allowed'
    ], 405);
}

$data = json_decode(
    file_get_contents('php://input'),
    true
);

$username = trim(
    $data['username'] ?? ''
);

$password = trim(
    $data['password'] ?? ''
);

if(
    empty($username) ||
    empty($password)
)
{
    Response::json([
        'success' => false,
        'message' => 'Username and password required'
    ],400);
}

try
{
    $db =
        Database::getConnection();

    $stmt =
        $db->prepare(
        "
        SELECT *
        FROM users
        WHERE username=?
        LIMIT 1
        "
    );

    $stmt->bind_param(
        's',
        $username
    );

    if(!$stmt->execute())
    {
        throw new Exception(
            $stmt->error
        );
    }

    $user =
        $stmt
        ->get_result()
        ->fetch_assoc();

    if(!$user)
    {
        Response::json([
            'success' => false,
            'message' =>
                'Invalid username or password'
        ],401);
    }

    /*
    |--------------------------------------------------------------------------
    | Active account check
    |--------------------------------------------------------------------------
    */

    if(
        isset($user['active']) &&
        (int)$user['active'] !== 1
    )
    {
        Response::json([
            'success' => false,
            'message' =>
                'Account disabled'
        ],403);
    }

    /*
    |--------------------------------------------------------------------------
    | Lockout check
    |--------------------------------------------------------------------------
    */

    if(
        !empty(
            $user['locked_until']
        )
    )
    {
        if(
            strtotime(
                $user['locked_until']
            ) > time()
        )
        {
            Response::json([
                'success' => false,
                'message' =>
                    'Account locked. Try again later.'
            ],423);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Password validation
    |--------------------------------------------------------------------------
    */

    if(
        !password_verify(
            $password,
            $user['password_hash']
        )
    )
    {
        if(
            isset(
                $user['failed_login_count']
            )
        )
        {
            $stmt =
                $db->prepare(
                "
                UPDATE users
                SET failed_login_count =
                    failed_login_count + 1
                WHERE id=?
                "
            );

            $stmt->bind_param(
                'i',
                $user['id']
            );

            $stmt->execute();

            $newCount =
                $user['failed_login_count']
                + 1;

            if($newCount >= 5)
            {
                $stmt =
                    $db->prepare(
                    "
                    UPDATE users
                    SET
                        locked_until =
                            DATE_ADD(
                                NOW(),
                                INTERVAL 30 MINUTE
                            )
                    WHERE id=?
                    "
                );

                $stmt->bind_param(
                    'i',
                    $user['id']
                );

                $stmt->execute();
            }
        }

        Response::json([
            'success' => false,
            'message' =>
                'Invalid username or password'
        ],401);
    }

    /*
    |--------------------------------------------------------------------------
    | Successful login
    |--------------------------------------------------------------------------
    */

    if(
        isset(
            $user['failed_login_count']
        )
    )
    {
        $stmt =
            $db->prepare(
            "
            UPDATE users
            SET
                failed_login_count = 0,
                locked_until = NULL,
                last_login = NOW()
            WHERE id=?
            "
        );

        $stmt->bind_param(
            'i',
            $user['id']
        );

        $stmt->execute();
    }
    else
    {
        $stmt =
            $db->prepare(
            "
            UPDATE users
            SET
                last_login = NOW()
            WHERE id=?
            "
        );

        $stmt->bind_param(
            'i',
            $user['id']
        );

        $stmt->execute();
    }

    /*
    |--------------------------------------------------------------------------
    | Create token
    |--------------------------------------------------------------------------
    */

    $token =
        Auth::createToken(
            $user['id']
        );

    /*
    |--------------------------------------------------------------------------
    | Response
    |--------------------------------------------------------------------------
    */

    Response::json([

        'success' => true,

        'token' => $token,

        'user' => [

            'id' =>
                $user['id'],

            'username' =>
                $user['username'],

            'full_name' =>
                $user['full_name']
                ?? '',

            'email' =>
                $user['email']
                ?? '',

            'role' =>
                $user['role']
                ?? 'sales'
        ]

    ]);
}
catch(Exception $e)
{
    Response::json([
        'success' => false,
        'message' => $e->getMessage()
    ],500);
}