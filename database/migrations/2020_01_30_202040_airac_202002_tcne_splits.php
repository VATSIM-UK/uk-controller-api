<?php

use App\Models\Controller\ControllerPosition;
use Illuminate\Database\Migrations\Migration;

class Airac202002TcneSplits extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create the positions
        ControllerPosition::create(
            [
                'callsign' => 'LTC_NL_CTR',
                'frequency' => 123.9,
            ]
        );

        ControllerPosition::create(
            [
                'callsign' => 'LTC_NR_CTR',
                'frequency' => 129.72,
            ]
        );

        // TODO: PRENOTE DATA
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Delete the positions
        ControllerPosition::where('callsign', 'LTC_NL_CTR')->firstOrFail()->delete();
        ControllerPosition::where('callsign', 'LTC_NR_CTR')->firstOrFail()->delete();

        // TODO: PRENOTE DATA
    }
}
