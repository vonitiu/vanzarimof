<?php

require 'config/config.php';
require 'classes/Database.php';

try
{
    $db = Database::getConnection();

    echo "Database connection OK<br>";

    $result = $db->query(
        "SELECT NOW() current_time"
    );

    $row = $result->fetch_assoc();

    echo "Server time: " .
         $row['current_time'];
}
catch(Exception $e)
{
    echo "Database error:<br>";
    echo $e->getMessage();
}