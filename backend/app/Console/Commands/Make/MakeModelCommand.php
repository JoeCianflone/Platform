<?php declare(strict_types=1);

namespace App\Console\Commands\Make;

use Illuminate\Foundation\Console\ModelMakeCommand;

final class MakeModelCommand extends ModelMakeCommand
{
    use ModuleAwareGenerator;

    protected string $structureKey = 'src.Eloquent.Models';
}
