<?php declare(strict_types=1);

namespace App\Tenant\Data\Snapshots;

use App\Support\MakeArray;
use App\Contracts\Snapshot;
use App\Tenant\Enums\TenantStatus;

final readonly class TenantSnapshot implements Snapshot
{
    public function __construct(
        public string $id,
        public string $name,
        public string $slug,
        public ?string $domain,
        public TenantStatus $status,
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return MakeArray::get($this);
    }
}
