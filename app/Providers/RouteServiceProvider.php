<?php

namespace App\Providers;

// Import class-class yang dibutuhkan
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

// Mendefinisikan class RouteServiceProvider yang mewarisi ServiceProvider
class RouteServiceProvider extends ServiceProvider
{
    /**
     * Path ke route "home" aplikasi.
     * Biasanya, user akan diarahkan ke sini setelah login.
     */
    public const HOME = '/dashboard';

    /**
     * Method utama untuk konfigurasi route, binding model, dan filter pola.
     */
    public function boot(): void
    {
        // Membatasi rate limit untuk route 'api'
        RateLimiter::for('api', function (Request $request) {
            // Maksimal 60 request per menit per user (atau per IP jika belum login)
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // Mendefinisikan group route
        $this->routes(function () {
            // Group route dengan middleware 'api' dan prefix 'api'
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            // Group route dengan middleware 'web'
            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }
}
