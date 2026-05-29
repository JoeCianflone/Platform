<?php declare(strict_types=1);

namespace App\Console\Commands\Make;

use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;
use Illuminate\Database\Console\Seeds\SeederMakeCommand;

final class MakeSeederCommand extends SeederMakeCommand
{
    use ModuleAwareGenerator;

    protected string $structureKey = 'database.seeders';

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

        $name = str_replace('\\', '/', Str::replaceFirst($this->rootNamespace(), '', $name));

        return base_path('modules/'.Str::studly($this->getModule()).'/database/seeders/'.$name.'.php');
    }

    protected function rootNamespace(): string
    {
        if (! $this->usingModule()) {
            return parent::rootNamespace();
        }

        return 'App\\'.Str::studly($this->getModule()).'\\Database\\Seeders\\';
    }
}
