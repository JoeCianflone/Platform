<?php declare(strict_types=1);

namespace App\Support;

final class ModulePath
{
    public static function get(string $path = ''): string
    {
        $base = config('modules.path');

        return $path ? $base.'/'.ltrim($path, '/') : $base;
    }
}
