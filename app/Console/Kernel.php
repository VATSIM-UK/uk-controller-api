<?php

namespace App\Console;

use App\Console\Commands\AllocateStandForArrival;
use App\Console\Commands\CleanSquawkAssignmentsHistory;
use App\Console\Commands\CleanStandAssignmentsHistory;
use App\Console\Commands\ClearAssignedHoldsHistory;
use App\Console\Commands\GenerateMinStackLevels;
use App\Console\Commands\GenerateRegionalPressures;
use App\Console\Commands\OptimiseTables;
use App\Console\Commands\RecatCategoriesImport;
use App\Console\Commands\SrdImport;
use App\Console\Commands\StandReservationsImport;
use App\Console\Commands\UpdateSrd;
use App\Console\Commands\UpdateVatsimNetworkData;
use App\Console\Commands\UserAdminCreate;
use App\Console\Commands\UserCreate;
use App\Console\Commands\WakeCategoriesImport;
use App\Services\SrdService;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\DeleteExpiredTokens;
use App\Console\Commands\DeleteUserTokens;
use App\Console\Commands\CreateUserToken;
use App\Console\Commands\DataAdminCreate;

class Kernel extends ConsoleKernel
{
    /**
     * @codeCoverageIgnore
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        CleanSquawkAssignmentsHistory::class,
        CreateUserToken::class,
        DeleteExpiredTokens::class,
        DeleteUserTokens::class,
        GenerateRegionalPressures::class,
        UserAdminCreate::class,
        UserCreate::class,
        \Bugsnag\BugsnagLaravel\Commands\DeployCommand::class,
        GenerateMinStackLevels::class,
        SrdImport::class,
        UpdateVatsimNetworkData::class,
        ClearAssignedHoldsHistory::class,
        OptimiseTables::class,
        CleanStandAssignmentsHistory::class,
        WakeCategoriesImport::class,
        AllocateStandForArrival::class,
        StandReservationsImport::class,
        RecatCategoriesImport::class,
        UpdateSrd::class,
        DataAdminCreate::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @codeCoverageIgnore
     * @param              \Illuminate\Console\Scheduling\Schedule $schedule
     * @return             void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('regional:generate')->hourlyAt([25, 55]);
        $schedule->command('tokens:delete-expired')->daily();
        $schedule->command('squawks:clean-history')->daily();
        $schedule->command('stands:clean-history')->daily();
        $schedule->command('holds:clean-history')->daily();
        $schedule->command('tables:optimise')->daily();
        $schedule->command('msl:generate')->hourlyAt([25, 55]);
        $schedule->command('networkdata:update')->everyMinute()->withoutOverlapping(5);
        $schedule->command('stands:assign-arrival')->everyTwoMinutes();
        $schedule->command('srd:update')
            ->hourly()
            ->when(function () {
                return $this->app->make(SrdService::class)->newSrdShouldBeAvailable();
            });
        $schedule->command('horizon:snapshot')->everyFiveMinutes();
    }
}
