<?php

class Response
{
    public static function json($data, $code = 200)
    {
        http_response_code($code);

        header('Content-Type: application/json');

        echo json_encode($data);

        exit;
    }

    public static function exception(    Exception $e        )
    {
        self::json([
            'success'=>false,
            'message'=>$e->getMessage()
        ],500);
    }
}