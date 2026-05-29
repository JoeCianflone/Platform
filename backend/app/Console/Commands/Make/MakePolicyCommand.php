<?php declare(strict_types=1);

namespace App\Console\Commands\Make;

use Illuminate\Foundation\Console\PolicyMakeCommand;

final class MakePolicyCommand extends PolicyMakeCommand
{
    use ModuleAwareGenerator;

    protected string $structureKey = 'src.Policies';
}
