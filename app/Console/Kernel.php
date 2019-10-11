<?php

namespace App\Console;

use App\Console\Commands\CleanSquawkAllocationHistory;
use App\Console\Commands\CleanSquawkAllocations;
use App\Console\Commands\ClearSquawkAllocations;
use App\Console\Commands\GenerateLegacyDependencies;
use App\Console\Commands\GenerateMinStackLevels;
use App\Console\Commands\GenerateRegionalPressures;
use App\Console\Commands\GetDeletedSidsFromSectorFile;
use App\Console\Commands\UserAdminCreate;
use App\Console\Commands\UserCreate;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\DeleteExpiredTokens;
use App\Console\Commands\DeleteUserTokens;
use App\Console\Commands\CreateUserToken;

class Kernel extends ConsoleKernel
{
    /**
     * @codeCoverageIgnore
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        CleanSquawkAllocationHistory::class,
        CleanSquawkAllocations::class,
        ClearSquawkAllocations::class,
        CreateUserToken::class,
        DeleteExpiredTokens::class,
        DeleteUserTokens::class,
        GenerateRegionalPressures::class,
        UserAdminCreate::class,
        UserCreate::class,
        \Bugsnag\BugsnagLaravel\Commands\DeployCommand::class,
        GenerateMinStackLevels::class,
        GenerateLegacyDependencies::class,
        GetDeletedSidsFromSectorFile::class,
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
        $schedule->command('allocations:clean')->everyFifteenMinutes();
        $schedule->command('regionals:generate')->hourlyAt('10');
        $schedule->command('tokens:delete-expired')->daily();
        $schedule->command('allocations:clean-history')->daily();
        $schedule->command('msl:generate')->hourlyAt('25')->hourlyAt('55');
    }
}
