<?php declare(strict_types=1);

namespace App\Tenant\Data\ValueObjects;

use App\Contracts\ValueObject;
use App\Concerns\Data\ValueObjects\CastToArray;
use App\Concerns\Data\ValueObjects\CastToString;
use App\Concerns\Data\ValueObjects\CheckEquality;
use App\Concerns\Data\ValueObjects\ValueObjectMaker;

final readonly class TenantDomain implements ValueObject
{
    use CastToArray;
    use CastToString;
    use CheckEquality;
    use ValueObjectMaker;

    private function __construct(
        public readonly ?string $value,
    ) {}

    /** @return array<string, array<int, callable>> */
    public static function format(): array
    {
        return [
            'value' => [fn (?string $v): ?string => $v !== null ? strtolower(trim($v)) : null],
        ];
    }

    /** @return array<string, mixed> */
    public static function rules(): array
    {
        return [
            'value' => [
                'nullable',
                'string',
                'max:253',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if ($value === null) {
                        return;
                    }

                    if (str_contains($value, '://') || str_contains($value, '/') || str_contains($value, ':')) {
                        $fail('The :attribute must be a bare hostname — no scheme, port, or path.');

                        return;
                    }

                    if (! filter_var($value, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)) {
                        $fail('The :attribute must be a valid hostname.');
                    }
                },
            ],
        ];
    }
}
