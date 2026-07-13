<?php declare(strict_types=1);

namespace App\Tenant\Contracts\Queries;

use App\Tenant\Domain\Scopes\TenantScope;
use App\Tenant\Data\Collections\TenantCollection;
use App\Tenant\Data\DataObjects\TenantDataObject;

interface TenantQueryContract
{
    public function first(TenantScope ...$scopes): ?TenantDataObject;

    public function get(TenantScope ...$scopes): TenantCollection;

    public function paginate(int $perPage, TenantScope ...$scopes): TenantCollection;
}
