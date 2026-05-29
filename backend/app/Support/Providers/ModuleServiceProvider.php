<?php declare(strict_types=1);

namespace App\Support\Providers;

use Illuminate\Support\Str;
use Illuminate\Support\ServiceProvider;

abstract class ModuleServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom($this->modulePath().'/'.$this->moduleName().'.routes.php');
    }

    public function register(): void
    {
        $this->loadMigrationsFrom($this->modulePath().'/database/migrations');
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
