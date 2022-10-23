<?php

namespace App\Providers;

use App\Allocator\Stand\AirlineCallsignSlugArrivalStandAllocator;
use App\Allocator\Stand\CargoFlightPreferredArrivalStandAllocator;
use App\Allocator\Stand\CargoFlightArrivalStandAllocator;
use App\Allocator\Stand\CidReservedArrivalStandAllocator;
use App\Services\Stand\AirfieldStandService;
use App\Services\Stand\StandAdminService;
use App\Services\Stand\StandAssignmentsService;
use App\Services\Stand\StandService;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use App\Imports\Stand\StandReservationsImport;
use App\Allocator\Stand\CargoAirlineFallbackStandAllocator;
use App\Allocator\Stand\AirlineArrivalStandAllocator;
use App\Allocator\Stand\FallbackArrivalStandAllocator;
use App\Allocator\Stand\CallsignFlightplanReservedArrivalStandAllocator;
use App\Allocator\Stand\DomesticInternationalStandAllocator;
use App\Allocator\Stand\AirlineTerminalArrivalStandAllocator;
use App\Allocator\Stand\AirlineDestinationArrivalStandAllocator;

class StandServiceProvider extends ServiceProvider
{
    /**
     * Registers the StandService with the app as a singleton
     */
    public function register()
    {
        $this->app->singleton(StandService::class, function (Application $application) {
            return new StandService(
                [
                    $application->make(CidReservedArrivalStandAllocator::class),
                    $application->make(CallsignFlightplanReservedArrivalStandAllocator::class),
                    $application->make(CargoFlightPreferredArrivalStandAllocator::class),
                    $application->make(CargoFlightArrivalStandAllocator::class),
                    $application->make(AirlineCallsignSlugArrivalStandAllocator::class),
                    $application->make(AirlineDestinationArrivalStandAllocator::class),
                    $application->make(AirlineArrivalStandAllocator::class),
                    $application->make(AirlineTerminalArrivalStandAllocator::class),
                    $application->make(CargoAirlineFallbackStandAllocator::class),
                    $application->make(DomesticInternationalStandAllocator::class),
                    $application->make(FallbackArrivalStandAllocator::class),
                ],
                $application->make(StandAssignmentsService::class),
                $application->make(AirfieldStandService::class)
            );
        });

        $this->app->singleton(StandReservationsImport::class);
        $this->app->singleton(StandAdminService::class);
        $this->app->singleton(AirfieldStandService::class);
    }
}
