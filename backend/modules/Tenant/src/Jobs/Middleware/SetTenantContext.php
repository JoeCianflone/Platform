<?php declare(strict_types=1);

namespace App\Tenant\Jobs\Middleware;

use App\Tenant\Domain\Scopes\WithId;
use App\Tenant\Contracts\TenantAware;
use App\Tenant\Support\TenantContext;
use App\Tenant\Support\TenantConnectionManager;
use App\Tenant\Contracts\Queries\TenantQueryContract;

final class SetTenantContext
{
    public function __construct(
        private readonly TenantQueryContract $tenants,
        private readonly TenantContext $context,
        private readonly TenantConnectionManager $connections,
    ) {}

    public function handle(mixed $job, callable $next): void
    {
        if (! $job instanceof TenantAware) {
            $next($job);

            return;
        }

        $tenant = $this->tenants->first(new WithId($job->getTenantId()));

        if ($tenant !== null) {
            $snapshot = $tenant->toSnapshot();

            $this->context->set($snapshot);
            $this->connections->connect($snapshot);
        }

        $next($job);
    }
}
