<?php declare(strict_types=1);

namespace App\Console\Commands\Make;

use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;
use App\Console\Commands\Concerns\InteractsWithModules;
use Illuminate\Database\Console\Migrations\MigrateMakeCommand;

final class MakeMigrationCommand extends MigrateMakeCommand
{
    use InteractsWithModules;

    protected function configure(): void
    {
        parent::configure();

        $this->addOption('module', null, InputOption::VALUE_REQUIRED, 'Generate within a module');
    }

    protected function getMigrationPath(): mixed
    {
        if (! $this->usingModule()) {
            return parent::getMigrationPath();
        }

        return base_path('modules/'.Str::studly($this->getModule()).'/database/migrations');
    }
}
