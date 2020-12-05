<?php

use App\Services\DependencyService;
use App\Services\HandoffService;
use Illuminate\Database\Migrations\Migration;

class FixManchesterHandoffOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        HandoffService::removeFromHandoffOrder('EGCC_SID_SOUTH' , 'EGCC_N_APP');
        HandoffService::insertIntoOrderAfter('EGCC_SID_SOUTH', 'EGCC_S_APP', 'LON_CTR');
        DependencyService::touchDependencyByKey('DEPENDENCY_HANDOFF');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
