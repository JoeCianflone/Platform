<?php declare(strict_types=1);

namespace App\Tenant\Domain\Listeners;

use App\Tenant\Domain\Events\TenantCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Tenant\Jobs\ProvisionTenantDatabase;

final class DispatchTenantProvisioning implements ShouldQueue
{
    public function handle(TenantCreated $event): void
    {
        ProvisionTenantDatabase::dispatch($event->tenant);
    }
}
