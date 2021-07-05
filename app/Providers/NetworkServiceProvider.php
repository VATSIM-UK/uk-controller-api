<?php

namespace App\Providers;

use App\Jobs\Hold\UnassignHoldOnDisconnect;
use App\Jobs\Network\AircraftDisconnected;
use App\Jobs\Network\DeleteNetworkAircraft;
use App\Jobs\Prenote\CancelOutstandingPrenoteMessages;
use App\Jobs\Release\Departure\CancelOutstandingDepartureReleaseRequests;
use App\Jobs\Squawk\MarkAssignmentDeletedOnDisconnect;
use App\Jobs\Stand\TriggerUnassignmentOnDisconnect;
use App\Models\FlightInformationRegion\FlightInformationRegion;
use App\Services\NetworkDataService;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class NetworkServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Registers the SquawkPressureService with the app.
     */
    public function register()
    {
        $this->app->singleton(NetworkDataService::class, function () {
            return new NetworkDataService(
                FlightInformationRegion::with('proximityMeasuringPoints')
                    ->get()
                    ->pluck('proximityMeasuringPoints')
                    ->flatten()
                    ->pluck('latLong')
            );
        });
        $this->app->bindMethod(
            [AircraftDisconnected::class, 'handle'],
            function (AircraftDisconnected $job, Application $application) {
                $job->handle(
                    collect([
                        $application->make(UnassignHoldOnDisconnect::class),
                        $application->make(MarkAssignmentDeletedOnDisconnect::class),
                        $application->make(TriggerUnassignmentOnDisconnect::class),
                        $application->make(CancelOutstandingDepartureReleaseRequests::class),
                        $application->make(CancelOutstandingPrenoteMessages::class),
                        $application->make(DeleteNetworkAircraft::class),
                    ])
                );
            }
        );
    }

    public function provides()
    {
        return [NetworkDataService::class];
    }
}
