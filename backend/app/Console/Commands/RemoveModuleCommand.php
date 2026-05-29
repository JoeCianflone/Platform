<?php declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;

final class RemoveModuleCommand extends Command
{
    protected $description = 'Remove a module, its files, and its composer registration';

    protected $signature = 'module:remove {name : Module name in TitleCase (e.g. Coffee)} {--force : Skip confirmation prompt}';

    public function handle(): int
    {
        $name = Str::studly($this->argument('name'));
        $lower = Str::lower($name);
        $modulePath = base_path("modules/{$name}");

        if (! is_dir($modulePath)) {
            $this->error("Module [{$name}] does not exist.");

            return self::FAILURE;
        }

        if (! $this->option('force') && ! $this->confirmRemoval($name)) {
            $this->line('Aborted.');

            return self::FAILURE;
        }

        $this->deregisterModule($lower);
        $this->deleteDirectory($modulePath);

        $this->info("Module [{$name}] removed.");

        return self::SUCCESS;
    }

    private function confirmRemoval(string $name): bool
    {
        return $this->confirm(
            "This will permanently delete modules/{$name} and remove it from composer.json. Continue?",
            false,
        );
    }

    private function deleteDirectory(string $path): void
    {
        $name = basename($path);

        if (! is_dir($path)) {
            return;
        }

        $items = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST,
        );

        foreach ($items as $item) {
            $item->isDir() ? rmdir($item->getPathname()) : unlink($item->getPathname());
        }

        rmdir($path);

        $this->line("  <fg=green>✓</> Deleted modules/{$name}");
    }

    private function deregisterModule(string $lower): void
    {
        exec("composer remove app/{$lower} --no-interaction 2>&1", $output);

        $composerJson = json_decode(file_get_contents(base_path('composer.json')) ?: '', true);
        $removed = ! isset($composerJson['require']["app/{$lower}"]);

        if (! $removed) {
            $this->warn("  <fg=yellow>!</> Failed to deregister — run: composer remove app/{$lower}");

            return;
        }

        $this->line("  <fg=green>✓</> Removed app/{$lower} from composer.json");
    }
}
