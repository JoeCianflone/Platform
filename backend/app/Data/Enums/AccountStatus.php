<?php declare(strict_types=1);

namespace App\Data\Enums;

enum AccountStatus: string
{
    case ACTIVE = 'active';
    case CLOSED = 'closed';
    case INACTIVE = 'inactive';
    case LOCKED = 'locked';
    case PENDING = 'pending';
}
