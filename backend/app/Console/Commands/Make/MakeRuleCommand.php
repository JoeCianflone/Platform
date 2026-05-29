<?php declare(strict_types=1);

namespace App\Console\Commands\Make;

use Illuminate\Foundation\Console\RuleMakeCommand;

final class MakeRuleCommand extends RuleMakeCommand
{
    use ModuleAwareGenerator;

    protected string $structureKey = 'src.Rules';
}
