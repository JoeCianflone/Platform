<?php declare(strict_types=1);

namespace App\Console\Commands\Make;

use Illuminate\Console\GeneratorCommand;

final class MakeVitestCommand extends GeneratorCommand
{
    protected $description = 'Generate a Vitest test file alongside a frontend component';

    protected $name = 'make:vitest';

    protected $type = 'Vitest test';

    protected function buildClass($name): string
    {
        $stub = $this->files->get($this->getStub());

        return str_replace('{{ name }}', basename($name), $stub);
    }

    protected function getPath($name): string
    {
        $segments = explode('/', $name);
        $componentName = array_pop($segments);
        $dir = implode('/', $segments);

        $frontendRoot = dirname($this->laravel->basePath()).'/frontend';
        $testDir = $dir !== '' ? "{$frontendRoot}/{$dir}/__tests__" : "{$frontendRoot}/__tests__";

        return "{$testDir}/{$componentName}.test.ts";
    }

    protected function getStub(): string
    {
        return $this->laravel->basePath('stubs/vitest.stub');
    }

    protected function qualifyClass($name): string
    {
        return trim(str_replace('\\', '/', $name), '/');
    }
}
