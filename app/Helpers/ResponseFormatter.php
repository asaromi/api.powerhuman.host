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

        self::$response['meta']['code'] = $code ?? 200;
        self::$response['result'] = $data;
        return response(self::$response, $code);
    }

    public static function error($message = null, $code = 500, $result = null)
    {
        if (!is_null($message)) {
            self::$response['meta']['message'] = $message;
        }

        self::$response['meta']['success'] = false;
        self::$response['meta']['code'] = $code ?? 500;
        self::$response['result'] = $result;
        return response(self::$response, $code);
    }
}
