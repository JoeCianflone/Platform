<?php declare(strict_types=1);

namespace App\Support\ValueObjects;

abstract readonly class ValueObject
{
    abstract public function __toString(): string;
    abstract public function equals(self $other): bool;
}
