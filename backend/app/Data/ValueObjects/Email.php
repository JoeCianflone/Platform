<?php declare(strict_types=1);

namespace App\Data\ValueObjects;

use App\Contracts\ValueObject;
use App\Concerns\Data\ValueObjects\CastToArray;
use App\Concerns\Data\ValueObjects\CastToString;
use App\Concerns\Data\ValueObjects\CheckEquality;
use App\Concerns\Data\ValueObjects\ValueObjectMaker;

final readonly class Email implements ValueObject
{
    use CastToArray;
    use CastToString;
    use CheckEquality;
    use ValueObjectMaker;

    private function __construct(
        public string $value
    ) {}

    /**
     * @return array<string, array<int, string>>
     */
    public static function rules(): array
    {
        return [
            'value' => ['required', 'email:rfc'],
        ];
    }
}
