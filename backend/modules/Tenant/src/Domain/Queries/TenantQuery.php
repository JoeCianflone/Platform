<?php declare(strict_types=1);

namespace App\Tenant\Domain\Queries;

use App\Tenant\Eloquent\Models\Tenant;
use App\Tenant\Domain\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Builder;
use App\Tenant\Data\Collections\TenantCollection;
use App\Tenant\Data\DataObjects\TenantDataObject;
use App\Tenant\Contracts\Queries\TenantQueryContract;

final class TenantQuery implements TenantQueryContract
{
    public function first(TenantScope ...$scopes): ?TenantDataObject
    {
        $model = $this->scopedQuery(...$scopes)->first();

        return $model ? TenantDataObject::fromModel($model) : null;
    }

    public function get(TenantScope ...$scopes): TenantCollection
    {
        return TenantCollection::fromModels($this->scopedQuery(...$scopes)->get());
    }

    public function paginate(int $perPage, TenantScope ...$scopes): TenantCollection
    {
        return TenantCollection::fromModels($this->scopedQuery(...$scopes)->paginate($perPage));
    }

    private function baseQuery(): Builder
    {
        return Tenant::query()->select([
            'id', 'name', 'slug', 'domain', 'status',
        ]);
    }

    private function scopedQuery(TenantScope ...$scopes): Builder
    {
        $query = $this->baseQuery();

        foreach ($scopes as $scope) {
            $scope->apply($query);
        }

        return $query;
    }
}
