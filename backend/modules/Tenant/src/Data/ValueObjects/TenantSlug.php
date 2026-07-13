<?php declare(strict_types=1);

namespace App\Tenant\Data\ValueObjects;

use App\Contracts\ValueObject;
use App\Concerns\Data\ValueObjects\CastToArray;
use App\Concerns\Data\ValueObjects\CastToString;
use App\Concerns\Data\ValueObjects\CheckEquality;
use App\Concerns\Data\ValueObjects\ValueObjectMaker;

final readonly class TenantSlug implements ValueObject
{
    use CastToArray;
    use CastToString;
    use CheckEquality;
    use ValueObjectMaker;

    private function __construct(
        public readonly string $value,
    ) {}

    /** @return array<string, array<int, callable>> */
    public static function format(): array
    {
        return [
            'value' => [fn (string $v): string => strtolower(trim($v))],
        ];
    }

    /** @return array<string, mixed> */
    public static function rules(): array
    {
        return [
            'value' => ['required', 'string', 'max:63', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
        ];
    }
}
