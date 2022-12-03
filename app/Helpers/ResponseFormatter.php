<?php

namespace App\Helpers;

class ResponseFormatter
{
    /**
     * 
     */
    protected static $response = [
        'meta' => [
            'code' => null,
            'success' => true,
        ],
        'result' => null,
    ];

    public static function success($data, $message = null, $code = 200)
    {
        if (!is_null($message)) {
            self::$response['meta']['message'] = $message;
        }

        self::$response['meta']['code'] = $code;
        self::$response['result'] = $data;
        return self::$response;
    }

    public static function error($message = null, $code = 500)
    {
        if (!is_null($message)) {
            self::$response['meta']['message'] = $message;
        }

        self::$response['meta']['status'] = false;
        self::$response['meta']['code'] = $code;
        self::$response['result'] = null;
        return self::$response;
    }
}
