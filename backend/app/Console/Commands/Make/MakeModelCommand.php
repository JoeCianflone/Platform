<?php declare(strict_types=1);

namespace App\Console\Commands\Make;

use Illuminate\Foundation\Console\ModelMakeCommand;

final class MakeModelCommand extends ModelMakeCommand
{
    use ModuleAwareGenerator;

    protected string $structureKey = 'src.Eloquent.Models';

    /**
     * @return array<string, string>
     */
    protected function buildFactoryReplacements(): array
    {
        if (! ($this->option('factory') || $this->option('all'))) {
            return [
                '{{ factory }}'              => '',
                "{{ factory }}\n"            => '',
                '{{ factoryImport }}'        => '',
                "{{ factoryImport }}\n"      => '',
                "{{ factoryImport }}\r\n"    => '',
                '{{ factoryAttribute }}'     => '',
                "{{ factoryAttribute }}\n"   => '',
                "{{ factoryAttribute }}\r\n" => '',
            ];
        }

        $modelName   = class_basename($this->qualifyClass($this->getNameInput()));
        $factoryFqcn = $this->usingModule()
            ? trim($this->rootNamespace(), '\\').'\\Database\\Factories\\'.$modelName.'Factory'
            : 'Database\\Factories\\'.$modelName.'Factory';

        return [
            '{{ factory }}'          => 'use HasFactory;',
            '{{ factoryImport }}'    => implode("\n", [
                "use {$factoryFqcn};",
                'use Illuminate\\Database\\Eloquent\\Attributes\\UseFactory;',
                'use Illuminate\\Database\\Eloquent\\Factories\\HasFactory;',
            ]),
            '{{ factoryAttribute }}' => "#[UseFactory({$modelName}Factory::class)]",
        ];
    }
}
