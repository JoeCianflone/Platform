<?php declare(strict_types=1);

namespace App\Tenant\Enums;

enum TenantStatus: string
{
    case ACTIVE = 'active';
    case PENDING = 'pending';
    case SUSPENDED = 'suspended';
}
