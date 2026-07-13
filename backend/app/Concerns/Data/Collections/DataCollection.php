<?php declare(strict_types=1);

namespace App\Concerns\Data\Collections;

trait DataCollection
{
    /** @return list<mixed> */
    public function all(): array
    {
        return $this->items;
    }
    public function count(): int
    {
        return count($this->items);
    }

    public function isEmpty(): bool
    {
        return empty($this->items);
    }

    /** @return list<array<string, mixed>> */
    public function toArray(): array
    {
        return array_map(fn ($item) => $item->toArray(), $this->items);
    }
}
