<?php

namespace App\Listeners\Database;

use App\Services\DependencyService;
use Illuminate\Database\Events\MigrationsEnded;

class MigrationsFinished
{
    public function handle()
    {
        DependencyService::checkForDependencyUpdates();
    }
}
