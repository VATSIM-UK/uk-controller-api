<?php

use App\Services\DependencyService;
use App\Services\HandoffService;
use Illuminate\Database\Migrations\Migration;

class FixSouthendHandoffs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        HandoffService::removeFromHandoffOrder('EGMC_PDR_CLACTON', 'EGMC_APP');
        HandoffService::removeFromHandoffOrder('EGMC_PDR_EVNAS', 'EGMC_APP');
        DependencyService::touchDependencyByKey('DEPENDENCY_HANDOFF');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        HandoffService::insertIntoOrderBefore('EGMC_PDR_CLACTON', 'EGMC_APP', 'THAMES_APP');
        HandoffService::insertIntoOrderBefore('EGMC_PDR_EVNAS', 'EGMC_APP', 'THAMES_APP');
        DependencyService::touchDependencyByKey('DEPENDENCY_HANDOFF');
    }
}
