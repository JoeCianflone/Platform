<?php declare(strict_types=1);

namespace App\Console\Commands\Make;

use Illuminate\Foundation\Console\EnumMakeCommand;

final class MakeEnumCommand extends EnumMakeCommand
{
    use ModuleAwareGenerator;

    protected string $structureKey = 'src.Enums';
}
