<?php declare(strict_types=1);

namespace App\Tenant\Contracts\Actions;

use App\Tenant\Data\DataObjects\TenantDataObject;

interface ResumeTenantAction
{
    public function handle(string $tenantId): TenantDataObject;
}
