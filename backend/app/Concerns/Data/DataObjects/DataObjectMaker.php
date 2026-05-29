<?php declare(strict_types=1);

namespace App\Concerns\Data\DataObjects;

use App\Concerns\Data\DataFormatter;
use App\Concerns\Data\DataNormalizer;

trait DataObjectMaker
{
    use DataFormatter;
    use DataNormalizer;

    public static function make(mixed ...$args): static
    {
        $args = make_array($args);
        $args = static::normalize($args);

        return new static(...static::formatter($args));
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
