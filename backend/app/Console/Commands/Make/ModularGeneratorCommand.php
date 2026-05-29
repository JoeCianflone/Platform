<?php declare(strict_types=1);

namespace App\Console\Commands\Make;

use Illuminate\Console\GeneratorCommand;

abstract class ModularGeneratorCommand extends GeneratorCommand
{
    use ModuleAwareGenerator;

    protected string $structureKey = '';
}
