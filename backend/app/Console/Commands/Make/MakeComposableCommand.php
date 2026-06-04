<?php declare(strict_types=1);

namespace App\Console\Commands\Make;

use Illuminate\Support\Str;
use Illuminate\Console\GeneratorCommand;

final class MakeComposableCommand extends GeneratorCommand
{
    protected $description = 'Generate a Vue composable in frontend/composables/';

    protected $name = 'make:composable';

    protected $type = 'Composable';

    protected function buildClass($name): string
    {
        $stub = $this->files->get($this->getStub());
        $displayName = Str::ucfirst(Str::after($name, 'use'));

        return str_replace('{{ name }}', $displayName, $stub);
    }

    protected function getNameInput(): string
    {
        $camel = Str::camel(parent::getNameInput());

        return str_starts_with($camel, 'use') ? $camel : 'use'.Str::ucfirst($camel);
    }

    protected function getPath($name): string
    {
        $frontendRoot = dirname($this->laravel->basePath()).'/frontend';

        return $frontendRoot.'/composables/'.$name.'.ts';
    }

    protected function getStub(): string
    {
        return $this->laravel->basePath('stubs/composable.stub');
    }

    protected function qualifyClass($name): string
    {
        return $name;
    }
}
