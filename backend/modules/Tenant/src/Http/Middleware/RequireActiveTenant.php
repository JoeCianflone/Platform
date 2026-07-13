<?php declare(strict_types=1);

namespace App\Tenant\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Tenant\Enums\TenantStatus;
use App\Tenant\Support\TenantContext;
use Symfony\Component\HttpFoundation\Response;

final class RequireActiveTenant
{
    public function __construct(private readonly TenantContext $context) {}

    public function handle(Request $request, Closure $next): Response
    {
        abort_if($this->context->require()->status !== TenantStatus::ACTIVE, 503);

        return $next($request);
    }
}
