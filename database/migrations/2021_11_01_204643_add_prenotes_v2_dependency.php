<?php

use App\Services\DependencyService;
use Illuminate\Database\Migrations\Migration;

class AddPrenotesV2Dependency extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DependencyService::createDependency(
            'DEPENDENCY_PRENOTES_V2',
            'PrenoteController@getPrenotesV2Dependency',
            false,
            'prenotes-v2.json',
            ['prenotes', 'prenote_orders']
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DependencyService::deleteDependency('DEPENDENCY_PRENOTES_V2');
    }
}
