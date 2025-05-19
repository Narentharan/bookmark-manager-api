<?php 
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && auth()->user()->role === 'admin') {
            return $next($request);
        }

        return response()->json([
            'status' => 'fail',
            'message' => 'Unauthorized. Admins only.'
        ], 403);
    }
}
