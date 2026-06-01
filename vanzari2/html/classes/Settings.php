<?php

class Settings
{
    private static function db()
    {
        return Database::getConnection();
    }

    public static function get(
        string $key,
        string $default = ''
    ): string
    {
        $db = self::db();

        $stmt = $db->prepare(
            "SELECT setting_value
             FROM app_settings
             WHERE setting_key=?"
        );

        $stmt->bind_param(
            's',
            $key
        );

        if(!$stmt->execute()){
    throw new Exception($stmt->error);
}

        $result =
            $stmt->get_result();

        if($row = $result->fetch_assoc())
        {
            return $row['setting_value'];
        }

        return $default;
    }

    public static function set(
        string $key,
        string $value
    ): bool
    {
        $db = self::db();

        $stmt = $db->prepare(
            "
            INSERT INTO app_settings
            (
                setting_key,
                setting_value
            )
            VALUES
            (
                ?,?
            )
            ON DUPLICATE KEY UPDATE
            setting_value=VALUES(setting_value)
            "
        );

        $stmt->bind_param(
            'ss',
            $key,
            $value
        );

        return $stmt->execute();
    }

    public static function getAll(): array
    {
        $db = self::db();

        $result = $db->query(
            "
            SELECT *
            FROM app_settings
            ORDER BY setting_key
            "
        );

        $settings = [];

        while(
            $row =
            $result->fetch_assoc()
        )
        {
            $settings[
                $row['setting_key']
            ] =
                $row['setting_value'];
        }

        return $settings;
    }
}