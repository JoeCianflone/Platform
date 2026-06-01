<?php declare(strict_types=1);

namespace App\Console\Commands\Make;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use App\Console\Commands\Concerns\InteractsWithModules;

final class MakeActionCommand extends Command
{
    use InteractsWithModules;

    protected $description = 'Create an Action contract interface and concrete class';

    protected $signature = 'make:action
                            {name : Action name without suffix (e.g. CreateItem)}
                            {--module= : Generate within a module}';

    public function handle(): int
    {
        $name = Str::studly($this->argument('name'));
        $baseNs = config('modules.base_namespace', 'App');

        if ($this->usingModule()) {
            $module = Str::studly($this->getModule());
            $moduleNs = "{$baseNs}\\{$module}";
            $moduleSrc = base_path("modules/{$module}/src");
            $contractNs = "{$moduleNs}\\Contracts\\Actions";
            $contractPath = "{$moduleSrc}/Contracts/Actions/{$name}Action.php";
            $concreteNs = "{$moduleNs}\\Domain\\Actions";
            $concretePath = "{$moduleSrc}/Domain/Actions/{$name}.php";
        } else {
            $contractNs = "{$baseNs}\\Contracts\\Actions";
            $contractPath = app_path("Contracts/Actions/{$name}Action.php");
            $concreteNs = "{$baseNs}\\Domain\\Actions";
            $concretePath = app_path("Domain/Actions/{$name}.php");
        }

        if (file_exists($contractPath) || file_exists($concretePath)) {
            $this->error("Action [{$name}] already exists.");

            return self::FAILURE;
        }

        $this->ensureDirectory(dirname($contractPath));
        $this->ensureDirectory(dirname($concretePath));

        $contractStub = file_get_contents(base_path('stubs/action-contract.stub')) ?: '';
        file_put_contents($contractPath, str_replace(
            ['{{ namespace }}', '{{ class }}'],
            [$contractNs, "{$name}Action"],
            $contractStub,
        ));
        $this->line("  <fg=green>✓</> Created Contracts/Actions/{$name}Action.php");

        $concreteStub = file_get_contents(base_path('stubs/action.stub')) ?: '';
        file_put_contents($concretePath, str_replace(
            ['{{ namespace }}', '{{ class }}', '{{ contractNamespace }}', '{{ contractClass }}'],
            [$concreteNs, $name, $contractNs, "{$name}Action"],
            $concreteStub,
        ));
        $this->line("  <fg=green>✓</> Created Domain/Actions/{$name}.php");

        $this->newLine();
        $this->comment('Add to your ServiceProvider::register():');
        $this->line("  \$this->app->bind(\\{$contractNs}\\{$name}Action::class, \\{$concreteNs}\\{$name}::class);");

        return self::SUCCESS;
    }

    private function ensureDirectory(string $path): void
    {
        if (! is_dir($path)) {
            mkdir($path, 0755, true);
        }
    }
}
