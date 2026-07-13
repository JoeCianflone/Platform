<?php declare(strict_types=1);

namespace App\Contracts;

interface Snapshot
{
    /** @return array<string, mixed> */
    public function toArray(): array;
}
