<?php
namespace App\Providers;

use App\Allocator\Squawk\General\AirfieldPairingSquawkAllocator;
use App\Allocator\Squawk\General\CcamsSquawkAllocator;
use App\Allocator\Squawk\General\OrcamSquawkAllocator;
use App\Allocator\Squawk\Local\UnitDiscreteSquawkAllocator;
use App\Listeners\Squawk\ReserveSquawkIfInFirProximity;
use App\Services\SectorfileService;
use App\Services\SquawkService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\Application;

class SquawkServiceProvider extends ServiceProvider
{
    /**
     * Registers the SquawkPressureService with the app.
     */
    public function register()
    {
        $this->app->bind(SquawkService::class, function (Application $app) {

            // Add the squawk allocation rules, in the order of preference
            return new SquawkService(
                [
                    $app->make(AirfieldPairingSquawkAllocator::class),
                    $app->make(OrcamSquawkAllocator::class),
                    $app->make(CcamsSquawkAllocator::class),
                ],
                [
                    $app->make(UnitDiscreteSquawkAllocator::class),
                ]
            );
        });

        $this->app->singleton(ReserveSquawkIfInFirProximity::class, function (Application $app) {
            return new ReserveSquawkIfInFirProximity(
                $app->make(SquawkService::class),
                [
                    SectorfileService::coordinateFromSectorfile('N053.35.13.000', 'W001.18.03.000'), // EGTT - UPTON
                    SectorfileService::coordinateFromSectorfile('N052.08.31.000', 'W002.03.38.000'), // EGTT - LUXTO
                    SectorfileService::coordinateFromSectorfile('N050.40.30.000', 'W001.51.00.000'), // EGTT - KAPEX
                    SectorfileService::coordinateFromSectorfile('N058.58.06.000', 'W003.52.22.000'), // EGPX - SOXON
                    SectorfileService::coordinateFromSectorfile('N055.47.58.000',  'W005.20.00.000'), // EGPX - TABIT
                    SectorfileService::coordinateFromSectorfile('N055.27.54.000', 'E000.14.53.000'), // EGPX - GIVEM

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
            SquawkService::class,
            ReserveSquawkIfInFirProximity::class,
        ];
    }
}
