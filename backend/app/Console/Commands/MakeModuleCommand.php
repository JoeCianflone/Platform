<?php declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Symfony\Component\Yaml\Yaml;

final class MakeModuleCommand extends Command
{
    protected $description = 'Scaffold a new module using module-structure.yml';

    protected $signature = 'make:module {name : Module name in TitleCase (e.g. Coffee)}';

    public function handle(): int
    {
        $name = Str::studly($this->argument('name'));
        $lower = Str::lower($name);
        $modulePath = base_path("modules/{$name}");

        if (is_dir($modulePath)) {
            $this->error("Module [{$name}] already exists.");

            return self::FAILURE;
        }

        mkdir($modulePath, 0755, true);

        $structure = $this->loadStructure();
        $this->walkStructure($structure, $modulePath, $modulePath, $name, $lower);
        $this->createComposerJson($name, $lower, $modulePath);
        $this->registerModule($lower);

        $this->info("Module [{$name}] scaffolded successfully.");

        return self::SUCCESS;
    }

    private function createComposerJson(string $name, string $lower, string $modulePath): void
    {
        $stubPath = base_path('stubs/module-composer.json.stub');
        $content = file_get_contents($stubPath) ?: '';
        $dbFolder = config('modules.database_folder_name');
        $facFolder = config('modules.factories_folder_name');
        $sedFolder = config('modules.seeders_folder_name');

        $content = str_replace(
            [
                '{{ Namespace }}',
                '{{ Name }}',
                '{{ name }}',
                '{{ SrcFolder }}',
                '{{ DatabaseFolder }}',
                '{{ FactoriesFolder }}',
                '{{ SeedersFolder }}',
                '{{ DatabaseNs }}',
                '{{ FactoriesNs }}',
                '{{ SeedersNs }}',
            ],
            [
                config('modules.base_namespace'),
                $name,
                $lower,
                config('modules.modules_src_folder_name'),
                $dbFolder,
                $facFolder,
                $sedFolder,
                Str::studly($dbFolder),
                Str::studly($facFolder),
                Str::studly($sedFolder),
            ],
            $content
        );

        file_put_contents("{$modulePath}/composer.json", $content);
        $this->line('  <fg=green>✓</> Created composer.json');
    }

    private function createFileFromStub(string $yamlKey, string $targetPath, string $name, string $lower): void
    {
        $stubFile = match ($yamlKey) {
            'module.routes.php' => 'module.routes.php.stub',
            'module.config.php' => 'module.config.php.stub',
            'ModuleServiceProvider.php' => 'ServiceProvider.php.stub',
            default => null,
        };

        if ($stubFile === null) {
            $this->warn("  No stub defined for [{$yamlKey}] — skipping.");

            return;
        }

        $stubPath = base_path("stubs/{$stubFile}");

        if (! file_exists($stubPath)) {
            $this->warn("  Stub not found: stubs/{$stubFile} — skipping.");

            return;
        }

        $content = file_get_contents($stubPath) ?: '';
        $content = str_replace(
            ['{{ Namespace }}', '{{ Name }}', '{{ name }}'],
            [config('modules.base_namespace'), $name, $lower],
            $content
        );

        file_put_contents($targetPath, $content);
        $this->line('  <fg=green>✓</> Created '.basename($targetPath));
    }

    /**
     * @return array<string|int, mixed>
     */
    private function loadStructure(): array
    {
        $yaml = Yaml::parseFile(config('modules.structure_path'));

        return $yaml['module'] ?? [];
    }

    private function registerModule(string $lower): void
    {
        exec("composer require app/{$lower} @dev --no-interaction 2>&1", $output);

        $composerJson = json_decode(file_get_contents(base_path('composer.json')) ?: '', true);
        $registered = isset($composerJson['require']["app/{$lower}"]);

        if (! $registered) {
            $this->warn("  <fg=yellow>!</> Failed to auto-register — run: composer require app/{$lower} @dev");

            return;
        }

        $this->line("  <fg=green>✓</> Registered app/{$lower} via composer require");
    }

    private function resolveEntryName(string $yamlKey, string $name, string $lower): string
    {
        return match ($yamlKey) {
            'module.routes.php' => "{$lower}.routes.php",
            'module.config.php' => "{$lower}.config.php",
            'ModuleServiceProvider.php' => "{$name}ServiceProvider.php",
            default => $yamlKey,
        };
    }

    /**
     * @param  array<string|int, mixed>  $structure
     */
    private function walkStructure(
        array $structure,
        string $moduleRoot,
        string $currentPath,
        string $name,
        string $lower,
    ): void {
        foreach ($structure as $yamlKey => $value) {
            $entryName = $this->resolveEntryName((string) $yamlKey, $name, $lower);
            $fullPath = "{$currentPath}/{$entryName}";

            if ($value === 'hide') {
                continue;
            }
            if ($value === 'file') {
                $this->createFileFromStub((string) $yamlKey, $fullPath, $name, $lower);
            } elseif (is_array($value) && empty($value)) {
                mkdir($fullPath, 0755, true);
                file_put_contents("{$fullPath}/.gitkeep", '');
            } else {
                mkdir($fullPath, 0755, true);
                $this->walkStructure($value, $moduleRoot, $fullPath, $name, $lower);
            }
        }
    }
}
