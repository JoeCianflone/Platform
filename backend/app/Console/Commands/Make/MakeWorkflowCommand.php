<?php declare(strict_types=1);

namespace App\Console\Commands\Make;

use Illuminate\Console\GeneratorCommand;

final class MakeWorkflowCommand extends GeneratorCommand
{
    protected $description = 'Create a new Workflow class in App\\Workflows';

    protected $name = 'make:workflow';

    protected $type = 'Workflow';

    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace.'\\Workflows';
    }

    protected function getNameInput(): string
    {
        $name = parent::getNameInput();

        return str_ends_with($name, 'Workflow') ? $name : $name.'Workflow';
    }

    protected function getStub(): string
    {
        return base_path('stubs/workflow.stub');
    }
}
