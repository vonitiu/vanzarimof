<?php

class Database
{
    private static $instance = null;

    public static function getConnection()
    {
        if (self::$instance === null)
        {
            self::$instance = new mysqli(
                DB_HOST,
                DB_USER,
                DB_PASS,
                DB_NAME
            );

            if (self::$instance->connect_error)
            {
                die("Database connection failed");
            }

            self::$instance->set_charset("utf8mb4");
        }

        return self::$instance;
    }
}