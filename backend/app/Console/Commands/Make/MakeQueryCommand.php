<?php declare(strict_types=1);

namespace App\Console\Commands\Make;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use App\Console\Commands\Concerns\InteractsWithModules;

final class MakeQueryCommand extends Command
{
    use InteractsWithModules;

    protected $description = 'Create a Query contract, concrete class, and Projection';

    protected $signature = 'make:query
                            {name : Entity name (e.g. Item)}
                            {--module= : Generate within a module}';

    public function handle(): int
    {
        $name = Str::studly($this->argument('name'));
        $baseNs = config('modules.base_namespace', 'App');

        if ($this->usingModule()) {
            $module = Str::studly($this->getModule());
            $moduleNs = "{$baseNs}\\{$module}";
            $moduleSrc = base_path("modules/{$module}/src");

            $contractNs = "{$moduleNs}\\Contracts\\Queries";
            $contractPath = "{$moduleSrc}/Contracts/Queries/{$name}QueryContract.php";

            $queryNs = "{$moduleNs}\\Domain\\Queries";
            $queryPath = "{$moduleSrc}/Domain/Queries/{$name}Query.php";

            $projectionNs = "{$moduleNs}\\Domain\\Queries\\Projections";
            $projectionPath = "{$moduleSrc}/Domain/Queries/Projections/{$name}Projection.php";
        } else {
            $contractNs = "{$baseNs}\\Contracts\\Queries";
            $contractPath = app_path("Contracts/Queries/{$name}QueryContract.php");

            $queryNs = "{$baseNs}\\Domain\\Queries";
            $queryPath = app_path("Domain/Queries/{$name}Query.php");

            $projectionNs = "{$baseNs}\\Domain\\Queries\\Projections";
            $projectionPath = app_path("Domain/Queries/Projections/{$name}Projection.php");
        }

        if (file_exists($contractPath) || file_exists($queryPath)) {
            $this->error("Query [{$name}] already exists.");

            return self::FAILURE;
        }

        $this->ensureDirectory(dirname($contractPath));
        $this->ensureDirectory(dirname($queryPath));
        $this->ensureDirectory(dirname($projectionPath));

        $contractStub = file_get_contents(base_path('stubs/query-contract.stub')) ?: '';
        file_put_contents($contractPath, str_replace(
            ['{{ namespace }}', '{{ class }}'],
            [$contractNs, "{$name}QueryContract"],
            $contractStub,
        ));
        $this->line("  <fg=green>✓</> Created Contracts/Queries/{$name}QueryContract.php");

        $queryStub = file_get_contents(base_path('stubs/query.stub')) ?: '';
        file_put_contents($queryPath, str_replace(
            ['{{ namespace }}', '{{ class }}', '{{ contractNamespace }}', '{{ contractClass }}'],
            [$queryNs, "{$name}Query", $contractNs, "{$name}QueryContract"],
            $queryStub,
        ));
        $this->line("  <fg=green>✓</> Created Domain/Queries/{$name}Query.php");

        $projectionStub = file_get_contents(base_path('stubs/projection.stub')) ?: '';
        file_put_contents($projectionPath, str_replace(
            ['{{ namespace }}', '{{ class }}'],
            [$projectionNs, "{$name}Projection"],
            $projectionStub,
        ));
        $this->line("  <fg=green>✓</> Created Domain/Queries/Projections/{$name}Projection.php");

        $this->newLine();
        $this->comment('Add to your ServiceProvider::register():');
        $this->line("  \$this->app->bind(\\{$contractNs}\\{$name}QueryContract::class, \\{$queryNs}\\{$name}Query::class);");

        return self::SUCCESS;
    }

    private function ensureDirectory(string $path): void
    {
        if (! is_dir($path)) {
            mkdir($path, 0755, true);
        }
    }
}
