<?php declare(strict_types=1);

namespace App\Console\Commands\Make;

use Illuminate\Support\Str;
use Illuminate\Console\GeneratorCommand;

final class MakeValueObjectCommand extends GeneratorCommand
{
    use ModuleAwareGenerator;

    protected $description = 'Create a new ValueObject class';

    protected $name = 'make:valueobject';

    protected string $structureKey = 'src.Data.ValueObjects';

    protected $type = 'ValueObject';

    protected function getDefaultNamespace($rootNamespace): string
    {
        if (! $this->usingModule()) {
            return $rootNamespace.'\Data\ValueObjects';
        }

        $suffix = str_replace('.', '\\', Str::after($this->structureKey, 'src.'));

        return $rootNamespace.'\\'.$suffix;
    }

    protected function getStub(): string
    {
        return base_path('stubs/value-object.stub');
    }
}
