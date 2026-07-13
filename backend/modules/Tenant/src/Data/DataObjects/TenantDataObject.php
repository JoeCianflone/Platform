<?php declare(strict_types=1);

namespace App\Tenant\Data\DataObjects;

use App\Support\MakeArray;
use App\Contracts\DataObject;
use App\Tenant\Enums\TenantStatus;
use App\Tenant\Eloquent\Models\Tenant;
use App\Tenant\Data\Snapshots\TenantSnapshot;

final readonly class TenantDataObject implements DataObject
{
    public function __construct(
        public string $id,
        public string $name,
        public string $slug,
        public ?string $domain,
        public TenantStatus $status,
    ) {}

    public static function fromModel(Tenant $model): static
    {
        return new static(
            id: $model->id,
            name: $model->name,
            slug: $model->slug,
            domain: $model->domain,
            status: $model->status,
        );
    }

    public static function make(mixed ...$args): static
    {
        return new static(...$args);
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return MakeArray::get($this);
    }

    public function toSnapshot(): TenantSnapshot
    {
        return new TenantSnapshot(
            id: $this->id,
            name: $this->name,
            slug: $this->slug,
            domain: $this->domain,
            status: $this->status,
        );
    }
}
