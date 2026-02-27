<?php

namespace App\Providers;

use App\Console\Commands\CheckDepreciatedAssetsCommand;
use App\Console\Commands\CheckLeasingExpiryCommand;
use Carbon\CarbonImmutable;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();

        // 2. Planification des tâches (Standard Laravel 12)
        $this->callAfterResolving(Schedule::class, function (Schedule $schedule) {

            // Vérification quotidienne des actifs amortis (pour déclencher la notification)
            $schedule->command(CheckDepreciatedAssetsCommand::class)->dailyAt('08:00');

            // Vérification hebdomadaire des leasings arrivant à échéance (sous 30 jours)
            $schedule->command(CheckLeasingExpiryCommand::class)->weeklyOn(1, '09:00');

            $schedule->command('app:check-maintenance')->daily();

            // Nettoyage automatique des modèles "soft deleted" depuis plus de 30 jours
            $schedule->command('model:prune')->daily();
        });
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
