<?php

namespace App\Providers;

use App\Support\Health\ScheduledTasksCheck;
use Illuminate\Support\ServiceProvider;
use Spatie\Health\Checks\Checks\DatabaseCheck;
use Spatie\Health\Checks\Checks\HorizonCheck;
use Spatie\Health\Checks\Checks\RedisCheck;
use Spatie\Health\Checks\Checks\ScheduleCheck;
use Spatie\Health\Facades\Health;

class HealthServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Health::checks([
            DatabaseCheck::new()->name('database'),
            RedisCheck::new()->name('redis'),
            HorizonCheck::new()->name('horizon'),
            ScheduleCheck::new()->name('schedule')->heartbeatMaxAgeInMinutes(3),
            ScheduledTasksCheck::new()->name('scheduled_tasks'),
        ]);
    }
}
