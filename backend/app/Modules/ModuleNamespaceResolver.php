<?php declare(strict_types=1);

namespace App\Modules;

class ModuleNamespaceResolver
{
    public function resolve(
        string $module,
        ModuleStructureNode $node,
        string $class = ''
    ): string {
        $base = trim(config('modules.base_namespace'), '\\');

        $namespace = trim($node->namespace(), '\\');

        $classNamespace = '';

        if ($class !== '') {
            $parts = explode('\\', trim($class, '\\'));

            array_pop($parts);

            $classNamespace = implode('\\', $parts);
        }

        return trim(
            $base
            .'\\'
            .$module
            .'\\'
            .$namespace
            .'\\'
            .$classNamespace,
            '\\'
        );
    }
}
