<?php declare(strict_types=1);

namespace App\Tenant;

use App\Tenant\Support\TenantContext;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use App\Providers\ModuleServiceProvider;
use App\Tenant\Domain\Queries\TenantQuery;
use App\Tenant\Domain\Actions\CreateTenant;
use App\Tenant\Domain\Actions\ResumeTenant;
use App\Tenant\Domain\Events\TenantCreated;
use App\Tenant\Domain\Actions\SuspendTenant;
use App\Tenant\Http\Middleware\ResolveTenant;
use App\Tenant\Http\Middleware\RequireActiveTenant;
use App\Tenant\Contracts\Actions\CreateTenantAction;
use App\Tenant\Contracts\Actions\ResumeTenantAction;
use App\Tenant\Contracts\Actions\SuspendTenantAction;
use App\Tenant\Contracts\Queries\TenantQueryContract;
use App\Tenant\Domain\Listeners\DispatchTenantProvisioning;

final class TenantServiceProvider extends ModuleServiceProvider
{
    public function boot(): void
    {
        if (! config('tenant.enabled', true)) {
            return;
        }

        parent::boot();

        Event::listen(TenantCreated::class, DispatchTenantProvisioning::class);

        Route::aliasMiddleware('resolve-tenant', ResolveTenant::class);
        Route::aliasMiddleware('require-active-tenant', RequireActiveTenant::class);
    }

    public function register(): void
    {
        $this->mergeConfigFrom($this->modulePath() . '/src/tenant.config.php', 'tenant');

        if (! config('tenant.enabled', true)) {
            return;
        }

        $this->loadMigrationsFrom($this->modulePath() . '/database/migrations');

        $this->app->singleton(TenantContext::class);

        $this->app->bind(TenantQueryContract::class, TenantQuery::class);
        $this->app->bind(CreateTenantAction::class, CreateTenant::class);
        $this->app->bind(SuspendTenantAction::class, SuspendTenant::class);
        $this->app->bind(ResumeTenantAction::class, ResumeTenant::class);
    }
    protected function modulePath(): string
    {
        return dirname(__DIR__);
    }
}
