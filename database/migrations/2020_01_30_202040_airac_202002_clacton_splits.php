<?php

use App\Models\Controller\ControllerPosition;
use App\Models\Controller\Handoff;
use Illuminate\Database\Migrations\Migration;

class Airac202002ClactonSplits extends Migration
{
    const HANDOFF_KEYS = [
        'EGLL_SID_NORTH_EAST',
        'EGLC_SID_CLN',
        'EGSS_SID_EAST_SOUTH',
        'EGGW_SID_SOUTH_EAST',
        'EGLC_SID_BPK_CPT_09',
        'EGLC_SID_BPK_CPT_27',
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add the positions
        $north = ControllerPosition::create(
            [
                'callsign' => 'LON_EN_CTR',
                'frequency' => 133.95
            ]
        );

        $south = ControllerPosition::create(
            [
                'callsign' => 'LON_ES_CTR',
                'frequency' => 133.95
            ]
        );

        // Handoff orders
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Delete the positions
        ControllerPosition::where('callsign', 'LON_EN_CTR')->firstOrFail()->delete();
        ControllerPosition::where('callsign', 'LON_ES_CTR')->firstOrFail()->delete();
    }

    private function updateHandoffOrdersUp()
    {
        foreach (self::HANDOFF_KEYS as $key) {
            // Find out the orders that have TC east. (DO IT BY QUERY)
            // Bump up TC East and above
            // Insert TC East Redfa in the gap

            // Find all orders that have AC Clacton
            // Bump up Clacton and above
            // Insert AC Clacton North in the gap.
        }
    }

    private function updateHandoffOrdersDown()
    {
        foreach (self::HANDOFF_KEYS as $key) {
            // Opposite of the above
        }
    }
}
