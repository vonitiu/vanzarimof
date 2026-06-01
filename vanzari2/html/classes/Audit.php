<?php

class Audit
{
    public static function log(
        ?int $userId,
        string $userName,
        string $action,
        string $details = ''
    ): void
    {
        $db =
            Database::getConnection();

        $stmt =
            $db->prepare(
            "
            INSERT INTO audit_logs
            (
                user_id,
                user_name,
                action,
                details
            )
            VALUES
            (
                ?,?,?,?
            )
            "
        );

        $stmt->bind_param(
            'isss',
            $userId,
            $userName,
            $action,
            $details
        );

        if(!$stmt->execute()){
    throw new Exception($stmt->error);
}
    }
}