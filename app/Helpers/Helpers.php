<?php

namespace App\Helpers;

if (!function_exists('successResponse')) {
    function successResponse(mixed $data, $message = 'success', $code = 200) {
        return response()->json([
            'data' => $data,
            'message' => $message,
            'code' => $code,
        ], $code);
    }
}