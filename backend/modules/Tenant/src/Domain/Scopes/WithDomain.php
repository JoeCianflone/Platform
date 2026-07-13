<?php declare(strict_types=1);

namespace App\Tenant\Domain\Scopes;

use Illuminate\Database\Eloquent\Builder;

final class WithDomain implements TenantScope
{
    public function __construct(private readonly string $domain) {}

    public function apply(Builder $query): void
    {
        $query->where('domain', $this->domain);
    }
}
