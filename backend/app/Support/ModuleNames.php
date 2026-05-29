<?php declare(strict_types=1);

namespace App\Support;

final class ModuleNames
{
    /**
     * @return array<int, string>
     */
    public static function toArray(): array
    {
        $modulePath = 'backend/modules';
        $modules = glob(base_path($modulePath.'/*')) ?: [];

        /** @var array<int, string> */
        return collect($modules)
            ->map(
                fn (string|false $dir): string => $dir === '' || $dir === '0'
                    ? ''
                    : str_replace(base_path($modulePath.'/'), '', $dir)
            )
            ->all();
    }
}
