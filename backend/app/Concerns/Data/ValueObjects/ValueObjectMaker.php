<?php declare(strict_types=1);

namespace App\Concerns\Data\ValueObjects;

use App\Concerns\Data\DataFormatter;
use App\Concerns\Data\DataValidator;
use App\Concerns\Data\DataNormalizer;
use Illuminate\Validation\ValidationException;

trait ValueObjectMaker
{
    use DataFormatter;
    use DataNormalizer;
    use DataValidator;

    /**
     * @throws ValidationException
     */
    public static function make(mixed ...$args): static
    {
        $args = make_array($args);
        static::validate($args);

        $args = static::normalize($args);

        return new static(...static::formatter($args));
    }
}
