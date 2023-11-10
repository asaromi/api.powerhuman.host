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

        $code = (int) $code;
        if ($code < 400) $code = 400;
        else if ($code > 505) $code = 500;

        self::$response['meta']= [
            'code' => $code,
            'success' => false
        ];
        self::$response['result'] = $result;
        return response(self::$response, $code);
    }

    public function notFound()
    {
        self::$response['meta'] = [
            'code' => 404,
            'success' => false,
            'message' => "Endpoint Not Found"
        ];

        return response(self::$response, 404);
    }
}
