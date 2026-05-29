<?php declare(strict_types=1);

namespace App\Console\Commands\Make;

use Illuminate\Routing\Console\ControllerMakeCommand;

final class MakeControllerCommand extends ControllerMakeCommand
{
    use ModuleAwareGenerator;

    protected string $structureKey = 'src.Http.Controllers';
}
