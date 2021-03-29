<?php
namespace App\Providers;

use App\Allocator\Squawk\General\AirfieldPairingSquawkAllocator;
use App\Allocator\Squawk\General\CcamsSquawkAllocator;
use App\Allocator\Squawk\General\OrcamSquawkAllocator;
use App\Allocator\Squawk\Local\UnitDiscreteSquawkAllocator;
use App\Jobs\Squawk\MarkAssignmentDeletedOnDisconnect;
use App\Listeners\Squawk\ReclaimIfLeftFirProximity;
use App\Listeners\Squawk\ReserveInFirProximity;
use App\Models\FlightInformationRegion\FlightInformationRegion;
use App\Models\Squawk\SquawkReservationMeasurementPoint;
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

        $this->app->singleton(ReserveInFirProximity::class, function (Application $app) {
            return new ReserveInFirProximity(
                $app->make(SquawkService::class),
                FlightInformationRegion::with('proximityMeasuringPoints')
                    ->get()
                    ->pluck('proximityMeasuringPoints')
                    ->flatten()
                    ->pluck('latLong')
                    ->toArray()
            );
        });

        $this->app->singleton(ReclaimIfLeftFirProximity::class, function (Application $app) {
            return new ReclaimIfLeftFirProximity(
                $app->make(SquawkService::class),
                FlightInformationRegion::with('proximityMeasuringPoints')
                    ->get()
                    ->pluck('proximityMeasuringPoints')
                    ->flatten()
                    ->pluck('latLong')
                    ->toArray()
            );
        });

        $this->app->singleton(MarkAssignmentDeletedOnDisconnect::class, function (Application $app) {
            return new MarkAssignmentDeletedOnDisconnect(
                $app->make(SquawkService::class)
            );
        });
    }
}
