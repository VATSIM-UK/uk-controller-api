<?php

use App\Services\AirfieldService;
use App\Services\DependencyService;
use Illuminate\Database\Migrations\Migration;

class FixManchesterTopDown extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        AirfieldService::removeFromTopDownsOrder('EGCC', 'MAN_WL_CTR');
        AirfieldService::removeFromTopDownsOrder('EGCC', 'MAN_W_CTR');
        AirfieldService::insertIntoOrderBefore('EGCC', 'MAN_E_CTR', 'MAN_CTR');
        DependencyService::touchDependencyByKey('DEPENDENCY_AIRFIELD_OWNERSHIP');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        AirfieldService::removeFromTopDownsOrder('EGCC', 'MAN_E_CTR');
        AirfieldService::insertIntoOrderBefore('EGCC', 'MAN_W_CTR', 'MAN_CTR');
        AirfieldService::insertIntoOrderBefore('EGCC', 'MAN_WL_CTR', 'MAN_W_CTR');
        DependencyService::touchDependencyByKey('DEPENDENCY_AIRFIELD_OWNERSHIP');
    }
}
