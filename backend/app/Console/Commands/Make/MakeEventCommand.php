<?php declare(strict_types=1);

namespace App\Console\Commands\Make;

use Illuminate\Foundation\Console\EventMakeCommand;

final class MakeEventCommand extends EventMakeCommand
{
    use ModuleAwareGenerator;

    protected string $structureKey = 'src.Domain.Events';
}
