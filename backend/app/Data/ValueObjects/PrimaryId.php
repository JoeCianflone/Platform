<?php declare(strict_types=1);

namespace App\Data\ValueObjects;

use App\Contracts\ValueObject;
use Ramsey\Uuid\Uuid as RamseyUuid;
use App\Concerns\Data\ValueObjects\CastToArray;
use App\Concerns\Data\ValueObjects\CastToString;
use App\Concerns\Data\ValueObjects\CheckEquality;
use App\Concerns\Data\ValueObjects\ValueObjectMaker;

final readonly class PrimaryId implements ValueObject
{
    use CastToArray;
    use CastToString;
    use CheckEquality;
    use ValueObjectMaker;

    private function __construct(
        public string $value
    ) {}

    public static function generate(): static
    {
        return new self(RamseyUuid::uuid4()->toString());
    }

    /**
     * @return array<string, array<int, string>>
     */
    public static function rules(): array
    {
        return [
            'value' => ['required', 'uuid'],
        ];
    }
}
