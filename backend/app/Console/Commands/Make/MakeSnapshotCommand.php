<?php declare(strict_types=1);

namespace App\Console\Commands\Make;

use Illuminate\Support\Str;
use Illuminate\Console\GeneratorCommand;

final class MakeSnapshotCommand extends GeneratorCommand
{
    use ModuleAwareGenerator;

    protected $description = 'Create a new Snapshot class';

    protected $name = 'make:snapshot';

    protected string $structureKey = 'src.Data.Snapshots';

    protected $type = 'Snapshot';

    protected function getDefaultNamespace($rootNamespace): string
    {
        if (! $this->usingModule()) {
            return $rootNamespace.'\\Data\\Snapshots';
        }

        $suffix = str_replace('.', '\\', Str::after($this->structureKey, 'src.'));

        return $rootNamespace.'\\'.$suffix;
    }

    protected function getNameInput(): string
    {
        $name = parent::getNameInput();

        return str_ends_with($name, 'Snapshot') ? $name : $name.'Snapshot';
    }

    protected function getStub(): string
    {
        return base_path('stubs/snapshot.stub');
    }
}
