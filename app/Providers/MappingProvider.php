<?php

namespace App\Providers;

use App\Services\MappingService;
use App\Services\VrpService;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class MappingProvider extends ServiceProvider
{
    public function boot()
    {
        $this->app->singleton(MappingService::class, function (Application $application) {
            return new MappingService(
                [
                    $application->make(VrpService::class)
                ]
            );
        });
    }
}
