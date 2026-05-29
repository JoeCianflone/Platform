<?php declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

abstract class ModuleServiceProvider extends ServiceProvider
{
    /** @var array<string> */
    protected static array $moduleSeederClasses = [];

    public function boot(): void
    {
        $routeFile = $this->modulePath().'/'.$this->moduleName().'.routes.php';

        if (is_file($routeFile)) {
            Route::middleware('web')->group(fn () => $this->loadRoutesFrom($routeFile));
        }

        $langPath = $this->modulePath().'/resources/lang';

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->moduleName());
        }
    }

    /**
     * @return array<string>
     */
    public static function moduleSeederClasses(): array
    {
        return static::$moduleSeederClasses;
    }

    public function register(): void
    {
        $this->loadMigrationsFrom($this->modulePath().'/database/migrations');

        $configFile = $this->modulePath().'/src/'.$this->moduleName().'.config.php';

        if (is_file($configFile)) {
            $this->mergeConfigFrom($configFile, $this->moduleName());
        }

        $module = Str::studly($this->moduleName());
        $ns = config('modules.base_namespace');
        $seederClass = "{$ns}\\{$module}\\Database\\Seeders\\{$module}DatabaseSeeder";

        if (class_exists($seederClass)) {
            static::$moduleSeederClasses[] = $seederClass;
        }
    }

    /**
     * Derive the lowercase module name from the ServiceProvider class name.
     * CoffeeServiceProvider → coffee
     */
    protected function moduleName(): string
    {
        return Str::lower(Str::before(class_basename(static::class), 'ServiceProvider'));
    }

    /**
     * Return the module root directory (the folder containing src/, database/, etc.).
     * Concrete providers live in src/, so this is dirname(__DIR__) from there.
     */
    abstract protected function modulePath(): string;
}
