<?php declare(strict_types=1);

namespace App\Console\Commands\Make;

use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;
use App\Console\Commands\Concerns\InteractsWithModules;

trait ModuleAwareGenerator
{
    use InteractsWithModules;

    protected function getDefaultNamespace($rootNamespace): string
    {
        if (! $this->usingModule() || ! str_starts_with($this->structureKey, 'src.')) {
            return parent::getDefaultNamespace($rootNamespace);
        }

        $suffix = str_replace('.', '\\', Str::after($this->structureKey, 'src.'));

        return $rootNamespace.'\\'.$suffix;
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

        $modulesFolder = config('modules.modules_folder_name');
        $srcFolder = config('modules.modules_src_folder_name');
        $relative = str_replace('\\', '/', Str::replaceFirst($this->moduleNamespace(), '', $name));

        return base_path("{$modulesFolder}/".Str::studly($this->getModule())."/{$srcFolder}/".$relative.'.php');
    }

    protected function moduleNamespace(): string
    {
        $module = $this->getModule();
        assert($module !== null, '--module is required to call moduleNamespace()');

        return config('modules.base_namespace').'\\'.Str::studly($module).'\\';
    }

    protected function rootNamespace(): string
    {
        if (! $this->usingModule()) {
            return parent::rootNamespace();
        }

        return $this->moduleNamespace();
    }
}
