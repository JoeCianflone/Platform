<?php declare(strict_types=1);

namespace App\Tenant\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Tenant\Support\TenantContext;
use App\Tenant\Domain\Scopes\WithSlug;
use App\Tenant\Domain\Scopes\WithDomain;
use Symfony\Component\HttpFoundation\Response;
use App\Tenant\Support\TenantConnectionManager;
use App\Tenant\Contracts\Queries\TenantQueryContract;

final class ResolveTenant
{
    public function __construct(
        private readonly TenantQueryContract $tenants,
        private readonly TenantContext $context,
        private readonly TenantConnectionManager $connections,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        if (! config('tenant.enabled', true)) {
            return $next($request);
        }

        $host = $request->getHost();

        $tenant = $this->tenants->first(new WithDomain($host));

        if ($tenant === null) {
            $subdomain = explode('.', $host)[0];
            $tenant = $this->tenants->first(new WithSlug($subdomain));
        }

        abort_if($tenant === null, 404);

        $snapshot = $tenant->toSnapshot();

        $this->context->set($snapshot);
        $this->connections->connect($snapshot);

        return $next($request);
    }
}
