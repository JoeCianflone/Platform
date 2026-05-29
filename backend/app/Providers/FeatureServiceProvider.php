<?php declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class FeatureServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Define Pennant feature flags here.
        // Example: Feature::define('feature-name', fn (User $user) => $user->isPremium());
    }
}
