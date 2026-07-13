<?php declare(strict_types=1);

namespace App\Tenant\Domain\Scopes;

use Illuminate\Database\Eloquent\Builder;

final class WithId implements TenantScope
{
    public function __construct(private readonly string $id) {}

    public function apply(Builder $query): void
    {
        $query->where('id', $this->id);
    }
}
