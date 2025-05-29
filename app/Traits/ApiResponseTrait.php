<?php

namespace App\Traits;

trait ApiResponseTrait
{
    public function success($data = null, $message = null, $code = 200)
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ], $code);
    }

    public function fail($message = null, $code = 400, $data = null)
    {
        return response()->json([
            'status' => 'fail',
            'message' => $message,
            'data' => $data
        ], $code);
    }

    public function error($message = 'Something went wrong', $code = 500)
    {
        return response()->json([
            'status' => 'error',
            'message' => $message
        ], $code);
    }
}
