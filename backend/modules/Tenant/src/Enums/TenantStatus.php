<?php declare(strict_types=1);

namespace App\Tenant\Enums;

enum TenantStatus: string
{
    case Active = 'active';
    case Pending = 'pending';
    case Suspended = 'suspended';
}
