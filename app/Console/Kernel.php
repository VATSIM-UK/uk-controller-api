<?php

namespace App\Console;

use App\Console\Commands\AllocateStandForArrival;
use App\Console\Commands\CheckForKeyTableUpdates;
use App\Console\Commands\CleanDepartureReleaseRequestHistory;
use App\Console\Commands\CleanMissedApproachNotifications;
use App\Console\Commands\CleanPluginEvents;
use App\Console\Commands\CleanPrenoteMessageHistory;
use App\Console\Commands\CleanSquawkAssignmentsHistory;
use App\Console\Commands\CleanStandAssignmentsHistory;
use App\Console\Commands\ClearAssignedHoldsHistory;
use App\Console\Commands\OptimiseTables;
use App\Console\Commands\RecatCategoriesImport;
use App\Console\Commands\SrdImport;
use App\Console\Commands\StandReservationsImport;
use App\Console\Commands\UpdateMetars;
use App\Console\Commands\UpdateSrd;
use App\Console\Commands\UpdateVatsimControllerData;
use App\Console\Commands\UpdateVatsimNetworkData;
use App\Console\Commands\UserAdminCreate;
use App\Console\Commands\UserCreate;
use App\Console\Commands\WakeCategoriesImport;
use Bugsnag\BugsnagLaravel\Commands\DeployCommand as BugsnagDeployCommand;
use Illuminate\Console\Scheduling\Schedule;
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
        UserAdminCreate::class,
        UserCreate::class,
        SrdImport::class,
        UpdateVatsimNetworkData::class,
        UpdateVatsimControllerData::class,
        ClearAssignedHoldsHistory::class,
        OptimiseTables::class,
        CleanStandAssignmentsHistory::class,
        WakeCategoriesImport::class,
        AllocateStandForArrival::class,
        StandReservationsImport::class,
        RecatCategoriesImport::class,
        UpdateSrd::class,
        DataAdminCreate::class,
        UpdateMetars::class,
        CleanPluginEvents::class,
        CleanDepartureReleaseRequestHistory::class,
        CleanPrenoteMessageHistory::class,
        CleanMissedApproachNotifications::class,
        CheckForKeyTableUpdates::class,
        BugsnagDeployCommand::class,
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
        $schedule->command('tokens:delete-expired')->daily();
        $schedule->command('squawks:clean-history')->daily();
        $schedule->command('stands:clean-history')->daily();
        $schedule->command('holds:clean-history')->daily();
        $schedule->command('departure-releases:clean-history')->daily();
        $schedule->command('prenote-messages:clean-history')->daily();
        $schedule->command('missed-approaches:clean-history')->daily();
        $schedule->command('queue:prune-failed --hours=168')->daily();
        $schedule->command('tables:optimise')->daily();
        $schedule->command('networkdata:update')->everyMinute()
            ->graceTimeInMinutes(3)
            ->withoutOverlapping(5);
        $schedule->command('networkdata:update-controllers')->everyMinute()
            ->graceTimeInMinutes(3)
            ->withoutOverlapping(5);
        $schedule->command('stands:assign-arrival')->everyTwoMinutes();
        $schedule->command('schedule-monitor:clean')
            ->dailyAt('08:01');
        $schedule->command('srd:update')
            ->cron('0 1-7 * * *');
        $schedule->command('horizon:snapshot')->everyFiveMinutes();
        $schedule->command('plugin-events:clean')->everyTenMinutes();
        $schedule->command('metars:update')->everyMinute();
        $schedule->command('database:check-table-updates')->everyMinute();
    }

    protected function bootstrappers()
    {
        return array_merge(
            [\Bugsnag\BugsnagLaravel\OomBootstrapper::class],
            parent::bootstrappers(),
        );
    }
}
