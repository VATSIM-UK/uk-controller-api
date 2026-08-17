<?php

use App\Services\AircraftService;
use App\Services\DependencyService;
use Illuminate\Database\Migrations\Migration;

class AddAircraftDependency extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DependencyService::createDependency(
            'DEPENDENCY_AIRCRAFT',
            sprintf('%s@getAircraftDependency', AircraftService::class),
            false,
            'aircraft.json',
            ['aircraft', 'aircraft_wake_category']
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DependencyService::deleteDependency('DEPENDENCY_AIRCRAFT');
    }
}
