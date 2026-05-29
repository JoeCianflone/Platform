<?php declare(strict_types=1);

namespace App\Concerns\Data;

use Illuminate\Support\Arr;

trait DataFormatter
{
    /**
     * @return array<string, array<int, callable(mixed): mixed>>
     */
    public static function format(): array
    {
        return [];
    }

    /**
     * @param  array<int|string, mixed>  $args
     * @return array<int|string, mixed>
     */
    public static function formatter(array $args): array
    {
        $formats = self::format();

        if ($formats === []) {
            return $args;
        }

        $result = [];

        foreach ($args as $key => $value) {
            $formatters = Arr::exists($formats, $key) ? $formats[(string) $key] : [];

            foreach ($formatters as $formatter) {
                /** @var callable(mixed): mixed $formatter */
                $value = $formatter($value);
            }

            $result[$key] = $value;
        }

        return $result;
    }
}
