<?php

use App\Services\DependencyService;
use Illuminate\Database\Migrations\Migration;

class AddFlightRulesDependency extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DependencyService::createDependency(
            'DEPENDENCY_FLIGHT_RULES',
            'FlightRulesController@getFlightRulesDependency',
            false,
            'flight-rules.json',
            ['flight-rules']
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DependencyService::deleteDependency('DEPENDENCY_FLIGHT_RULES');
    }
}
