<?php declare(strict_types=1);

namespace App\Tenant\Support;

use Illuminate\Support\Facades\DB;
use App\Tenant\Data\Snapshots\TenantSnapshot;

final class TenantConnectionManager
{
    public function connect(TenantSnapshot $tenant): void
    {
        $default = config('database.connections.' . config('database.default'));

        config(['database.connections.tenant' => array_merge($default, [
            'database' => $this->databaseNameFor($tenant),
        ])]);

        DB::purge('tenant');
        DB::reconnect('tenant');
    }

    public function databaseNameFor(TenantSnapshot $tenant): string
    {
        return config('tenant.db_prefix') . '_tenant_' . $tenant->id;
    }

    public function disconnect(): void
    {
        DB::purge('tenant');
    }
}
