<?php declare(strict_types=1);

$subNamespaces = [
    'Http\\Controllers',
    'Domain\\Actions',
    'Domain\\Queries',
    'Domain\\Queries\\Projections',
    'Domain\\Scopes',
    'Domain\\Events',
    'Domain\\Listeners',
    'Data\\DomainObjects',
    'Data\\Snapshots',
    'Data\\Collections',
    'Data\\ValueObjects',
];

foreach (moduleNamespaces() as $module) {
    foreach ($subNamespaces as $sub) {
        arch("{$module}\\{$sub} are final")
            ->expect("{$module}\\{$sub}")
            ->toBeFinal();
    }
}

arch('App\\Workflows are final')
    ->expect('App\\Workflows')
    ->toBeFinal();
