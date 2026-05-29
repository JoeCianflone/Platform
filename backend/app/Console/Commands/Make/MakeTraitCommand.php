<?php declare(strict_types=1);

namespace App\Console\Commands\Make;

use Illuminate\Foundation\Console\TraitMakeCommand;

final class MakeTraitCommand extends TraitMakeCommand
{
    use ModuleAwareGenerator;

    protected string $structureKey = 'src.Concerns';
}
