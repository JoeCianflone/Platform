<?php declare(strict_types=1);

use App\Contracts\DataObject;
use App\Contracts\ValueObject;
use App\Contracts\Snapshot as SnapshotContract;

foreach (moduleNamespaces() as $module) {
    arch("{$module} DataObjects are readonly")
        ->expect("{$module}\\Data\\DomainObjects")
        ->toBeReadonly();

    arch("{$module} Snapshots are readonly")
        ->expect("{$module}\\Data\\Snapshots")
        ->toBeReadonly();

    arch("{$module} ValueObjects are readonly")
        ->expect("{$module}\\Data\\ValueObjects")
        ->toBeReadonly();

    arch("{$module} DataObjects implement DataObject contract")
        ->expect("{$module}\\Data\\DomainObjects")
        ->toImplement(DataObject::class);

    arch("{$module} ValueObjects implement ValueObject contract")
        ->expect("{$module}\\Data\\ValueObjects")
        ->toImplement(ValueObject::class);

    arch("{$module} Snapshots implement Snapshot contract")
        ->expect("{$module}\\Data\\Snapshots")
        ->toImplement(SnapshotContract::class);
}
