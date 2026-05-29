<?php declare(strict_types=1);

$modules = moduleNamespaces();

$crossModuleForbidden = [
    'Domain\\Actions',
    'Domain\\Queries',
    'Domain\\Scopes',
    'Eloquent\\Models',
    'Data\\DomainObjects',
    'Data\\Snapshots',
    'Contracts',
];

foreach ($modules as $module) {
    // Eloquent models are strictly contained — the golden rule
    arch("{$module} Eloquent models only used within {$module}")
        ->expect("{$module}\\Eloquent\\Models")
        ->toOnlyBeUsedIn($module);

    foreach ($modules as $other) {
        if ($module === $other) {
            continue;
        }

        // No module reaches into another module's domain layer.
        // Listeners are excluded — they handle cross-module events and get their own rule.
        arch("{$module} does not use {$other} domain implementations")
            ->expect($module)
            ->not->toUse("{$other}\\Domain")
            ->ignoring("{$module}\\Domain\\Listeners");

        // DataObjects are internal — cross-module data crosses via Snapshots inside Events
        arch("{$module} does not use {$other} DataObjects directly")
            ->expect($module)
            ->not->toUse("{$other}\\Data\\DomainObjects");

        // Listeners may ONLY import Domain\Events from other modules.
        // All other cross-module internals are forbidden even for listeners.
        $forbiddenListenerImports = array_map(
            fn (string $sub): string => "{$other}\\{$sub}",
            $crossModuleForbidden
        );

        arch("{$module} listeners do not reach into {$other} internals")
            ->expect("{$module}\\Domain\\Listeners")
            ->not->toUse($forbiddenListenerImports);
    }
}
