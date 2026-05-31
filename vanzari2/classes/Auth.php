<?php

class Auth
{
    public static function generateToken()
    {
        return bin2hex(random_bytes(32));
    }

    public static function createToken($userId)
    {
        $db = Database::getConnection();

        $token = self::generateToken();

        $expires = date(
            'Y-m-d H:i:s',
            strtotime('+30 days')
        );

        $stmt = $db->prepare(
            "INSERT INTO auth_tokens
            (token,user_id,expires_at)
            VALUES (?,?,?)"
        );

        $stmt->bind_param(
            "sis",
            $token,
            $userId,
            $expires
        );

        $stmt->execute();

        return $token;
    }

    public static function validate()
    {
        $headers = getallheaders();

        if (!isset($headers['Authorization']))
        {
            return false;
        }

        $auth = $headers['Authorization'];

        $token = str_replace(
            'Bearer ',
            '',
            $auth
        );

        $db = Database::getConnection();

        $stmt = $db->prepare(
            "SELECT
                u.*
            FROM auth_tokens t
            JOIN users2 u
                ON u.id=t.user_id
            WHERE
                t.token=?
                AND t.expires_at > NOW()
        ");

        $stmt->bind_param("s", $token);

        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows === 0)
        {
            return false;
        }

        return $result->fetch_assoc();
    }
}