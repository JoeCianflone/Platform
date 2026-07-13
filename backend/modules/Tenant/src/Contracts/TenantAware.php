<?php declare(strict_types=1);

namespace App\Tenant\Contracts;

interface TenantAware
{
    public function getTenantId(): string;
}
