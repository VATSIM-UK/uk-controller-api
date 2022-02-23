<?php

use App\Services\AirfieldService;
use App\Services\HandoffService;
use Illuminate\Database\Migrations\Migration;

class StanstedEnrouteTopDown extends Migration
{


    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->removeDaventryFromTopDowns();
        $this->removeDaventryFromHandoffs();
        $this->addNewTopDownPositions();
        $this->updateDefaultHandoffOrders();
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

    private function removeDaventryFromTopDowns()
    {
        AirfieldService::removeFromTopDownsOrder('EGSS', 'LTC_NW_CTR');
        AirfieldService::removeFromTopDownsOrder('EGSS', 'LTC_M_CTR');
        AirfieldService::removeFromTopDownsOrder('EGSS', 'LON_M_CTR');
        AirfieldService::removeFromTopDownsOrder('EGSS', 'LON_CE_CTR');
        AirfieldService::removeFromTopDownsOrder('EGSS', 'LON_CL_CTR');
        AirfieldService::removeFromTopDownsOrder('EGSC', 'LTC_NW_CTR');
        AirfieldService::removeFromTopDownsOrder('EGSC', 'LTC_M_CTR');
        AirfieldService::removeFromTopDownsOrder('EGSC', 'LON_M_CTR');
        AirfieldService::removeFromTopDownsOrder('EGSC', 'LON_CE_CTR');
        AirfieldService::removeFromTopDownsOrder('EGSC', 'LON_CL_CTR');
    }

    private function removeDaventryFromHandoffs()
    {
        HandoffService::removeFromHandoffOrder('AIRFIELD_EGSS_DEFAULT_HANDOFF', 'LTC_NW_CTR');
        HandoffService::removeFromHandoffOrder('AIRFIELD_EGSS_DEFAULT_HANDOFF', 'LON_M_CTR');
        HandoffService::removeFromHandoffOrder('AIRFIELD_EGSS_DEFAULT_HANDOFF', 'LON_CE_CTR');
        HandoffService::removeFromHandoffOrder('AIRFIELD_EGSS_DEFAULT_HANDOFF', 'LON_CL_CTR');
        HandoffService::removeFromHandoffOrder('AIRFIELD_EGSS_DEFAULT_HANDOFF', 'LTC_M_CTR');
        HandoffService::removeFromHandoffOrder('AIRFIELD_EGSC_DEFAULT_HANDOFF', 'LTC_NW_CTR');
        HandoffService::removeFromHandoffOrder('AIRFIELD_EGSC_DEFAULT_HANDOFF', 'LON_M_CTR');
        HandoffService::removeFromHandoffOrder('AIRFIELD_EGSC_DEFAULT_HANDOFF', 'LTC_M_CTR');
        HandoffService::removeFromHandoffOrder('AIRFIELD_EGSC_DEFAULT_HANDOFF', 'LON_CE_CTR');
        HandoffService::removeFromHandoffOrder('AIRFIELD_EGSC_DEFAULT_HANDOFF', 'LON_CL_CTR');
    }

    private function addNewTopDownPositions()
    {
        AirfieldService::insertIntoOrderBefore('EGSS', 'LON_E_CTR', 'LON_C_CTR');
        AirfieldService::insertIntoOrderBefore('EGSS', 'LTC_E_CTR', 'LON_E_CTR');
        AirfieldService::insertIntoOrderBefore('EGSS', 'LTC_NE_CTR', 'LTC_N_CTR');

        AirfieldService::insertIntoOrderBefore('EGSC', 'LON_E_CTR', 'LON_C_CTR');
        AirfieldService::insertIntoOrderBefore('EGSC', 'LTC_E_CTR', 'LON_E_CTR');
        AirfieldService::insertIntoOrderBefore('EGSC', 'LTC_NE_CTR', 'LTC_N_CTR');
    }

    private function updateDefaultHandoffOrders()
    {
        HandoffService::insertIntoOrderBefore('AIRFIELD_EGSS_DEFAULT_HANDOFF', 'LON_E_CTR', 'LON_C_CTR');
        HandoffService::insertIntoOrderBefore('AIRFIELD_EGSS_DEFAULT_HANDOFF', 'LTC_E_CTR', 'LON_E_CTR');
        HandoffService::insertIntoOrderBefore('AIRFIELD_EGSS_DEFAULT_HANDOFF', 'LTC_NE_CTR', 'LTC_N_CTR');

        HandoffService::insertIntoOrderBefore('AIRFIELD_EGSC_DEFAULT_HANDOFF', 'LON_E_CTR', 'LON_C_CTR');
        HandoffService::insertIntoOrderBefore('AIRFIELD_EGSC_DEFAULT_HANDOFF', 'LTC_E_CTR', 'LON_E_CTR');
        HandoffService::insertIntoOrderBefore('AIRFIELD_EGSC_DEFAULT_HANDOFF', 'LTC_NE_CTR', 'LTC_N_CTR');
    }
}
