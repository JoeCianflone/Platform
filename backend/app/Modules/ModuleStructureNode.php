<?php declare(strict_types=1);

namespace App\Modules;

class ModuleStructureNode
{
    /**
     * @param  array<string, ModuleStructureNode>  $children
     */
    public function __construct(
        protected string $key,
        protected string $path,
        protected string $namespace,
        protected array $children = [],
    ) {}

    /**
     * @return array<string, ModuleStructureNode>
     */
    public function children(): array
    {
        return $this->children;
    }

    public function key(): string
    {
        return $this->key;
    }

    public function namespace(): string
    {
        return $this->namespace;
    }

    public function path(): string
    {
        return $this->path;
    }
}
