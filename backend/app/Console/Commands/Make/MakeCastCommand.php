<?php declare(strict_types=1);

namespace App\Console\Commands\Make;

use Illuminate\Foundation\Console\CastMakeCommand;

final class MakeCastCommand extends CastMakeCommand
{
    use ModuleAwareGenerator;

    protected string $structureKey = 'src.Eloquent.Casts';
}
