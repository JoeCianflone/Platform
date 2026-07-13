<?php declare(strict_types=1);

namespace App\Tenant\Data\Collections;

use App\Tenant\Eloquent\Models\Tenant;
use Illuminate\Database\Eloquent\Collection;
use App\Concerns\Data\Collections\DataCollection;
use App\Tenant\Data\DataObjects\TenantDataObject;

final class TenantCollection
{
    use DataCollection;

    /** @param list<TenantDataObject> $items */
    public function __construct(private readonly array $items = []) {}

    public static function fromModels(Collection $models): static
    {
        return new static(
            $models->map(fn (Tenant $tenant) => TenantDataObject::fromModel($tenant))->all(),
        );
    }
}
