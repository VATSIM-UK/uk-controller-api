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
use App\Console\Commands\StandReservationsImport;
use App\Console\Commands\UpdateMetars;
use App\Console\Commands\UpdateVatsimControllerData;
use App\Console\Commands\UpdateVatsimNetworkData;
use App\Console\Commands\UserAdminCreate;
use App\Console\Commands\UserCreate;
use App\Console\Commands\WakeCategoriesImport;
use App\Jobs\Acars\UpdateOnlineCallsigns;
use Bugsnag\BugsnagLaravel\Commands\DeployCommand as BugsnagDeployCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\DeleteExpiredTokens;
use App\Console\Commands\DeleteUserTokens;
use App\Console\Commands\CreateUserToken;
use Spatie\ScheduleMonitor\Models\MonitoredScheduledTaskLogItem;

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
        UpdateVatsimNetworkData::class,
        UpdateVatsimControllerData::class,
        ClearAssignedHoldsHistory::class,
        OptimiseTables::class,
        CleanStandAssignmentsHistory::class,
        WakeCategoriesImport::class,
        StandReservationsImport::class,
        RecatCategoriesImport::class,
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
        $schedule->command('model:prune', ['--model' => MonitoredScheduledTaskLogItem::class])->daily()->doNotMonitor();
        $schedule->command('model:prune')->daily()->doNotMonitor();
        $schedule->command('tokens:delete-expired')->daily()->doNotMonitor();
        $schedule->command('squawks:clean-history')->daily()->doNotMonitor();
        $schedule->command('stands:clean-history')->daily()->doNotMonitor();
        $schedule->command('holds:clean-history')->daily()->doNotMonitor();
        $schedule->command('departure-releases:clean-history')->daily()->doNotMonitor();
        $schedule->command('prenote-messages:clean-history')->daily()->doNotMonitor();
        $schedule->command('missed-approaches:clean-history')->daily()->doNotMonitor();
        $schedule->command('queue:prune-failed --hours=168')->daily()->doNotMonitor();
        $schedule->command('tables:optimise')->daily()->doNotMonitor();
        $schedule->command('networkdata:update')->everyMinute()
            ->graceTimeInMinutes(3)
            ->withoutOverlapping(5);
        $schedule->command('networkdata:update-controllers')->everyMinute()
            ->graceTimeInMinutes(3)
            ->withoutOverlapping(5);
        $schedule->command('horizon:snapshot')->everyFiveMinutes()->doNotMonitor();
        $schedule->command('plugin-events:clean')->everyTenMinutes()->doNotMonitor();
        $schedule->command('metars:update')->everyMinute();
        $schedule->command('database:check-table-updates')->everyMinute();
        $schedule->job(UpdateOnlineCallsigns::class)->everyTwoMinutes()->doNotMonitor();
    }

    protected function bootstrappers()
    {
        return array_merge(
            env('APP_ENV') === 'testing' ? [] : [\Bugsnag\BugsnagLaravel\OomBootstrapper::class],
            parent::bootstrappers(),
        );
    }
}
