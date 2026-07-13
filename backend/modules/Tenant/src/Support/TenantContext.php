<?php declare(strict_types=1);

namespace App\Tenant\Support;

use RuntimeException;
use App\Tenant\Data\Snapshots\TenantSnapshot;

final class TenantContext
{
    private ?TenantSnapshot $current = null;

    public function clear(): void
    {
        $this->current = null;
    }

    public function get(): ?TenantSnapshot
    {
        return $this->current;
    }

    public function has(): bool
    {
        return $this->current !== null;
    }

    public function require(): TenantSnapshot
    {
        return $this->current ?? throw new RuntimeException('No tenant set in context.');
    }

    public function set(TenantSnapshot $tenant): void
    {
        $this->current = $tenant;
    }
}
