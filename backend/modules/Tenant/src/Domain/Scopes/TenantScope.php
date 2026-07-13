<?php declare(strict_types=1);

namespace App\Tenant\Domain\Scopes;

use Illuminate\Database\Eloquent\Builder;

interface TenantScope
{
    public function apply(Builder $query): void;
}
