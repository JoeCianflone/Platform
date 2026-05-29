<?php declare(strict_types=1);

namespace App\Console\Commands\Make;

use Illuminate\Support\Str;
use Illuminate\Console\GeneratorCommand;

final class MakeDataObjectCommand extends GeneratorCommand
{
    use ModuleAwareGenerator;

    protected $description = 'Create a new DataObject class';

    protected $name = 'make:dataobject';

    protected string $structureKey = 'src.Data.DomainObjects';

    protected $type = 'DataObject';

    protected function getDefaultNamespace($rootNamespace): string
    {
        if (! $this->usingModule()) {
            return $rootNamespace.'\Data\DataObject';
        }

        $suffix = str_replace('.', '\\', Str::after($this->structureKey, 'src.'));

        return $rootNamespace.'\\'.$suffix;
    }

    protected function getNameInput(): string
    {
        $name = trim($this->argument('name'));

        return Str::endsWith($name, 'DataObject') ? $name : $name.'DataObject';
    }

    protected function getStub(): string
    {
        return base_path('stubs/data-object.stub');
    }
}
