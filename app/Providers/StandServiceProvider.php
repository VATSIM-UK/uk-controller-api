<?php
namespace App\Providers;

use App\Allocator\Stand\AirlineArrivalStandAllocator;
use App\Allocator\Stand\AirlineDestinationArrivalStandAllocator;
use App\Allocator\Stand\CargoArrivalStandAllocator;
use App\Allocator\Stand\DomesticInternationalStandAllocator;
use App\Allocator\Stand\SizeAppropriateArrivalStandAllocator;
use App\Services\StandService;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

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
                    $application->make(AirlineDestinationArrivalStandAllocator::class),
                    $application->make(AirlineArrivalStandAllocator::class),
                    $application->make(CargoArrivalStandAllocator::class),
                    $application->make(DomesticInternationalStandAllocator::class),
                    $application->make(SizeAppropriateArrivalStandAllocator::class),
                ]
            );
        });
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
