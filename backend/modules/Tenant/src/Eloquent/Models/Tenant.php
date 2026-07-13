<?php declare(strict_types=1);

namespace App\Tenant\Eloquent\Models;

use Laravel\Cashier\Billable;
use App\Tenant\Enums\TenantStatus;
use Illuminate\Database\Eloquent\Model;
use App\Tenant\Data\Snapshots\TenantSnapshot;
use App\Tenant\Database\Factories\TenantFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;

#[UseFactory(TenantFactory::class)]
final class Tenant extends Model
{
    use Billable;
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'name',
        'slug',
        'domain',
        'status',
    ];

    public function toSnapshot(): TenantSnapshot
    {
        return new TenantSnapshot(
            id: $this->id,
            name: $this->name,
            slug: $this->slug,
            domain: $this->domain,
            status: $this->status,
        );
    }

    /** @return array<string, mixed> */
    protected function casts(): array
    {
        return [
            'status' => TenantStatus::class,
        ];
    }
}
