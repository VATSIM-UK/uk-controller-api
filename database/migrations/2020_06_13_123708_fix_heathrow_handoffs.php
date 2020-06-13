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
        HandoffService::setHandoffForSid('EGLL', 'GOGSI2G', 'EGLL_SID_SOUTH_WEST');
        HandoffService::setHandoffForSid('EGLL', 'GOGSI2F', 'EGLL_SID_SOUTH_WEST');
        HandoffService::setHandoffForSid('EGLL', 'GASGU2J', 'EGLL_SID_SOUTH_WEST');
        HandoffService::setHandoffForSid('EGLL', 'GASGU2K', 'EGLL_SID_SOUTH_WEST');
        DependencyService::touchDependencyByKey('DEPENDENCY_SID_HANDOFF');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Nothing to do here
    }
}
