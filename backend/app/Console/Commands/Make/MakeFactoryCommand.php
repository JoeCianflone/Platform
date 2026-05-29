<?php declare(strict_types=1);

namespace App\Console\Commands\Make;

use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;
use Illuminate\Database\Console\Factories\FactoryMakeCommand;

final class MakeFactoryCommand extends FactoryMakeCommand
{
    use ModuleAwareGenerator;

    protected string $structureKey = 'database.factories';

    protected function buildClass($name): string
    {
        $content = parent::buildClass($name);

        if (! $this->usingModule()) {
            return $content;
        }

        $correct = 'App\\'.Str::studly($this->getModule()).'\\Database\\Factories';

        return preg_replace('/^namespace .+;$/m', 'namespace '.$correct.';', $content, 1) ?? $content;
    }

    /**
     * @return array<mixed>
     */
    protected function getOptions(): array
    {
        return [
            ...parent::getOptions(),
            ['module', null, InputOption::VALUE_REQUIRED, 'Generate within a module'],
        ];
    }

    protected function getPath($name): string
    {
        if (! $this->usingModule()) {
            return parent::getPath($name);
        }

        $className = class_basename($name);

        if (! Str::endsWith($className, 'Factory')) {
            $className .= 'Factory';
        }

        return base_path('modules/'.Str::studly($this->getModule()).'/database/factories/'.$className.'.php');
    }

    protected function rootNamespace(): string
    {
        if (! $this->usingModule()) {
            return parent::rootNamespace();
        }

        return 'App\\'.Str::studly($this->getModule()).'\\';
    }
}
