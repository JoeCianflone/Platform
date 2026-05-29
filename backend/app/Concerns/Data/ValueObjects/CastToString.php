<?php declare(strict_types=1);

namespace App\Concerns\Data\ValueObjects;

use ReflectionClass;

trait CastToString
{
    public function __toString(): string
    {
        return $this->toString();
    }

    public function toString(): string
    {
        $reflectionClass = new ReflectionClass($this);

        $values = [];

        foreach ($reflectionClass->getProperties(\ReflectionProperty::IS_PUBLIC) as $reflectionProperty) {
            $value = $reflectionProperty->getValue($this);

            if ($value === null) {
                continue;
            }

            if (is_object($value) && method_exists($value, '__toString')) {
                $values[] = (string) $value;

                continue;
            }

            if (is_scalar($value)) {
                $values[] = (string) $value;
            }
        }

        return implode(' ', $values);
    }
}
