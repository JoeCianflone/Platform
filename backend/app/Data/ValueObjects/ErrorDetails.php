<?php declare(strict_types=1);

namespace App\Data\ValueObjects;

use App\Contracts\ValueObject;
use App\Concerns\Data\ValueObjects\CastToArray;
use App\Concerns\Data\ValueObjects\CastToString;
use App\Concerns\Data\ValueObjects\CheckEquality;
use App\Concerns\Data\ValueObjects\ValueObjectMaker;

final readonly class ErrorDetails implements ValueObject
{
    use CastToArray;
    use CastToString;
    use CheckEquality;
    use ValueObjectMaker;

    private function __construct(
        public string $code,
        public string $message,
        public ?string $field = null,
    ) {}

    /**
     * @return array<string, array<int, string>>
     */
    public static function rules(): array
    {
        return [
            'code' => ['required', 'string'],
            'message' => ['required', 'string'],
            'field' => ['nullable', 'string'],
        ];
    }
}
