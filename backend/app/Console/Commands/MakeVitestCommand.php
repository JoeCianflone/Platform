<?php declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;

final class MakeVitestCommand extends Command
{
    protected $description = 'Generate a Vitest test file alongside a frontend component';

    protected $signature = 'make:vitest
                            {path : Component path from frontend/ including name (e.g. domains/users/Avatar)}';

    public function handle(): int
    {
        $input = trim($this->argument('path'), '/');
        $segments = explode('/', $input);
        $name = array_pop($segments);
        $relativeDir = implode('/', $segments);

        $frontendRoot = dirname(base_path()).'/frontend';
        $testDir = $relativeDir !== ''
            ? "{$frontendRoot}/{$relativeDir}/__tests__"
            : "{$frontendRoot}/__tests__";
        $targetPath = "{$testDir}/{$name}.test.ts";

        if (file_exists($targetPath)) {
            $this->error("Test [{$name}.test.ts] already exists.");

            return self::FAILURE;
        }

        if (! is_dir($testDir)) {
            mkdir($testDir, 0755, true);
        }

        $stub = file_get_contents(base_path('stubs/vitest.stub')) ?: '';
        $content = str_replace('{{ name }}', $name, $stub);

        file_put_contents($targetPath, $content);

        $displayPath = ltrim(str_replace($frontendRoot, 'frontend', $targetPath), '/');
        $this->line("  <fg=green>✓</> Created {$displayPath}");

        return self::SUCCESS;
    }
}
