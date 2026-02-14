<?php

namespace App\Providers;

use App\Allocator\Stand\AirlineAircraftArrivalStandAllocator;
use App\Allocator\Stand\AirlineAircraftTerminalArrivalStandAllocator;
use App\Allocator\Stand\AirlineCallsignArrivalStandAllocator;
use App\Allocator\Stand\AirlineCallsignSlugArrivalStandAllocator;
use App\Allocator\Stand\AirlineCallsignSlugTerminalArrivalStandAllocator;
use App\Allocator\Stand\AirlineCallsignTerminalArrivalStandAllocator;
use App\Allocator\Stand\AirlineDestinationTerminalArrivalStandAllocator;
use App\Allocator\Stand\BusinessAviationFlightArrivalStandAllocator;
use App\Allocator\Stand\CargoFlightPreferredArrivalStandAllocator;
use App\Allocator\Stand\CargoFlightArrivalStandAllocator;
use App\Allocator\Stand\CidReservedArrivalStandAllocator;
use App\Allocator\Stand\UserRequestedArrivalStandAllocator;
use App\Services\Stand\AirfieldStandService;
use App\Services\Stand\ArrivalAllocationService;
use App\Services\Stand\RecordsAssignmentHistory;
use App\Services\Stand\StandAssignmentsHistoryService;
use App\Services\Stand\StandAssignmentsService;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use App\Imports\Stand\StandReservationsImport;
use App\Allocator\Stand\CargoAirlineFallbackStandAllocator;
use App\Allocator\Stand\AirlineGeneralArrivalStandAllocator;
use App\Allocator\Stand\FallbackArrivalStandAllocator;
use App\Allocator\Stand\CallsignFlightplanReservedArrivalStandAllocator;
use App\Allocator\Stand\DomesticInternationalStandAllocator;
use App\Allocator\Stand\AirlineGeneralTerminalArrivalStandAllocator;
use App\Allocator\Stand\AirlineDestinationArrivalStandAllocator;
use App\Allocator\Stand\OriginAirfieldStandAllocator;

class StandServiceProvider extends ServiceProvider
{
    /**
     * Registers the StandService with the app as a singleton
     */
    public function register()
    {
        $this->app->singleton(ArrivalAllocationService::class, function (Application $application) {
            return new ArrivalAllocationService(
                $application->make(StandAssignmentsService::class),
                [
                    $application->make(CidReservedArrivalStandAllocator::class),
                    $application->make(UserRequestedArrivalStandAllocator::class),
                    $application->make(CallsignFlightplanReservedArrivalStandAllocator::class),
                    $application->make(CargoFlightPreferredArrivalStandAllocator::class),
                    $application->make(CargoFlightArrivalStandAllocator::class),
                    $application->make(BusinessAviationFlightArrivalStandAllocator::class),
                    $application->make(AirlineCallsignArrivalStandAllocator::class),
                    $application->make(AirlineCallsignSlugArrivalStandAllocator::class),
                    $application->make(AirlineAircraftArrivalStandAllocator::class),
                    $application->make(AirlineDestinationArrivalStandAllocator::class),
                    $application->make(AirlineGeneralArrivalStandAllocator::class),
                    $application->make(AirlineCallsignTerminalArrivalStandAllocator::class),
                    $application->make(AirlineCallsignSlugTerminalArrivalStandAllocator::class),
                    $application->make(AirlineAircraftTerminalArrivalStandAllocator::class),
                    $application->make(AirlineDestinationTerminalArrivalStandAllocator::class),
                    $application->make(AirlineGeneralTerminalArrivalStandAllocator::class),
                    $application->make(CargoAirlineFallbackStandAllocator::class),
                    $application->make(OriginAirfieldStandAllocator::class),
                    $application->make(DomesticInternationalStandAllocator::class),
                    $application->make(FallbackArrivalStandAllocator::class),
                ]
            );
        });
        $this->app->singleton(StandReservationsImport::class);
        $this->app->singleton(AirfieldStandService::class);
        $this->app->singleton(
            RecordsAssignmentHistory::class,
            fn (Application $application) => $application->make(StandAssignmentsHistoryService::class)
        );
    }
}
