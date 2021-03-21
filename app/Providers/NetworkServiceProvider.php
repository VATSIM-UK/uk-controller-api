<?php
namespace App\Providers;

use App\Listeners\Network\RecordFirEntry;
use App\Models\FlightInformationRegion\FlightInformationRegion;
use App\Services\NetworkDataService;
use Illuminate\Support\ServiceProvider;

class NetworkServiceProvider extends ServiceProvider
{
    /**
     * Registers the SquawkPressureService with the app.
     */
    public function register()
    {
        $this->app->singleton(RecordFirEntry::class);
        $this->app->singleton(NetworkDataService::class, function () {
            return new NetworkDataService(
                FlightInformationRegion::with('proximityMeasuringPoints')
                    ->get()
                    ->pluck('proximityMeasuringPoints')
                    ->flatten()
                    ->pluck('latLong')
            );
        });
    }
}
