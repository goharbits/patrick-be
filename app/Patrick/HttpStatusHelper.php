<?php

namespace App\Patrick;

class HttpStatusHelper
{
    // Success 2xx
    const OK = 200;
    const CREATED = 201;
    const ACCEPTED = 202;
    const NO_CONTENT = 204;

    // Client Error 4xx
    const BAD_REQUEST = 400;
    const UNAUTHORIZED = 401;
    const FORBIDDEN = 403;
    const NOT_FOUND = 404;
    const METHOD_NOT_ALLOWED = 405;
    const CONFLICT = 409;

    // Server Error 5xx
    const INTERNAL_SERVER_ERROR = 500;

    private static $messages = [
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        204 => 'No Content',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        409 => 'Conflict',
        500 => 'Internal Server Error',
    ];

    public static function getMessage(int $statusCode): string
    {
        return self::$messages[$statusCode] ?? 'Unknown Status Code';
    }

    public static function response(int $statusCode, $data = null)
    {
        return response()->json([
            'status' => $statusCode,
            'message' => self::getMessage($statusCode),
            'data' => $data,
        ], $statusCode);
    }

    public static function successResponse(int $statusCode, $message,$data = null)
    {
        return response()->json([
            'status' => $statusCode,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

     public static function errorResponse(int $statusCode,$error, $data = null)
    {
        return response()->json([
            'status' => $statusCode,
            'message' => $error,
            'data' => $data,
        ], $statusCode);
    }
}
