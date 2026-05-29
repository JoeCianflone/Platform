<?php declare(strict_types=1);

namespace App\Data\ValueObjects;

use App\Contracts\ValueObject;
use App\Concerns\Data\ValueObjects\CastToArray;
use App\Concerns\Data\ValueObjects\CheckEquality;
use App\Concerns\Data\ValueObjects\ValueObjectMaker;

final readonly class Address implements ValueObject
{
    use CastToArray;
    use CheckEquality;
    use ValueObjectMaker;

    private function __construct(
        public string $street,
        public ?string $street2,
        public string $city,
        public string $state,
        public string $zip,
    ) {}

    /**
     * @return array<string, array<int, string>>
     */
    public static function format(): array
    {
        return [
            'street' => ['trim'],
            'street2' => ['trim'],
            'city' => ['trim'],
            'state' => ['trim', 'strtoupper'],
            'zip' => ['trim'],
        ];
    }

    /**
     * @return array<string, array<int, string>>
     */
    public static function rules(): array
    {
        return [
            'street' => ['required', 'string'],
            'street2' => ['nullable', 'string'],
            'city' => ['required', 'string'],
            'state' => ['required', 'string', 'size:2'],
            'zip' => ['required', 'string', 'size:5'],
        ];
    }

    public function toString(): string
    {
        return implode(', ', array_filter([
            $this->street,
            $this->street2,
            sprintf('%s, %s %s', $this->city, $this->state, $this->zip),
        ]));
    }
}
