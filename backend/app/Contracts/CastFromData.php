<?php declare(strict_types=1);

namespace App\Contracts;

interface CastFromData
{
    /**
     * @param  object|array<string, mixed>  $data
     */
    public static function make(object|array $data): static;
}
