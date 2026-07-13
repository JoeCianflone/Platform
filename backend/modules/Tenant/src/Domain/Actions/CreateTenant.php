<?php declare(strict_types=1);

namespace App\Tenant\Domain\Actions;

use App\Tenant\Enums\TenantStatus;
use Illuminate\Support\Facades\DB;
use App\Tenant\Eloquent\Models\Tenant;
use App\Tenant\Domain\Events\TenantCreated;
use App\Tenant\Data\DataObjects\TenantDataObject;
use App\Tenant\Contracts\Actions\CreateTenantAction;
use App\Tenant\Data\DataObjects\CreateTenantDataObject;

final class CreateTenant implements CreateTenantAction
{
    public function handle(CreateTenantDataObject $data): TenantDataObject
    {
        return DB::transaction(function () use ($data): TenantDataObject {
            $tenant = Tenant::create([
                'name'   => $data->name,
                'slug'   => $data->slug->value,
                'domain' => $data->domain?->value,
                'status' => TenantStatus::PENDING,
            ]);

            event(new TenantCreated($tenant->toSnapshot()));

            return TenantDataObject::fromModel($tenant);
        });
    }
}
