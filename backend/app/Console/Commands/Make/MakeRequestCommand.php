<?php declare(strict_types=1);

namespace App\Console\Commands\Make;

use Illuminate\Foundation\Console\RequestMakeCommand;

final class MakeRequestCommand extends RequestMakeCommand
{
    use ModuleAwareGenerator;

    protected string $structureKey = 'src.Http.Requests';
}
