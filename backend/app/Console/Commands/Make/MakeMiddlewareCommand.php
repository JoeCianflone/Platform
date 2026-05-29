<?php declare(strict_types=1);

namespace App\Console\Commands\Make;

use Illuminate\Routing\Console\MiddlewareMakeCommand;

final class MakeMiddlewareCommand extends MiddlewareMakeCommand
{
    use ModuleAwareGenerator;

    protected string $structureKey = 'src.Http.Middleware';
}
