<?php declare(strict_types=1);

namespace App\Tenant\Jobs;

use Illuminate\Bus\Queueable;
use App\Tenant\Enums\TenantStatus;
use Illuminate\Support\Facades\DB;
use App\Tenant\Eloquent\Models\Tenant;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Tenant\Data\Snapshots\TenantSnapshot;
use App\Tenant\Domain\Events\TenantProvisioned;
use App\Tenant\Support\TenantConnectionManager;

final class ProvisionTenantDatabase implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(public readonly TenantSnapshot $tenant)
    {
        $this->onQueue('high');
    }

    public function handle(TenantConnectionManager $connections): void
    {
        $database = $connections->databaseNameFor($this->tenant);

        DB::statement("CREATE DATABASE IF NOT EXISTS `{$database}`");

        $connections->connect($this->tenant);

        Artisan::call('migrate', [
            '--database' => 'tenant',
            '--path' => dirname(__DIR__, 2) . '/database/tenant-migrations',
            '--realpath' => true,
            '--force' => true,
        ]);

        $seederClass = 'App\\Tenant\\Database\\Seeders\\NewTenantSeeder';

        if (class_exists($seederClass)) {
            Artisan::call('db:seed', [
                '--database' => 'tenant',
                '--class' => $seederClass,
                '--force' => true,
            ]);
        }

        $tenant = Tenant::findOrFail($this->tenant->id);
        $tenant->update(['status' => TenantStatus::ACTIVE]);

        $snapshot = $tenant->fresh()->toSnapshot();

        event(new TenantProvisioned($snapshot));
    }
}
