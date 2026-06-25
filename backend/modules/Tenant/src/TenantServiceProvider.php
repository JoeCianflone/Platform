<?php declare(strict_types=1);

namespace App\Tenant;

use App\Providers\ModuleServiceProvider;

final class TenantServiceProvider extends ModuleServiceProvider
{
    public function boot(): void
    {
        if (! config('tenant.enabled', true)) {
            return;
        }

        parent::boot();
    }

    public function register(): void
    {
        $this->mergeConfigFrom($this->modulePath() . '/src/tenant.config.php', 'tenant');

        if (! config('tenant.enabled', true)) {
            return;
        }

        $this->loadMigrationsFrom($this->modulePath() . '/database/migrations');
    }
    protected function modulePath(): string
    {
        return dirname(__DIR__);
    }
}
