<?php declare(strict_types=1);

namespace App\Modules;

class ModulePathResolver
{
    public function resolve(
        string $module,
        ModuleStructureNode $node,
        string $class
    ): string {
        $class = str_replace('\\', '/', trim($class, '\\'));

        return rtrim(config('modules.path'), DIRECTORY_SEPARATOR)
            .DIRECTORY_SEPARATOR
            .$module
            .DIRECTORY_SEPARATOR
            .trim($node->path(), '/')
            .DIRECTORY_SEPARATOR
            .$class
            .'.php';
    }
}
