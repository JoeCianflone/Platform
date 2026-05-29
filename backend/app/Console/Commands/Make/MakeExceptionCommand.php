<?php declare(strict_types=1);

namespace App\Console\Commands\Make;

use Illuminate\Foundation\Console\ExceptionMakeCommand;

final class MakeExceptionCommand extends ExceptionMakeCommand
{
    use ModuleAwareGenerator;

    protected string $structureKey = 'src.Exceptions';
}
