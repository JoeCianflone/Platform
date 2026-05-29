<?php declare(strict_types=1);

namespace App\Concerns\Data;

use ReflectionClass;
use ReflectionNamedType;

trait DataNormalizer
{
    /**
     * @param  array<int|string, mixed>  $data
     * @return array<int|string, mixed>
     */
    protected static function normalize(array $data): array
    {
        $reflectionClass = new ReflectionClass(static::class);
        $constructor = $reflectionClass->getConstructor();

        if ($constructor === null) {
            return $data;
        }

        foreach ($constructor->getParameters() as $parameter) {
            $name = $parameter->getName();

            if (! array_key_exists($name, $data)) {
                $type = $parameter->getType();

                if (
                    $type instanceof ReflectionNamedType &&
                    $type->allowsNull()
                ) {
                    $data[$name] = null;
                }
            }
        }

        return $data;
    }
}
