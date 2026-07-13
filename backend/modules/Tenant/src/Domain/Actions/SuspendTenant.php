<?php declare(strict_types=1);

namespace App\Tenant\Domain\Actions;

use App\Tenant\Enums\TenantStatus;
use Illuminate\Support\Facades\DB;
use App\Tenant\Eloquent\Models\Tenant;
use App\Tenant\Domain\Events\TenantSuspended;
use App\Tenant\Data\DataObjects\TenantDataObject;
use App\Tenant\Contracts\Actions\SuspendTenantAction;

final class SuspendTenant implements SuspendTenantAction
{
    public function handle(string $tenantId): TenantDataObject
    {
        return DB::transaction(function () use ($tenantId): TenantDataObject {
            $tenant = Tenant::findOrFail($tenantId);

            $tenant->update(['status' => TenantStatus::SUSPENDED]);

            $tenant = $tenant->fresh();

            event(new TenantSuspended($tenant->toSnapshot()));

            return TenantDataObject::fromModel($tenant);
        });
    }
}
