<?php declare(strict_types=1);

namespace App\Tenant\Domain\Scopes;

use Illuminate\Database\Eloquent\Builder;

final class WithSlug implements TenantScope
{
    public function __construct(private readonly string $slug) {}

    public function apply(Builder $query): void
    {
        $query->where('slug', $this->slug);
    }
}
