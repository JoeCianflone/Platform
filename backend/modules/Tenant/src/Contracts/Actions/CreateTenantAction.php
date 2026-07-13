<?php declare(strict_types=1);

namespace App\Tenant\Contracts\Actions;

use App\Tenant\Data\DataObjects\TenantDataObject;
use App\Tenant\Data\DataObjects\CreateTenantDataObject;

interface CreateTenantAction
{
    public function handle(CreateTenantDataObject $data): TenantDataObject;
}
