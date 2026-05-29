<?php declare(strict_types=1);

namespace App\Console\Commands\Make;

use Illuminate\Foundation\Console\ObserverMakeCommand;

final class MakeObserverCommand extends ObserverMakeCommand
{
    use ModuleAwareGenerator;

    protected string $structureKey = 'src.Eloquent.Observers';
}
