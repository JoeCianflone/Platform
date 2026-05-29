<?php declare(strict_types=1);

namespace App\Providers;

use App\Modules\ModulePathResolver;
use Illuminate\Support\ServiceProvider;
use App\Modules\ModuleNamespaceResolver;
use App\Modules\ModuleStructureRepository;
use App\Console\Commands\Make\MakeJobCommand;
use App\Console\Commands\Make\MakeCastCommand;
use App\Console\Commands\Make\MakeEnumCommand;
use App\Console\Commands\Make\MakeMailCommand;
use App\Console\Commands\Make\MakeRuleCommand;
use App\Console\Commands\Make\MakeTestCommand;
use App\Console\Commands\Make\MakeEventCommand;
use App\Console\Commands\Make\MakeModelCommand;
use App\Console\Commands\Make\MakeScopeCommand;
use App\Console\Commands\Make\MakeTraitCommand;
use App\Console\Commands\Make\MakePolicyCommand;
use App\Console\Commands\Make\MakeSeederCommand;
use App\Console\Commands\Make\MakeArtisanCommand;
use App\Console\Commands\Make\MakeFactoryCommand;
use App\Console\Commands\Make\MakeRequestCommand;
use App\Console\Commands\Make\MakeListenerCommand;
use App\Console\Commands\Make\MakeObserverCommand;
use App\Console\Commands\Make\MakeExceptionCommand;
use App\Console\Commands\Make\MakeMigrationCommand;
use App\Console\Commands\Make\MakeControllerCommand;
use App\Console\Commands\Make\MakeDataObjectCommand;
use App\Console\Commands\Make\MakeMiddlewareCommand;
use App\Console\Commands\Make\MakeValueObjectCommand;
use App\Console\Commands\Make\MakeNotificationCommand;

class ModularArtisanServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->commands([
            MakeArtisanCommand::class,
            MakeDataObjectCommand::class,
            MakeCastCommand::class,
            MakeControllerCommand::class,
            MakeEnumCommand::class,
            MakeEventCommand::class,
            MakeExceptionCommand::class,
            MakeFactoryCommand::class,
            MakeJobCommand::class,
            MakeListenerCommand::class,
            MakeMailCommand::class,
            MakeMigrationCommand::class,
            MakeMiddlewareCommand::class,
            MakeModelCommand::class,
            MakeNotificationCommand::class,
            MakeObserverCommand::class,
            MakePolicyCommand::class,
            MakeRequestCommand::class,
            MakeRuleCommand::class,
            MakeScopeCommand::class,
            MakeSeederCommand::class,
            MakeTestCommand::class,
            MakeTraitCommand::class,
            MakeValueObjectCommand::class,
        ]);
    }

    public function register(): void
    {
        $this->app->singleton(ModuleStructureRepository::class);
        $this->app->singleton(ModulePathResolver::class);
        $this->app->singleton(ModuleNamespaceResolver::class);

        // MigrationCreator requires $customStubPath — can't auto-resolve; use existing binding
        $this->app->singleton(MakeMigrationCommand::class, function ($app) {
            return new MakeMigrationCommand($app['migration.creator'], $app['composer']);
        });
    }
}
