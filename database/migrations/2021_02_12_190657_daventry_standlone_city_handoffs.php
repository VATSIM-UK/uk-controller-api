<?php

use App\Services\DependencyService;
use App\Services\HandoffService;
use Illuminate\Database\Migrations\Migration;

class DaventryStandloneCityHandoffs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        HandoffService::insertIntoOrderAfter('EGLC_SID_BPK_CPT_09', 'LON_M_CTR', 'LON_S_CTR');
        HandoffService::setPositionsForHandoffOrder(
            'EGLC_SID_BPK_CPT_27',
            [
                'THAMES_APP',
                'LTC_SE_CTR',
                'LTC_S_CTR',
                'LTC_CTR',
                'LON_D_CTR',
                'LON_S_CTR',
                'LON_SC_CTR',
                'LON_CTR',
                'LTC_NE_CTR',
                'LTC_N_CTR',
                'LTC_E_CTR',
                'LON_C_CTR',
                'LON_M_CTR',
            ]
        );
        DependencyService::touchDependencyByKey('DEPENDENCY_HANDOFF');
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
