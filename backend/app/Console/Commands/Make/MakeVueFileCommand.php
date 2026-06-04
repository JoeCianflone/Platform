<?php declare(strict_types=1);

namespace App\Console\Commands\Make;

use Illuminate\Console\GeneratorCommand;

final class MakeVueFileCommand extends GeneratorCommand
{
    protected $description = 'Generate a Vue SFC in the frontend directory';

    protected $name = 'make:vue-file';

    protected $type = 'Vue component';

    protected function buildClass($name): string
    {
        $stub = $this->files->get($this->getStub());

        return str_replace('Example', basename($name), $stub);
    }

    protected function getPath($name): string
    {
        $frontendRoot = dirname($this->laravel->basePath()).'/frontend';

        return $frontendRoot.'/'.ltrim($name, '/').'.vue';
    }

    protected function getStub(): string
    {
        return $this->laravel->basePath('stubs/vue-sfc.stub');
    }

    protected function qualifyClass($name): string
    {
        return trim(str_replace('\\', '/', $name), '/');
    }
}
