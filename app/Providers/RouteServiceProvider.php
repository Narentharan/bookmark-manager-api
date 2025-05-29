<?php 
namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;

class RouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        RateLimiter::for('role_based', function (Request $request) {
            if ($request->user()?->role === 'admin') {
                return Limit::perMinute(100)->by($request->user()->id);
            }

            if ($request->user()) {
                return Limit::perMinute(60)->by($request->user()->id);
            }

            return Limit::perMinute(30)->by($request->ip());
        });
    }
}
