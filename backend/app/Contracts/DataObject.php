<?php declare(strict_types=1);

namespace App\Contracts;

interface DataObject
{
    public static function make(mixed ...$args): static;

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
