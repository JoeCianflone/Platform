<?php declare(strict_types=1);

namespace App\Tenant\Domain\Scopes;

use App\Tenant\Enums\TenantStatus;
use Illuminate\Database\Eloquent\Builder;

final class ActiveTenants implements TenantScope
{
    public function apply(Builder $query): void
    {
        $query->where('status', TenantStatus::ACTIVE->value);
    }
}
