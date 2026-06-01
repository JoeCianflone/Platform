<?php declare(strict_types=1);

namespace App\Providers;

use Illuminate\Http\Request;
use Laravel\Horizon\Horizon;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->configureRateLimiting();
        $this->configureHorizon();
    }

    public function register(): void {}

    private function configureHorizon(): void
    {
        Horizon::auth(function (Request $request): bool {
            if (app()->environment('local')) {
                return true;
            }

            return in_array(
                $request->user()?->email,
                config('horizon.authorized_emails', []),
                true,
            );
        });
    }

    private function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request): Limit {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // Brute-force protection for authentication endpoints
        RateLimiter::for('auth', function (Request $request): Limit {
            return Limit::perMinute(5)->by($request->ip());
        });
    }
}
