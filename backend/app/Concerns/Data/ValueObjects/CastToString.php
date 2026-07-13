<?php declare(strict_types=1);

namespace App\Concerns\Data\ValueObjects;

trait CastToString
{
    public function __toString(): string
    {
        return $this->toString();
    }

    public function toString(): string
    {
        $values = [];

        foreach (get_object_vars($this) as $value) {
            if ($value === null) {
                continue;
            }

            if (is_scalar($value) || (is_object($value) && method_exists($value, '__toString'))) {
                $values[] = (string) $value;
            }
        }

        return implode(' ', $values);
    }
}
