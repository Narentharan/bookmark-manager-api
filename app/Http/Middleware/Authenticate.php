<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    protected function redirectTo($request)
    {
        if (!$request->expectsJson()) {
            abort(response()->json([
                'status' => 'fail',
                'message' => 'Unauthorized access. Please login.'
            ], 401));
        }
    }
}
