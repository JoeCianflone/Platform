<?php declare(strict_types=1);

namespace App\Concerns\Data\ValueObjects;

use App\Support\MakeArray;

trait CastToArray
{
    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return MakeArray::get($this);
    }
}
