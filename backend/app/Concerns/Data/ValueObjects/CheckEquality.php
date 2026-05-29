<?php declare(strict_types=1);

namespace App\Concerns\Data\ValueObjects;

use App\Contracts\ValueObject;

trait CheckEquality
{
    public function equals(ValueObject $valueObject): bool
    {
        return json_encode($this) === json_encode($valueObject);
    }
}
