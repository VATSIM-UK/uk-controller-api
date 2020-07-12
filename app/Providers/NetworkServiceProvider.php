<?php
namespace App\Providers;

use App\Listeners\Network\RecordFirEntry;
use Illuminate\Support\ServiceProvider;

class NetworkServiceProvider extends ServiceProvider
{
    /**
     * Registers the SquawkPressureService with the app.
     */
    public function register()
    {
        $this->app->singleton(RecordFirEntry::class);
    }

    /**
     * Tells the framework what services this provider provides.
     *
     * @return array Array of provided services.
     */
    public function provides()
    {
        return [RecordFirEntry::class];
    }
}
