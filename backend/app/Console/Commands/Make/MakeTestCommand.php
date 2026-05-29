<?php declare(strict_types=1);

namespace App\Console\Commands\Make;

use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;
use Illuminate\Foundation\Console\TestMakeCommand;

final class MakeTestCommand extends TestMakeCommand
{
    use ModuleAwareGenerator;

    protected string $structureKey = 'tests.Feature';

    /**
     * @return array<mixed>
     */
    protected function getOptions(): array
    {
        return [
            ...parent::getOptions(),
            ['module', null, InputOption::VALUE_REQUIRED, 'Generate within a module'],
        ];
    }

    protected function getPath($name): string
    {
        if (! $this->usingModule()) {
            return parent::getPath($name);
        }

        $module = Str::studly($this->getModule());
        $name = Str::replaceFirst($this->rootNamespace(), '', $name);

        return base_path("modules/{$module}/tests/".ltrim(str_replace('\\', '/', $name), '/').'.php');
    }
}
