<?php declare(strict_types=1);

namespace App\Tenant\Data\DataObjects;

use App\Contracts\DataObject;
use App\Tenant\Data\ValueObjects\TenantSlug;
use App\Tenant\Data\ValueObjects\TenantDomain;
use App\Concerns\Data\DataObjects\DataObjectMaker;

final readonly class CreateTenantDataObject implements DataObject
{
    use DataObjectMaker;

    public function __construct(
        public string $name,
        public TenantSlug $slug,
        public ?TenantDomain $domain,
    ) {}
}
