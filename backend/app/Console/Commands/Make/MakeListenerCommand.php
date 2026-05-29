<?php declare(strict_types=1);

namespace App\Console\Commands\Make;

use Illuminate\Foundation\Console\ListenerMakeCommand;

final class MakeListenerCommand extends ListenerMakeCommand
{
    use ModuleAwareGenerator;

    protected string $structureKey = 'src.Domain.Listeners';
}
