<?php

use App\Services\DependencyService;
use Illuminate\Database\Migrations\Migration;

class AddHandoffV2Dependency extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DependencyService::createDependency(
            'DEPENDENCY_HANDOFFS_V2',
            'HandoffController@getHandoffsV2Dependency',
            false,
            'handoffs-v2.json',
            ['handoffs', 'handoff_orders']
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DependencyService::deleteDependency('DEPENDENCY_HANDOFFS_V2');
    }
}
