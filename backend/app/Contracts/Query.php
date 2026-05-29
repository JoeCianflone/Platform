<?php declare(strict_types=1);

namespace App\Contracts;

interface Query
{
    public function handle(mixed ...$data): DataObject|Snapshot;
}
