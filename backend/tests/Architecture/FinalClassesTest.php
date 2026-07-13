<?php declare(strict_types=1);

$subNamespaces = [
    'Http\\Controllers',
    'Domain\\Actions',
    'Domain\\Queries',
    'Domain\\Queries\\Projections',
    'Domain\\Scopes',
    'Domain\\Events',
    'Domain\\Listeners',
    'Data\\DataObjects',
    'Data\\Snapshots',
    'Data\\Collections',
    'Data\\ValueObjects',
];

foreach (moduleNamespaces() as $module) {
    foreach ($subNamespaces as $sub) {
        arch("{$module}\\{$sub} are final")
            ->expect("{$module}\\{$sub}")
            ->classes()
            ->toBeFinal();
    }
}

arch('App\\Workflows are final')
    ->expect('App\\Workflows')
    ->classes()
    ->toBeFinal();
