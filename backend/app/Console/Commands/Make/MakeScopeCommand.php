<?php declare(strict_types=1);

namespace App\Console\Commands\Make;

use Illuminate\Foundation\Console\ScopeMakeCommand;

final class MakeScopeCommand extends ScopeMakeCommand
{
    use ModuleAwareGenerator;

    protected string $structureKey = 'src.Domain.Scopes';
}
