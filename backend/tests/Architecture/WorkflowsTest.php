<?php declare(strict_types=1);

arch('no Workflow classes exist inside modules')
    ->expect('App')
    ->not->toHaveSuffix('Workflow')
    ->ignoring('App\\Workflows');

foreach (moduleNamespaces() as $module) {
    arch("App\\Workflows does not use {$module} domain implementations directly")
        ->expect('App\\Workflows')
        ->not->toUse("{$module}\\Domain");
}
