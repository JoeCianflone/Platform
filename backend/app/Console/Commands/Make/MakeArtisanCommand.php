<?php declare(strict_types=1);

namespace App\Console\Commands\Make;

use Illuminate\Foundation\Console\ConsoleMakeCommand;

final class MakeArtisanCommand extends ConsoleMakeCommand
{
    use ModuleAwareGenerator;

    protected string $structureKey = 'src.Console.Commands';
}
