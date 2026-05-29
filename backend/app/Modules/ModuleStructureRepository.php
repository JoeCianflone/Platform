<?php declare(strict_types=1);

namespace App\Modules;

use RuntimeException;
use Symfony\Component\Yaml\Yaml;

class ModuleStructureRepository
{
    /** @var array<string, ModuleStructureNode> */
    protected array $nodes = [];

    public function __construct()
    {
        $this->load();
    }

    /**
     * @return array<string, ModuleStructureNode>
     */
    public function all(): array
    {
        return $this->nodes;
    }

    public function exists(string $key): bool
    {
        return isset($this->nodes[$key]);
    }

    public function find(string $key): ModuleStructureNode
    {
        if (! isset($this->nodes[$key])) {
            throw new RuntimeException(
                "Structure key [{$key}] does not exist."
            );
        }

        return $this->nodes[$key];
    }

    protected function load(): void
    {
        $path = config('modules.structure_path');

        if (! file_exists($path)) {
            throw new RuntimeException(
                "Module structure file not found: {$path}"
            );
        }

        $yaml = Yaml::parseFile($path);

        $this->walk($yaml['module'] ?? []);
    }

    /**
     * @param  array<string|int, mixed>  $structure
     */
    protected function walk(
        array $structure,
        string $path = '',
        string $namespace = '',
        string $keyPrefix = ''
    ): void {
        foreach ($structure as $name => $value) {

            if ($value === 'file') {
                continue;
            }

            $key = trim($keyPrefix.'.'.$name, '.');

            $currentPath = trim($path.'/'.$name, '/');

            $currentNamespace = trim(
                $namespace.'\\'.$name,
                '\\'
            );

            $this->nodes[$key] = new ModuleStructureNode(
                key: $key,
                path: $currentPath,
                namespace: $currentNamespace,
            );

            if (is_array($value)) {
                $this->walk(
                    structure: $value,
                    path: $currentPath,
                    namespace: $currentNamespace,
                    keyPrefix: $key,
                );
            }
        }
    }
}
