<?php declare(strict_types=1);

namespace App\Concerns\Data\ValueObjects;

trait CastToArray
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return collect(make_array($this))
            ->flatMap(fn ($value, $key): array => [$key => $value])
            ->toArray();
    }
}
