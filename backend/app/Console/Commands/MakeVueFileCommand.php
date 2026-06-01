<?php declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;

final class MakeVueFileCommand extends Command
{
    protected $description = 'Generate a Vue SFC in the frontend directory';

    protected $signature = 'make:vue-file
                            {path : Relative path from frontend/ including component name (e.g. domains/users/Avatar)}';

    public function handle(): int
    {
        $input = trim($this->argument('path'), '/');
        $segments = explode('/', $input);
        $componentName = array_pop($segments);

        $relativeDir = implode('/', $segments);
        $frontendRoot = dirname(base_path()) . '/frontend';
        $targetDir = $relativeDir !== '' ? "{$frontendRoot}/{$relativeDir}" : $frontendRoot;
        $targetFile = "{$targetDir}/{$componentName}.vue";

        if (file_exists($targetFile)) {
            $this->error("Component already exists: frontend/{$relativeDir}/{$componentName}.vue");

            return self::FAILURE;
        }

        if (! is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $stub = file_get_contents(base_path('stubs/vue-sfc.stub')) ?: '';
        $content = str_replace('Example', $componentName, $stub);

        file_put_contents($targetFile, $content);

        $displayPath = ltrim(str_replace($frontendRoot, 'frontend', $targetFile), '/');
        $this->line("  <fg=green>✓</> Created {$displayPath}");

        return self::SUCCESS;
    }
}
