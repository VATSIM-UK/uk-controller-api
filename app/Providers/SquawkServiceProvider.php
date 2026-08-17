<?php

namespace App\Providers;

use App\Allocator\Squawk\General\AirfieldPairingSquawkAllocator;
use App\Allocator\Squawk\General\CcamsSquawkAllocator;
use App\Allocator\Squawk\General\OrcamSquawkAllocator;
use App\Allocator\Squawk\Local\UnitDiscreteSquawkAllocator;
use App\Jobs\Squawk\MarkAssignmentDeletedOnDisconnect;
use App\Services\SquawkService;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class SquawkServiceProvider extends ServiceProvider implements DeferrableProvider
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

        $this->app->singleton(MarkAssignmentDeletedOnDisconnect::class, function (Application $app) {
            return new MarkAssignmentDeletedOnDisconnect(
                $app->make(SquawkService::class)
            );
        });
    }

    public function provides()
    {
        return [SquawkService::class, MarkAssignmentDeletedOnDisconnect::class];
    }
}
