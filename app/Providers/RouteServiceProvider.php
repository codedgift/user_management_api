<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('login_register', function (Request $request) {
            // Less allowance during night hours
            $hour = now()->hour;
            $limit = ($hour >= 21 || $hour < 6) ? 3 : 5;
        
            return Limit::perMinute($limit)->by($request->user()?->id ?: $request->user()?->email ?: $request->ip())->response(function(Request $request, array $headers) {
                $retryAfter = $headers['Retry-After'] ?? null;
                return response()->json(['message' => 'Too many requests, please slow down.', 'retryAfter' => $retryAfter], 429);
            });
        });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api/v1')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }
}
