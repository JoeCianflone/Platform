<?php declare(strict_types=1);

namespace App\Console\Commands\Concerns;

trait InteractsWithModules
{
    protected function getModule(): ?string
    {
        $value = $this->option('module');

        return is_string($value) ? $value : null;
    }

    /** @return array<string> */
    protected function possibleModules(): array
    {
        $paths = glob(config('modules.path').'/*') ?: [];

        return collect($paths)
            ->filter(fn (string $path) => is_dir($path))
            ->map(fn (string $path) => basename($path))
            ->values()
            ->all();
    }

    /**
     * @phpstan-assert-if-true string $this->getModule()
     */
    protected function usingModule(): bool
    {
        return filled($this->getModule());
    }
}
