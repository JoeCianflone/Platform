<?php declare(strict_types=1);

namespace App\Tenant\Domain\Events;

use Illuminate\Queue\SerializesModels;
use App\Tenant\Data\Snapshots\TenantSnapshot;
use Illuminate\Foundation\Events\Dispatchable;

final class TenantCreated
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public readonly TenantSnapshot $tenant) {}
}
