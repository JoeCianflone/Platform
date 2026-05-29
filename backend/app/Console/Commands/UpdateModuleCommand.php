<?php declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;

final class UpdateModuleCommand extends Command
{
    protected $description = 'Rename a module, updating its files and composer registration';

    protected $signature = 'module:update {old : Current module name} {new : New module name} {--force : Skip confirmation prompt}';

    public function handle(): int
    {
        $oldName = Str::studly($this->argument('old'));
        $newName = Str::studly($this->argument('new'));
        $oldLower = Str::lower($oldName);
        $newLower = Str::lower($newName);
        $oldPath = base_path("modules/{$oldName}");
        $newPath = base_path("modules/{$newName}");

        if (! is_dir($oldPath)) {
            $this->error("Module [{$oldName}] does not exist.");

            return self::FAILURE;
        }

        if (is_dir($newPath)) {
            $this->error("Module [{$newName}] already exists.");

            return self::FAILURE;
        }

        if (! $this->option('force') && ! $this->confirm(
            "Rename module [{$oldName}] to [{$newName}]? This updates all namespaces and composer registration.",
            false,
        )) {
            $this->line('Aborted.');

            return self::FAILURE;
        }

        rename($oldPath, $newPath);
        $this->line("  <fg=green>✓</> Renamed modules/{$oldName} → modules/{$newName}");

        $this->renameFiles($newPath, $oldName, $newName, $oldLower, $newLower);
        $this->updateFileContents($newPath, $oldName, $newName, $oldLower, $newLower);
        $this->reregisterModule($oldLower, $newLower);

        $this->info("Module [{$oldName}] renamed to [{$newName}].");

        return self::SUCCESS;
    }

    private function renameFiles(
        string $dir,
        string $oldName,
        string $newName,
        string $oldLower,
        string $newLower,
    ): void {
        $items = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST,
        );

        foreach ($items as $item) {
            if ($item->isDir()) {
                continue;
            }

            $filename = $item->getFilename();
            $newFilename = str_replace(
                [$oldName, $oldLower],
                [$newName, $newLower],
                $filename,
            );

            if ($newFilename !== $filename) {
                rename($item->getPathname(), $item->getPath().'/'.$newFilename);
            }
        }

        $this->line('  <fg=green>✓</> Renamed files');
    }

    private function reregisterModule(string $oldLower, string $newLower): void
    {
        exec("composer remove app/{$oldLower} --no-interaction 2>&1");
        exec("composer require app/{$newLower} @dev --no-interaction 2>&1");
        exec("composer update app/{$newLower} --no-interaction 2>&1");

        $composerJson = json_decode(file_get_contents(base_path('composer.json')) ?: '', true);

        $oldGone = ! isset($composerJson['require']["app/{$oldLower}"]);
        $newPresent = isset($composerJson['require']["app/{$newLower}"]);

        if (! $oldGone || ! $newPresent) {
            $this->warn('  <fg=yellow>!</> Composer reregistration incomplete — run manually:');
            $this->warn("      composer remove app/{$oldLower}");
            $this->warn("      composer require app/{$newLower} @dev");
            $this->warn("      composer update app/{$newLower}");

            return;
        }

        $this->line("  <fg=green>✓</> Reregistered app/{$newLower} in composer.json");
    }

    private function updateFileContents(
        string $dir,
        string $oldName,
        string $newName,
        string $oldLower,
        string $newLower,
    ): void {
        $items = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS),
        );

        $search = [
            "App\\{$oldName}",   // PHP namespace/use statements — must precede bare name
            "App\\\\{$oldName}", // JSON-escaped double backslash
            "app/{$oldLower}",   // composer name field
            $oldName,            // class names, descriptions, remaining occurrences
            $oldLower,           // lowercase occurrences (route comments etc.)
        ];

        $replace = [
            "App\\{$newName}",
            "App\\\\{$newName}",
            "app/{$newLower}",
            $newName,
            $newLower,
        ];

        foreach ($items as $item) {
            if (! $item->isFile()) {
                continue;
            }

            $content = file_get_contents($item->getPathname()) ?: '';
            $updated = str_replace($search, $replace, $content);

            if ($updated !== $content) {
                file_put_contents($item->getPathname(), $updated);
            }
        }

        $this->line('  <fg=green>✓</> Updated namespaces and references');
    }
}
