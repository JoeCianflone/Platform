<?php declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;

final class MakeComposableCommand extends Command
{
    protected $description = 'Generate a Vue composable in frontend/composables/';

    protected $signature = 'make:composable {name : Composable name with or without "use" prefix (e.g. Auth or useAuth)}';

    public function handle(): int
    {
        $name = $this->argument('name');
        $camel = Str::camel($name);
        $composableName = str_starts_with($camel, 'use') ? $camel : 'use'.Str::ucfirst($camel);

        $frontendRoot = dirname(base_path()).'/frontend';
        $targetPath = "{$frontendRoot}/composables/{$composableName}.ts";

        if (file_exists($targetPath)) {
            $this->error("Composable [{$composableName}.ts] already exists.");

            return self::FAILURE;
        }

        $stub = file_get_contents(base_path('stubs/composable.stub')) ?: '';
        $functionName = $composableName;
        $displayName = Str::ucfirst(Str::after($composableName, 'use'));
        $content = str_replace('{{ name }}', $displayName, $stub);

        file_put_contents($targetPath, $content);
        $this->line("  <fg=green>✓</> Created frontend/composables/{$composableName}.ts");

        return self::SUCCESS;
    }
}
