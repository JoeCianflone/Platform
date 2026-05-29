<?php declare(strict_types=1);

namespace App\Console\Commands\Make;

use Illuminate\Foundation\Console\JobMakeCommand;

final class MakeJobCommand extends JobMakeCommand
{
    use ModuleAwareGenerator;

    protected string $structureKey = 'src.Jobs';
}
