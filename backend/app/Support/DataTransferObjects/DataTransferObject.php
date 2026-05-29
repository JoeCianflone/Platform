<?php declare(strict_types=1);

namespace App\Support\DataTransferObjects;

abstract readonly class DataTransferObject
{
    /**
     * @return array<string, mixed>
     */
    abstract public function toArray(): array;
}
