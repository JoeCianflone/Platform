<?php declare(strict_types=1);

namespace App\Contracts;

use Illuminate\Contracts\Support\Arrayable;

/**
 * @extends Arrayable<string, mixed>
 */
interface ValueObject extends Arrayable
{
    public function equals(ValueObject $valueObject): bool;

    public static function make(mixed ...$args): static;

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array;

    public function toString(): string;
}
