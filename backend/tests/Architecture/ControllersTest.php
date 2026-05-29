<?php declare(strict_types=1);

use App\Http\Controllers\Controller;

foreach (moduleNamespaces() as $module) {
    arch("{$module} controllers are invokable")
        ->expect("{$module}\\Http\\Controllers")
        ->toBeInvokable();

    arch("{$module} controllers extend base Controller")
        ->expect("{$module}\\Http\\Controllers")
        ->toExtend(Controller::class);

    arch("{$module} controllers use contracts not domain implementations")
        ->expect("{$module}\\Http\\Controllers")
        ->not->toUse("{$module}\\Domain");
}

arch('no direct Inertia renders anywhere in the app')
    ->expect('App')
    ->not->toUse('Inertia\\Inertia');
