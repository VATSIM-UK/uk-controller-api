<?php
namespace App\Providers;

use App\Services\StandService;
use Illuminate\Support\ServiceProvider;

class StandServiceProvider extends ServiceProvider
{
    /**
     * Registers the StandService with the app as a singleton
     */
    public function register()
    {
        $this->app->singleton(StandService::class);
    }

    /**
     * Tells the framework what services this provider provides.
     *
     * @return array Array of provided services.
     */
    public function provides()
    {
        return [
            StandService::class,
        ];
    }
}
