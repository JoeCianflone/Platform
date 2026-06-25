<?php declare(strict_types=1);

namespace App\Tenant\Database\Factories;

use App\Tenant\Enums\TenantStatus;
use App\Tenant\Eloquent\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\Attributes\UseModel;

/**
 * @extends Factory<Tenant>
 */
#[UseModel(Tenant::class)]
final class TenantFactory extends Factory
{
    public function definition(): array
    {
        $name = $this->faker->company();

        return [
            'name'   => $name,
            'slug'   => str($name)->slug()->toString(),
            'domain' => null,
            'status' => TenantStatus::Active,
        ];
    }

    public function pending(): static
    {
        return $this->state(['status' => TenantStatus::Pending]);
    }

    public function suspended(): static
    {
        return $this->state(['status' => TenantStatus::Suspended]);
    }

    public function withDomain(string $domain): static
    {
        return $this->state(['domain' => $domain]);
    }
}
