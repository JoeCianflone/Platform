<?php declare(strict_types=1);

namespace App\Support;

final class MakeArray
{
    /**
     * @param  array<int|string, mixed>|object|string  $data
     * @return array<int|string, mixed>
     */
    public static function get(array|object|string $data): array
    {
        if (is_string($data)) {
            return json_decode($data, true);
        }

        return is_array($data)
            ? $data
            : get_object_vars($data);
    }
}
