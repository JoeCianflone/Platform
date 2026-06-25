<?php declare(strict_types=1);

use App\Tenant\Enums\TenantStatus;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('domain')->nullable()->unique();
            $table->string('status')->default(TenantStatus::Pending->value);
            $table->string('stripe_id')->nullable()->index();
            $table->string('pm_type')->nullable();
            $table->string('pm_last_four', 4)->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamps();
        });
    }
};
