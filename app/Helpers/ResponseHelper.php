<?php

namespace App\Helpers;

class ResponseHelper
{

    public static function success($data = [], $message = 'Operation successful', $statusCode = 200)
    {
        $response = [
            'message' => $message,
            'status' => 'success',
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }
        return response()->json($response, $statusCode);
    }

    public static function error($message = 'Something went wrong', $statusCode = 500, $errorDetails = [])
    {
        return response()->json([
            'message' => $message,
            'status' => 'error',
            'errors' => $errorDetails,
        ], $statusCode);
    }

    public static function validationFailed($errors, $message = 'Validation failed')
    {
        return response()->json([
            'message' => $message,
            'status' => 'failed',
            'errors' => $errors,
        ], 422);
    }

    public static function unauthorized($message = 'Unauthorized access')
    {
        return response()->json([
            'message' => $message,
            'status' => 'failed',
        ], 401);
    }
}
